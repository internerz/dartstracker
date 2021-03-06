<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $class = $request->cookie('splashscreen') ? '' : 'splashscreen';
        $response = new Response(view('home', [
            'class' => $class,
        ]));
        $response->withCookie(cookie('splashscreen', true, 3600));

        return $response;
    }
}
