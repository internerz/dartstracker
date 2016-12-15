<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $class = $request->cookie('splashscreen') ? '' : 'splashscreen';
        $response = new Response(view('home', array(
            'class' => $class,
        )));
        $response->withCookie(cookie('splashscreen', true, 3600));
        return $response;
    }
}
