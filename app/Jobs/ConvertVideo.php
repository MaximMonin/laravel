<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\File;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;

class ConvertVideo implements ShouldQueue
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
       $this->queue = 'videoconvert';
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
      $ffprobe = FFProbe::create();
      if (! $ffprobe->isValid($finalPath . $file->file)) { return; }

      $inputformat = $ffprobe->format($finalPath . $file->file); // extracts file informations
      $secs =  $inputformat->get('duration');                    // returns the duration property
      $vstream = $ffprobe->streams($finalPath . $file->file)     // extracts streams informations
                              ->videos()                         // filters video streams
                              ->first();
      if (!$vstream) { return; }                                 // no video stream in input file
      $bitrate = $vstream->get('bit_rate') / 1024;               // video bitrate

      $astream = $ffprobe->streams($finalPath . $file->file)  // extracts streams informations
                              ->audios()                      // filters audio streams
                              ->first();                      // returns the first audio stream

      $abitrate = 256;
      if ($astream) {
        $abitrate = $vstream->get('bit_rate') / 1024;         // audio bitrate
      }

      if ($bitrate > 1000) {
        $bitrate = 1000;
      }
      if ($abitrate > 256) {
        $abitrate = 256;
      }

      $ffmpeg = FFMpeg::create(['timeout' => 3600, 'ffmpeg.threads' => 12, ]);
      $video_name = $this->createFileMP4 ($finalPath, $file);

      $video = $ffmpeg->open($finalPath . $file->file);
      $video->filters()->synchronize();
      $format = new X264();
      $format->setKiloBitrate($bitrate)
             ->setAudioCodec('aac')
             ->setAudioChannels(2)
             ->setAudioKiloBitrate($abitrate);

      $video->save($format, $finalPath . $video_name);

      if ($ffprobe->isValid($finalPath . $video_name) && filesize ($finalPath . $video_name) > 0) 
      {
        unlink ($finalPath . $file->file);
        $file->file = $video_name;
        $file->filetype = 'video/mp4';
        $file->filesize = filesize($finalPath . $video_name);
        $file->save();
      }
      else {
        unlink ($finalPath . $video_name);
      }
    }
    protected function createFileMP4($finalPath, $file)
    {
        $extension = pathinfo($file->file, PATHINFO_EXTENSION);
        $dir = pathinfo($file->file, PATHINFO_DIRNAME);

        $filename = str_replace(".".$extension, "", $file->filename); // Filename without extension

        $extension = 'mp4';
        $filename .= "_convert_" . md5(time()) . "." . $extension;
        return $dir . '/' . $filename;
    }

}
