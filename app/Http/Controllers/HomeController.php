<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function generateFavicon(Request $request)
    {
        //Read the favicon template from favicon.png
        //file from current directory
        $im = imagecreatefrompng(public_path("favicon/favicon.png"));
        $char = substr($request->get("title","U"), 0, 1);

        /* Read the character which needs to be added in favicon from
         * get request
         */
        if(isset($char) && !empty($char)) {
            $string = $char;
        } else {
            /* If no character is specified; add some default value */
            $string = 'V';
        }

        /* background color for the favicon */
        $bg = imagecolorallocate($im, 255, 255, 255);

        /* foreground (font) color for the favicon */
        $black = imagecolorallocate($im, 0, 0, 0);

        /* Write the character in favicon
         * arguements: image, fontsize, x-coordinate,
         *              y-coordinate, characterstring, color
         */
        imagechar($im, 2, 5, 1, $string, $black);

        header('Content-type: image/png');

        imagepng($im);
    }
}
