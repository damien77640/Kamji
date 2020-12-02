<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Auth;

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
        $nbInvitation = app('App\Http\Controllers\NotificationController')->getNbInvitation();
        return view('home', ['nbInvitation' => $nbInvitation]);
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
        $description = strip_tags($request->input('description'));
        $start = substr($request->input('start'), 0, 10);
        $heureDeb = $request->input('heureDeb');
        $eventDeb = $start . " " . $heureDeb;
        $end = substr($request->input('end'), 0, 10);
        $heureFin = $request->input('heureFin');
        $eventFin = $end . " " . $heureFin;
        if ($eventFin == ' ') $eventFin = null;
        $color = $request->input('color');
        $owner = $request->input('owner');
        $calendrier = $request->input('calendrier');
        if ($request->input('check-day') == 'on') $allDay = 1;
        else $allDay = 0;
        $data = array("title" => $title, "description" => $description, "start" => $eventDeb, "end" => $eventFin, "color" => $color, "owner" => $owner, "calendrier" => $calendrier, "allDay" => $allDay);
        DB::table('events')->insert($data);
        $message = "eventCreate";
//        return view('home', ['message' => $message, 'title' => $title, 'idCalendar' => $calendrier]);
        return redirect()->route('home')->with(['message' => $message, 'title' => $title, 'idCalendar' => $calendrier]);
    }

    public function supprCalendar(Request $request)
    {
        $id = $request->input('idCalendar');
        $user = $request->input('idUser');
        $nb_calendar = DB::table('calendar')->where('owner', $user)->count();
        if ($nb_calendar == 1) {
            $message = "calendarDelete_Error";
        } else {
            DB::table('calendar')->where('id', $id)->delete();
            $message = "calendarDelete";
        }
        return redirect()->route('home')->with(['message' => $message]);
    }

    public function leftCalendar(Request $request)
    {
        $id = $request->input('idCalendar');
        $user = $request->input('idUser');

        $resQuery = DB::table('calendar')
            ->select('participant_conf')
            ->where('id', $id)
            ->first();
        $arrayuser = explode(",", str_replace(array('{', '}'), '', $resQuery->participant_conf));

        if (($key = array_search($user, $arrayuser)) !== false) {
            unset($arrayuser[$key]);
        }
        $newParticipant = "{" . implode(",", $arrayuser) . "}";
        DB::table('calendar')
            ->where('id', $id)
            ->update(['participant_conf' => $newParticipant]);
        $message = "calendarLeft";
        return redirect()->route('home')->with(['message' => $message]);
    }

    public function shareCalendar(Request $request)
    {
        $message = "shareCalendar";
        $id = $request->input('idCalendar');
        $userArray = $request->input('pseudonymeArray');

        //formatage
        $userArray = explode(",", $userArray);
        $userArray = array_filter($userArray);
        $userArray = implode(",", array_values($userArray));
        $userArray = "{" . $userArray . "}";

        DB::table('calendar')->where('id', $id)->update(['participant_wait' => $userArray]);

        return redirect()->route('home')->with(['message' => $message, 'idCalendar' => $id]);
    }

    public function qrcode(Request $request)
    {
        $message = "QRCalendar";
        $id = $request->query('numcal');
        $currentuserid = array(Auth::user()->id);

        $resQuery = DB::table('calendar')
            ->select('participant_conf')
            ->where('id', $id)
            ->first();
        $arrayuser = explode(",", str_replace(array('{', '}'), '', $resQuery->participant_conf));

        $arrayuser = array_merge($arrayuser, array_diff($currentuserid, $arrayuser));
        $newParticipant = "{" . implode(",", array_filter($arrayuser)) . "}";
        DB::table('calendar')
            ->where('id', $id)
            ->update(['participant_conf' => $newParticipant]);
       return redirect()->route('home')->with(['message' => $message, 'idCalendar' => $id]);
    }

    public function shareviewCalendar(Request $request)
    {
        $message = "participantRemove";
        $id = $request->input('idCalendar');
        $user = $request->input('userConf');

        $resQuery = DB::table('calendar')
            ->select('participant_conf')
            ->where('id', $id)
            ->first();
        $arrayuser = explode(",", str_replace(array('{', '}'), '', $resQuery->participant_conf));

        if (($key = array_search($user, $arrayuser)) !== false) {
            unset($arrayuser[$key]);
        }
        $newParticipant = "{" . implode(",", $arrayuser) . "}";
        DB::table('calendar')
            ->where('id', $id)
            ->update(['participant_conf' => $newParticipant]);
        return redirect()->route('home')->with(['message' => $message, 'idCalendar' => $id]);
    }

    public function getID()
    {
        return view('ajaxIDFromMail');
    }

    public function PostID(Request $request)
    {
        $mail = $request->mail;
        $listuser = $request->list;
        $iduser_init = $request->id;
        $idUser = DB::table('users')->where('email', $mail)->first();
        if ($idUser === null) {
            $result = "none";
        } else {
            $idUser = $idUser->id;
            if (in_array($idUser, explode(",", $listuser))) {
                $result = "double";
            } else if ($idUser == $iduser_init) {
                $result = "you";
            } else {
                $result = $idUser;
            }
        }
        return response()->json(['success' => $result]);
    }

    public function changeCalendar(Request $request)
    {
        $idCalendar = $request->input('idCalendar');
        return redirect()->route('home')->with(['idCalendar' => $idCalendar]);
    }

    public function modifCalendar(Request $request)
    {
        $idCalendar = $request->input('idCalendar');
        $title_modif = strip_tags($request->input('title'));
        $color_modif = $request->input('color');
        $logo_modif = $request->input('logo');
        DB::table('calendar')->where('id', $idCalendar)->update(['title' => $title_modif, 'color' => $color_modif, 'logo' => $logo_modif]);
        $message = "calendarEdit";
        return redirect()->route('home')->with(['idCalendar' => $idCalendar, 'message' => $message]);
    }

    public function modifEvent(Request $request)
    {
        $idCalendar = $request->input('idCalendar');
        $idEvent = $request->input('id');
        $title_modif = strip_tags($request->input('eventTitle'));
        $color_modif = $request->input('color');
        $desc_modif = $request->input('eventDesc');
        DB::table('events')->where('id', $idEvent)->update(['title' => $title_modif, 'color' => $color_modif, 'description' => $desc_modif]);
        $message = "EventEdit";
        return redirect()->route('home')->with(['idCalendar' => $idCalendar, 'message' => $message, 'title' => $title_modif]);
    }

    public function deleteEvent(Request $request)
    {
        $idCalendar = $request->input('idCalendar');
        $idEvent = $request->input('id');
        $title = strip_tags($request->input('eventTitle'));
        DB::table('events')->where('id', $idEvent)->delete();
        $message = "EventDelete";
        return redirect()->route('home')->with(['idCalendar' => $idCalendar, 'message' => $message, 'title' => $title]);
    }

    public function getEventInfo()
    {
        return view('ajaxEventInfo');
    }

    public function PostEventInfo(Request $request)
    {
        $idEvent = $request->idEvent;
        $event = DB::table('events')->where('id', $idEvent)->first();
        if ($event === null) {
            $event = "none";
        }
        return response()->json(['success' => $event]);
    }
}
