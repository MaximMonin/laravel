<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class ChatTest extends DuskTestCase
{
    protected $user;

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
}
