<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index');
    }
    public function about(): string
    {
        return view('home/about');
    }
    public function contact(): string
    {
        return view('home/contact');
    }
}
