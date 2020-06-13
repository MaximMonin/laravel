<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Storage;
use Illuminate\Http\File;

class MoveFileToNextcloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $targetPath;
    protected $file;
    protected $targetFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tDir, $ifile, $tFile)
    {
       $this->targetPath = $tDir;
       $this->file = $ifile;
       $this->targetFile = $tFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       // Copy file to Nextcloud Storage 
       $disk = Storage::disk('nextcloud');
       $disk->putFileAs($this->targetPath, new File ($this->file), $this->targetFile);
       // We need to delete the file when uploaded to nextcloud
       unlink($this->file);
    }
}
