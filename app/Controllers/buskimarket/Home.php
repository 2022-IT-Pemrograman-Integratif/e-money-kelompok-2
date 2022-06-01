<?php

namespace App\Controllers\Buskimarket;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function buskidi()
    {
        return view('buskidi');
    }
}
