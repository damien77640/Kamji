<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
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
        $nbInvitation = app('App\Http\Controllers\NotificationController')->getNbInvitation();
        return view('newCalendar', ['nbInvitation' => $nbInvitation]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function insertform()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insert(Request $request)
    {
        $title = strip_tags($request->input('title'));
        $owner = $request->input('owner');
        $date_crea = date('m-d-Y h:i:s', time());
        $date_modif = date('m-d-Y h:i:s', time());
        $color = $request->input('color');
        $logo = $request->input('logo');
        $datas = array("title" => $title, "owner" => $owner, "date_crea" => $date_crea, "date_modif" => $date_modif, "color" => $color, "logo" => $logo);
        DB::table('calendar')->insert($datas);
        $results = DB::select('select max(id) from calendar');
        $idCalendar = $results[0]->max;
        $message = "calendarCreate";
        return redirect()->route('home')->with(['idCalendar' => $idCalendar,'message' => $message]);

    }



}

