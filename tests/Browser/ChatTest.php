<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class ChatTest extends DuskTestCase
{
    protected $user;
    protected $user2;
    protected $user3;

    public function testBasicInput()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;

        // Chat basic test
        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->loginAs($user)
                    ->visit('/user/chat')
                    ->type('@chattext', 'Begin chat test message')
                    ->click('@sendMessage')
                    ->pause (100)
                    ->assertSee('Begin chat test message')
                    ->attach('@chatFile', base_path('tests/data/profile.jpg'))
                    ->attach('@chatFile', base_path('tests/data/video.mp4'))
                    ->type('@chattext', 'Testing message with photo and video')
                    ->pause (3000)
                    ->click('@sendMessage')
                    ->pause (100)
                    ->assertSee('video.mp4')
                    ->assertSee('profile.jpg')
                    ->pause (500)
                    ->screenshot('chat-basic')
                    ->logout();
        });


        // Checking if there are messages in database
        $messages = $this->user->messages()->latest()->paginate(20)->getCollection();
        $this->assertNotEmpty( $messages, 'No messages created in database');
        $i = 0;
        $j = 0;
        foreach ($messages as $message) {
          $i++;
          if ($message->files) {
            $files = json_decode ($message->files);
            foreach ($files as $file) {
               $filename = storage_path('app/' . $file->url);
               $this->assertEquals($file->size, filesize($filename), 'filesize differs from database size');
               $j++;
            }
          }
        }
        $this->assertEquals (2, $i, 'Not all messages loaded' );
        $this->assertEquals (2, $j, 'diffrent count of files loaded' );

        // Delete user and messages
        foreach ($messages as $message) {
          if ($message->files) {
            $files = json_decode ($message->files);
            foreach ($files as $file) {
               $filename = storage_path('app/' . $file->url);
               unlink ($filename);
            }
          }
        }
        $this->user->delete();
    }
    public function testVueChatForm()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;

        // Chacking Vue response
        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->loginAs($user)
                    ->visit('/user/chat')
                    ->pause (100)
                    ->assertSourceHas('Введите сообщение')
                    // Change to english language and check messages
                    ->click('@lang-button')
                    ->clickLink('English')
                    ->pause (100)
                    ->assertSourceHas('Enter message')
                    // check Attaching/removing files and tooltip
                    ->attach('@chatFile', base_path('tests/data/profile.jpg'))
                    ->attach('@chatFile', base_path('tests/data/video.mp4'))
                    ->pause (3000)
                    ->assertSee('Delete (2)')
                    ->mouseover('@removefiles')
                    ->assertSee('profile.jpg')
                    ->assertSee('video.mp4')
                    ->screenshot('chat-vuetest')
                    ->click('@removefiles')
                    ->pause (500)
                    ->assertDontSee('Delete')
                    ->logout();
        });
        $this->user->delete();
    }

/*
    public function testMultiUserChat()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;
        $this->user2 = factory('App\User')->create();
        $user2 = $this->user2;
        $this->user3 = factory('App\User')->create();
        $user3 = $this->user3;

        $this->browse(function ($first, $second, $third) use ($user, $user2, $user3) {
            $first->logout()
                  ->loginAs($user)
                  ->visit('/user/chat')
                  ->pause (100);
           $second->loginAs($user2)
                  ->visit('/user/chat')
                  ->pause (100);
           $third->loginAs($user3)
                  ->visit('/user/chat')
                  ->pause (100);

            $first->type('@chattext', 'We are begging to test multiuser chat')
                  ->click('@sendMessage')
                  ->pause (100)
                  ->type('@chattext', 'This is my test message to everyone')
                  ->click('@sendMessage')
                  ->pause (1000);

           $second->assertSee('This is my test message to everyone')
                  ->type( '@chattext', 'Hey '. $user->name)
                  ->click('@sendMessage')
                  ->pause (100)
                  ->type( '@chattext', 'How are you')
                  ->click('@sendMessage')
                  ->pause (1000);

            $first->assertSee('How are you')
                  ->type( '@chattext', 'Hi '. $user->name)
                  ->click('@sendMessage')
                  ->pause (100)
                  ->type( '@chattext', 'It seems chat is working ok')
                  ->click('@sendMessage')
                  ->pause (1000);


            $third->assertSee('It seems chat is working ok')
                  ->type('@chattext', 'Yes I see all messages')
                  ->click('@sendMessage')
                  ->pause (1000);

            $first->assertSee('Yes I see all messages')
                  ->screenshot('chat-user1');

           $second->assertSee('Yes I see all messages')
                  ->screenshot('chat-user2');

            $third->screenshot('chat-user3');
        });

        $this->user->delete();
        $this->user2->delete();
        $this->user3->delete();
    }
*/
}
