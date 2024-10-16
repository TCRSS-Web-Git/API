<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateSignature;
use App\Mail\UserInvitation;
use App\Models\Invite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_user_list(): void
    {
        $this->signInAdmin();

        [$userA, $userB] = User::factory()->count(2)->admin()->create();

        // act
        $response = $this->getJson(route('users.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $userA->hashid]);
        $response->assertJsonFragment(['id' => $userB->hashid]);

        $response->assertStatus(200);
    }

    public function test_admin_can_get_user_with_filter(): void
    {
        $this->signInAdmin();

        $userA = User::factory()->admin()->create(['first_name' => 'Test First Name']);
        $userB = User::factory()->admin()->create(['first_name' => 'John']);
        $userC = User::factory()->admin()->create(['last_name' => 'Test Last Name']);
        $userD = User::factory()->admin()->create(['email' => 'Test@email.com']);
        $userE = User::factory()->admin()->create(['email' => 'John@email.com']);
        $userF = User::factory()->admin()->create(['phone' => '0912345675']);

        // act
        $response = $this->getJson(route('users.index', ['search' => 'Test']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $userA->hashid]);
        $response->assertJsonFragment(['id' => $userC->hashid]);
        $response->assertJsonFragment(['id' => $userD->hashid]);

        $response->assertJsonMissing(['id' => $userB->hashid]);
        $response->assertJsonMissing(['id' => $userE->hashid]);

        $response = $this->getJson(route('users.index', ['search' => '091-234-5675']));
        $response->assertJsonFragment(['id' => $userF->hashid]);

        $response = $this->getJson(route('users.index', ['search' => $userE->hashid]));
        $response->assertJsonFragment(['id' => $userE->hashid]);
    }

    public function test_admin_can_get_user_with_filter_role_id(): void
    {
        $this->signInAdmin();

        $adminRole = Role::where('name', Role::ROLE_ADMIN)->firstOrCreate();
        $superAdminRole = Role::where('name', Role::ROLE_SUPER_ADMIN)->firstOrCreate();

        $userA = User::factory()->admin()->create(['first_name' => 'Test First Name']);
        $userB = User::factory()->admin()->create(['first_name' => 'John']);
        $userC = User::factory()->admin()->create(['last_name' => 'Test Last Name']);
        $userD = User::factory()->superAdmin()->create(['email' => 'Test@email.com']);
        $userE = User::factory()->superAdmin()->create(['email' => 'John@email.com']);
        $userF = User::factory()->superAdmin()->create(['phone' => '0912345675']);

        // act
        $response = $this->getJson(route('users.index', ['role_id' => $adminRole->hashid]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $userA->hashid]);
        $response->assertJsonFragment(['id' => $userB->hashid]);
        $response->assertJsonFragment(['id' => $userC->hashid]);

        $response->assertJsonMissing(['id' => $userD->hashid]);
        $response->assertJsonMissing(['id' => $userE->hashid]);
        $response->assertJsonMissing(['id' => $userF->hashid]);

        // act
        $response = $this->getJson(route('users.index', ['role_id' => $superAdminRole->hashid]));
        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $userD->hashid]);
        $response->assertJsonFragment(['id' => $userE->hashid]);
        $response->assertJsonFragment(['id' => $userF->hashid]);

        $response->assertJsonMissing(['id' => $userA->hashid]);
        $response->assertJsonMissing(['id' => $userB->hashid]);
        $response->assertJsonMissing(['id' => $userC->hashid]);
    }

    public function test_admin_get_user(): void
    {
        $this->signInAdmin();

        $userA = User::factory()->admin()->create(['first_name' => 'Test First Name']);

        $response = $this->getJson(route('users.show', $userA));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $userA->hashid]);
    }

    public function test_admin_get_user_me(): void
    {
        $userA = $this->signInAdmin();

        $response = $this->getJson(route('user.me'));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $userA->hashid]);
    }

    public function test_super_admin_can_create_user(): void
    {
        $this->signInSuperAdmin();
        $mockUser = User::factory()->make();

        Mail::fake();

        $role = Role::where('name', Role::ROLE_ADMIN)->first();
        $response = $this->postJson(route('users.store', [
            'role_id' => $role->hashid,
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => $mockUser->email,
            'phone' => $mockUser->phone,
        ]));

        $response->assertCreated();
        $this->assertDatabaseCount('users', 2); // signIn 1 + create 1
        $this->assertDatabaseHas('users', [
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => $mockUser->email,
            'phone' => $mockUser->phone,
        ]);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => User::decodeHash($response->json('data.id')),
        ]);

        $this->assertDatabaseHas('invites', [
            'email' => $mockUser->email,
        ]);

        Mail::assertSent(UserInvitation::class, static function ($mail) use ($mockUser) {
            return $mail->hasTo($mockUser->email);
        });
    }

    public function test_admin_can_not_create_user(): void
    {
        $this->signInAdmin();
        $mockUser = User::factory()->make();

        $role = Role::where('name', Role::ROLE_ADMIN)->first();
        $response = $this->postJson(route('users.store', [
            'role_id' => $role->hashid,
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => $mockUser->email,
            'phone' => $mockUser->phone,
        ]));

        $response->assertForbidden();
        $this->assertDatabaseCount('users', 1); // signIn 1
        $this->assertDatabaseMissing('users', [
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => $mockUser->email,
            'phone' => $mockUser->phone,
        ]);
    }

    public function test_super_admin_can_not_create_user_invalid_email(): void
    {
        $this->signInAdmin();
        $mockUser = User::factory()->make();

        $response = $this->postJson(route('users.store', [
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => 'wrong-email',
            'phone' => $mockUser->phone,
        ]));

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 1); // signIn 1
        $this->assertDatabaseMissing('users', [
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => $mockUser->email,
            'phone' => $mockUser->phone,
        ]);
    }

    public function test_super_admin_can_update_user(): void
    {
        $this->signInSuperAdmin();

        $user = User::factory()->admin()->create();
        $mockUser = User::factory()->make();
        $adminRole = Role::where('name', Role::ROLE_ADMIN)->first();
        $superAdminRole = Role::where('name', Role::ROLE_SUPER_ADMIN)->first();

        $response = $this->putJson(route('users.update', $user), [
            'role_id' => $superAdminRole->hashid,
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'phone' => $mockUser->phone,
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('users', 2); // signIn 1 + factory 1
        $this->assertDatabaseHas('users', [
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'phone' => $mockUser->phone,
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $superAdminRole->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $adminRole->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    public function test_admin_can_not_update_user(): void
    {
        $this->signInAdmin();

        $user = User::factory()->create();
        $mockUser = User::factory()->make();
        $role = Role::where('name', Role::ROLE_ADMIN)->first();

        $response = $this->putJson(route('users.update', $user), [
            'role_id' => $role->hashid,
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'phone' => $mockUser->phone,

        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('users', 2); // signIn 1 + factory 1
        $this->assertDatabaseMissing('users', [
            'title' => 'Mr.',
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'phone' => $mockUser->phone,
        ]);
    }

    public function test_super_admin_can_delete_user(): void
    {
        $this->signInSuperAdmin();

        $user = User::factory()->create();

        $response = $this->deleteJson(route('users.destroy', $user));

        // assert
        $response->assertNoContent();
        $this->assertSoftDeleted($user);
    }

    public function test_invited_user_can_accept_invitation(): void
    {
        /* @var User $user */
        $user = User::factory()->create(['password' => null, 'email_verified_at' => null]);

        /* @var Invite $invite */
        $invite = Invite::factory()->for($user)->create([
            'email' => $user->email,
        ]);

        $response = $this->withoutMiddleware(ValidateSignature::class)
            ->put(route('accept.users.invitation', ['email' => $invite->email, 'token' => $invite->token]), [
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        $response->assertNoContent();

        $user->refresh();
        $this->assertDatabaseCount('invites', 0);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_super_admin_can_resend_user_invitation_email(): void
    {
        $this->signInSuperAdmin();

        /** @var User $user */
        $user = User::factory()->create(['password' => null, 'email_verified_at' => null]);

        /* @var Invite $invite */
        Invite::factory()->for($user)->create([
            'email' => $user->email,
        ]);

        Mail::fake();

        $response = $this->post(route('invite.resend', $user));
        $response->assertNoContent();

        $this->assertDatabaseCount('invites', 1);

        Mail::assertSent(UserInvitation::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
