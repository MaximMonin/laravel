<?php
namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Jobs\MoveFileToRemoteStorage;

use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    /**
     * Handles the file upload
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     */
    public function upload0(Request $request) {
      return $this->upload ($request, 'local');
    }

    public function upload(Request $request, $storage) {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        $filedir = $request->input('filedir');
        $action = $request->input('action');

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile(), $storage, $filedir, $action);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file to S3 server
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFile($file, $storage, $filedir, $action)
    {
//       Log::info('savefile: ' . 'storage: ' . $storage . ' file: ' . $file->getClientOriginalName() . " filedir: " . $filedir . " action:" . $action);

       if ($storage == '' || $storage == 'local') {
          $rc = $this->saveFileLocal($file, $filedir);
       }  
       elseif ($storage == 's3') { 
          $rc = $this->saveFileToS3($file, $filedir);
       }
       else $rc = $this->saveFileRemote($file, $storage, $filedir);

       if ($action == 'SaveDatabase') {
       }
       return $rc;
    }
    protected function saveFileToS3($file, $filedir)
    {
        $fileName = $this->createFilename($file);

        $disk = Storage::disk('s3');
        $disk->putFileAs($filedir, $file, $fileName);
        $mime = str_replace('/', '-', $file->getMimeType());

        // We need to delete the file when uploaded to s3
        unlink($file->getPathname());

        return response()->json([
            'path' => $disk->url($fileName),
            'name' => $fileName,
            'mime_type' =>$mime
        ]);
    }

    protected function saveFileRemote($file, $storage, $filedir)
    {
        $fileName = $this->createFilename($file);
        $mime = str_replace('/', '-', $file->getMimeType());

        $finalPath = storage_path("app/chunks/");
        $file->move($finalPath, $fileName);
        
        $targetPath = $filedir;
        MoveFileToRemoteStorage::dispatch ($storage, $targetPath, $finalPath . $fileName, $fileName);

        return response()->json([
            'path' => $filedir,
            'name' => $fileName,
            'mime_type' =>$mime
        ]);
    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFileLocal(UploadedFile $file, $filedir)
    {
        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());

        // Build the file path
        $filePath = $filedir;
        $finalPath = storage_path("app/".$filePath);

        // move the file name
        $file->move($finalPath, $fileName);

        return response()->json([
            'path' => $filePath,
            'name' => $fileName,
            'mime_type' => $mime
        ]);
    }

    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }

    public function uploaddelete0(Request $request) {
      return $this->uploaddelete ($request, 'local');
      
    }
    public function uploaddelete(Request $request, $storage) {
        $file = request ('file');

//       Log::info('deletefile: ' . 'storage: ' . $storage . ' file: ' . $file);

        Storage::disk($storage)->delete($file);

        return response()->json([
            'status' => true
        ]);
    }

}
