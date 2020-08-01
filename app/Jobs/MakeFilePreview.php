<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\File;
use Image;

class MakeFilePreview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $fileid )
    {
       $this->fileid = $fileid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $file = File::find ($this->fileid);
      if (! $file) { return; }

      $finalPath = storage_path("app/");
      $image_name = $this->createFilePreview ($finalPath, $file);

      $resize_image = Image::make($finalPath . $file->file);
      $resize_image->resize(300, 300, function($constraint){
         $constraint->aspectRatio();
      })->save($finalPath . $image_name);

      $file->filepreview = $image_name;
      $file->save();
    }
    protected function createFilePreview($finalPath, $file)
    {
        $extension = pathinfo($file->file, PATHINFO_EXTENSION);
        $dir = pathinfo($file->file, PATHINFO_DIRNAME);

        $filename = str_replace(".".$extension, "", $file->filename); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_preview_" . md5(time()) . "." . $extension;
        return $dir . '/' . $filename;
    }

}
