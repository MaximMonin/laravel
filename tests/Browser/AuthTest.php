<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use Illuminate\Support\Facades\Hash;

class authenticationTest extends DuskTestCase
{
    protected $user;

    public function testAuthentication()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;

        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->loginAs($user)
                    ->visit('/home')
                    ->assertSee(__('messages.Applications'))
                    ->screenshot('home')
                    ->assertAuthenticatedAs($user)
                    ->logout()
                    ->assertGuest();
        });
        $this->user->delete();
    }

    public function testAllUserLogin()
    {            
        $users = User::where('email_verified_at', '!=', null)->get();
        foreach($users as $user) {
            $this->browse(function ($browser) use ($user) {
                $browser->logout()
                    ->loginAs($user)
                    ->visit('/home')
                    ->assertSee(__('messages.Applications'))
                    ->click('@user-button')
//                    ->screenshot('usermenu'.$user->id)
                    ->click('@logout-button');
            });
        }
    }

    public function testAllNotConfirmedUserLogin()
    {            
        $users = User::where('email_verified_at', '=', null)->get();
        foreach($users as $user) {
            $this->browse(function ($browser) use ($user) {
                $browser->logout()
                    ->loginAs($user)
                    ->visit('/home')
                    ->assertPathIs('/email/verify')
                    ->click('@user-button')
                    ->click('@logout-button');
            });
        }
    }

    public function testLoginLogout()
    {
        $this->user = factory('App\User')->create();
        $this->user->password = Hash::make('testpassword');
        $this->user->save();

        $user = $this->user;

        $this->browse(function (Browser $browser) use ($user) {
           $browser->logout()
                   ->visit('/')
                   ->type('email', $user->email)
                   ->type('password', 'testpassword')
                   ->press(__('Login'))
                   ->assertPathIs('/home')
                   ->screenshot('Loggedin')
                   ->click('@user-button')
                   ->click('@logout-button');
        });
        $this->user->delete();
    }

}
