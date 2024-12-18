<x-mail::message>
    <x-mail::button
        :url="config('app.admin_url').'/user-invitation?api='.$url"
        color="primary"
        align="center"
    >
        Set password
    </x-mail::button>
</x-mail::message>
