<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class authenticationTest extends DuskTestCase
{
    protected $user;

    /** @test */
    public function testAuthentication()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;

        $this->browse(function (Browser $browser) use ($user) {
           $browser->loginAs($user)
                    ->visit('/home')
                    ->assertSee(__('messages.Applications'))
                    ->screenshot('home')
                    ->assertAuthenticatedAs($user)
                    ->logout()
                    ->assertGuest();
        });
        $this->user->delete();
    }
}
