<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PublicTest extends DuskTestCase
{
    protected $user;

    public function testPublicPages()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/')
                    ->assertPathIs('/')
                    ->assertDontSee('404')
                    ->visit('/video')
                    ->assertPathIs('/video')
                    ->assertDontSee('404')
                    ->visit('/documentation')
                    ->assertPathIs('/documentation')
                    ->assertDontSee('404')
                    ->visit('/contact')
                    ->assertPathIs('/contact')
                    ->assertDontSee('404')

                    ->visit('/login')
                    ->assertPathIs('/login')
                    ->assertDontSee('404')
                    ->visit('/password/reset')
                    ->assertPathIs('/password/reset')
                    ->assertDontSee('404')

                    ->visit('/initstore')
                    ->assertPathIs('/initstore')

                    ->visit('/videos')
                    ->assertPathIs('/videos')
                    ->assertSee('404');

        });
    }

    public function testPrivatePages()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/home')
                    ->assertPathIs('/login')
                    ->visit('/email/verify')
                    ->assertPathIs('/login')
                    ->visit('/password/confirm')
                    ->assertPathIs('/login')
                    ->visit('/profile')
                    ->assertPathIs('/login')
                    ->visit('/user/chat')
                    ->assertPathIs('/login')
                    ->visit('/user/chat/messages')
                    ->assertPathIs('/login')
                    ->visit('/user/photos')
                    ->assertPathIs('/login')
                    ->visit('/user/docs')
                    ->assertPathIs('/login')
                    ->visit('/user/videos')
                    ->assertPathIs('/login')
                    ->visit('/user/upload')
                    ->assertPathIs('/login');
        });
    }
}
