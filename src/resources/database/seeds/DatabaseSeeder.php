<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	$email = 'admin@laragen.com';
    	$pass = Hash::make('password');

	    $user = User::where('email', '=', $email)->first();
	    if ($user === null) {
	        $user = new App\User();
	        $user->password = $pass;
	        $user->email = $email;
	        $user->email_verified_at = now();
	        $user->remember_token = Str::random(10);
	        $user->name = 'Admin User';
	        $user->save();
	    }
    }
}
