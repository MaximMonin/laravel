<?php

namespace App\Http\Middleware;

use GuzzleHttp\Client;

class Backend {

    public $error = '';
    public $timeout = 0;

    public function __construct()
    {
    }

    public function call($procname, $proc_params)
    {
        $this->error = '';
        $client = new Client(['http_errors' => false ]);
        $url = env('BACKEND_URL', 'http://localhost') . '/cgi-bin/wspd_cgi.sh/WService=wsoblik/iOblik/' . $procname;

        try {
           if ($this->timeout !==0) {
             $res = $client->request('POST', $url, [ 'form_params' => $proc_params, 'timeout' => $this->timeout ] );
           }
           else {
             $res = $client->request('POST', $url, [ 'form_params' => $proc_params ]);
           }
        }
        catch (GuzzleHttp\Exception\ConnectException $e) {
          $response = $e->getResponse();
          $this->error = $response->getBody()->getContents();
        }
        catch (GuzzleHttp\Exception\ClientException $e) {
          $response = $e->getResponse();
          $this->error = $response->getBody()->getContents();
        }
        if (isset($res)) {
          return json_decode ($res->getBody());
        }
        else {
          return null;
        }
    }
}