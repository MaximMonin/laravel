<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

class SftpServiceProvider extends ServiceProvider
{
    public function boot()
    {
       Storage::extend('sftp', function ($app, $config) {
           return new Filesystem(new SftpAdapter($config));
       });
    }

    public function register()
    {

    }
}
