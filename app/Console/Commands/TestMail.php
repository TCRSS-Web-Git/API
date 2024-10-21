<?php

namespace App\Console\Commands;

use App\Mail\TestAttach;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $req['resume'] = '8eznov0EjwYImi33/3-2-1 Product Specification.pdf';
        $req['certs'] = ['k0SuP0eRLACMIfai/3-2-2 Applications.pdf', 'Z43R141xMaVQDP3M/3-2-3 By Product.pdf'];
        Mail::to('test@test.com')->send(new TestAttach($req['resume'], $req['certs']));
    }
}
