<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class CommunicationTest extends DuskTestCase
{
    protected $user;
    protected $user2;

    public function testMultiUserChat()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;
        $this->user2 = factory('App\User')->create();
        $user2 = $this->user2;

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user, $user2) {
         $browser1->loginAs($user)
                  ->visit('/user/chat')
                  ->pause (100);
         $browser2->loginAs($user2)
                  ->visit('/user/chat')
                  ->pause (100);

         $browser1->type('@chattext', 'We are begging to test multiuser chat')
                  ->click('@sendMessage')
                  ->pause (100)
                  ->type('@chattext', 'This is my test message to everyone')
                  ->click('@sendMessage')
                  ->pause (1000);

         $browser2->assertSee('This is my test message to everyone')
                  ->type( '@chattext', 'Hey '. $user->name)
                  ->click('@sendMessage')
                  ->pause (100)
                  ->type( '@chattext', 'How are you')
                  ->click('@sendMessage')
                  ->pause (1000);

         $browser1->assertSee('Hey '. $user->name)
                  ->assertSee('How are you')
                  ->type( '@chattext', 'Hi '. $user2->name)
                  ->click('@sendMessage')
                  ->pause (100)
                  ->type( '@chattext', 'It seems chat is working ok')
                  ->click('@sendMessage')
                  ->pause (1000);

         $browser2->assertSee('Hi '. $user2->name)
                  ->assertSee('It seems chat is working ok')
                  ->type('@chattext', 'Yes I see all messages')
                  ->click('@sendMessage')
                  ->pause (1000);

         $browser1->assertSee('Yes I see all messages')
                  ->screenshot('chat-user1')
                  ->logout()
                  ->quit();

         $browser2->assertSee('Yes I see all messages')
                  ->screenshot('chat-user2')
                  ->logout()
                  ->quit();

        });

        $this->user->delete();
        $this->user2->delete();
    }
}
