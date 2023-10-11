<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function chart()
    {
        // 連線到資料庫
        DB::connection('mysql');

        // 取值
        $value = DB::table('chart')->orderBy('id', 'desc')->limit(1)->value('value');

        return view('chart')->with('value', $value);
    }

    public function chartEventStream()
    {
        // 連線到資料庫
        DB::connection('mysql');
        $randomNumber=rand(1,100);
        $t=strtotime('+0 hoirs');
        DB::insert('insert into chart (value,time) values(?,?)',[ $randomNumber, date('y-md H:i:s',$t)]);
        $data = [
            'time' => date('Y-m-d H:i:s', $t),
            // 取值
            'value' => DB::table('chart')->orderBy('id', 'desc')->limit(1)->value('value')
        ];

        $response = new StreamedResponse();
        $response->setCallback(function () use ($data) {
            echo 'data: ' . json_encode($data) . "\n\n";
            echo "retry: 5000\n";
            ob_flush();
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cach-Control', 'no-cache');
        $response->send();
    }
}