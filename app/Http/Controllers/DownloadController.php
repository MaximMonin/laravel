<?php
namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{
    public function download(Request $request, $storage, $file) {
       $disk = Storage::disk($storage);

       if (!$disk->exists($file)) {
         return abort(404);
       }
       return $disk->download($file);
    }

    public function loadfile(Request $request, $storage, $file) {
       $disk = Storage::disk($storage);

       if (!$disk->exists($file)) {
         return abort(404);
       }
       $content = $disk->get($file);
       $type = $disk->getMimeType($file);
       $response = Response::make($content, 200);
       $response->header("Content-Type", $type);
       return $response;
    }
}
