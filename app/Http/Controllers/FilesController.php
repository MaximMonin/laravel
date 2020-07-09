<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Fetch all user's photos
    */
    public function fetchPhotos()
    {
       $user = Auth::user();
       return $user->files()->latest()->whereRaw('filetype LIKE "image%"')->paginate(20)->getCollection();
    }
    /**
     * Fetch all user's videos
    */
    public function fetchVideos()
    {
       $user = Auth::user();
       return $user->files()->latest()->whereRaw('filetype LIKE "video%"')->paginate(20)->getCollection();
    }
    /**
     * Fetch all user's Documents
    */
    public function fetchDocs()
    {
       $user = Auth::user();
       return $user->files()->latest()->whereRaw('NOT (filetype LIKE "image%" or filetype LIKE "video%")')->paginate(20)->getCollection();
    }

}
