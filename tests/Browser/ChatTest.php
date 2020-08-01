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

        // Dropzone fileupload
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
                    ->screenshot('chat-basic');
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

/*
        // Checking Photos Videos Docs tabs and check if there is link on pages
        $this->browse(function (Browser $browser2) use ($user, $photo, $video, $doc) {
           $browser2->logout()
                    ->loginAs($user)
                    ->visit('/user/upload')
                    ->click('@photo-tab')
                    ->assertSourceHas($photo->file)
                    ->assertSourceHas($photo->filepreview)
                    ->pause (500)
                    ->screenshot('photos')
                    ->click('@video-tab')
                    ->assertSourceHas($video->file)
                    ->pause (500)
                    ->screenshot('videos')
                    ->click('@doc-tab')
                    ->assertSourceHas($doc->file)
                    ->pause (500)
                    ->screenshot('docs');
        });

        // Deleting files and users
        foreach ($files as $file) {
          $filename = storage_path('app/' . $file->file);
          unlink($filename);
          if ($file->filepreview) {
            $j++;
            $filename2 = storage_path('app/' . $file->filepreview);
            unlink($filename2);
          }
        }

        rmdir(storage_path('app/cdn/user' . $this->user->id));
        $this->user->delete();
*/
    }
}
