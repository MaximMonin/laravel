<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class FileUploadTest extends DuskTestCase
{
    protected $user;

    public function testUpload()
    {
        $this->user = factory('App\User')->create();
        $user = $this->user;

        // Dropzone fileupload
        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->loginAs($user)
                    ->visit('/user/upload')
                    ->click('@upload-tab')
                    ->clickLink ( __('messages.UploadNew') )
                    ->attach('input.dz-hidden-input', base_path('tests/data/profile.jpg'))
                    ->attach('input.dz-hidden-input', base_path('tests/data/video.mp4'))
                    ->attach('input.dz-hidden-input', base_path('tests/data/laravel7.pdf'))
                    ->pause (3000)
                    ->assertSeeLink(__("upload.remove") )
                    ->assertSee('video.mp4')
                    ->assertSee('laravel7.pdf')
                    ->screenshot('fileupload');
        });

        // Checking if there are loaded files with the same size and md5 sum
        $files = $this->user->files()->latest()->paginate(20)->getCollection();
        $this->assertNotEmpty( $files, 'No Files created in database');
        $i = 0;
        $j = 0;
        foreach ($files as $file) {
          if ($file->filename == 'profile.jpg') {
            $photo = $file;
          }
          if ($file->filename == 'video.mp4') {
            $video = $file;
          }
          if ($file->filename == 'laravel7.pdf') {
            $doc = $file;
          }
          $i++;
          $filename = storage_path('app/' . $file->file);
          $this->assertEquals(filesize(base_path('tests/data/' . $file->filename)), filesize($filename), 'source filesize differs from uploaded size');
          $this->assertEquals(md5_file(base_path('tests/data/' . $file->filename)), md5_file($filename), 'source md5sum differs from uploaded md5sum');
          if ($file->filepreview) {
            $j++;
            $filename2 = storage_path('app/' . $file->filepreview);
            $this->assertEquals(true, filesize($filename2) > 0, 'preview filesize is 0');
          }
        }
        $this->assertEquals (3, $i, 'Not all files loaded' );
        $this->assertEquals (1, $j, 'No preview files for images' );
        $this->assertNotEmpty( $video, 'No video File');
        $this->assertNotEmpty( $doc, 'No document File');
        $this->assertNotEmpty( $photo, 'No image file');

        // Checking Photos Videos Docs tabs and check file detected 
        $this->browse(function (Browser $browser2) use ($user, $photo, $video, $doc) {
           $browser2->logout()
                    ->loginAs($user)
                    ->visit('/user/upload')
                    ->click('@photo-tab')
                    ->assertSourceHas($photo->file)
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
    }
}
