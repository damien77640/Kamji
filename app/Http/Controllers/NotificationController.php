<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
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
     * Show the notification dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $nbInvitation = $this->getNbInvitation();
        //Get the id of the user
        $idUser = Auth::user()->id;
        //Get all the calendar that invite the user
        $resQuery = DB::table('calendar')
            ->join('users', 'calendar.owner', '=', 'users.id')
            ->select('participant_wait', 'name', 'title', 'calendar.id')
            ->where('participant_wait', 'like', "%{$idUser}%")
            ->get();
        //Add to var invintation the information for the page
        $invitation = [];
        foreach ($resQuery as $calendar) {
            $flag = false;
            $arr = [];
            foreach ($calendar as $key => $value) {
                if ($flag) {
                    $arr[$key] = $value;
                }
                if ($key == "participant_wait") {
                    $arrayuser = explode(",", str_replace(array('{', '}'), '', $value));
                    if (in_array($idUser, $arrayuser)) {
                        $flag = true;
                    }
                }
            }
            if (sizeof($arr) != 0) {
                array_push($invitation, $arr);
            }
        }
        return view('notification', ['invitation' => $invitation, 'nbInvitation' => $nbInvitation]);
    }

    /**
     * Accept an invitation.
     *
     *
     */
    public function accept(Request $request)
    {
        $idCalendar = $request->input('idCalendar');
        //Get the id of Calendar invitation
        $idUser = Auth::user()->id;
        //Delete the user id from participant_wait
        $resQuery = DB::table('calendar')
            ->select('participant_wait')
            ->where('id', $idCalendar)
            ->first();
        //Add the user id to participant_conf

        $arrayuser = explode(",", str_replace(array('{', '}'), '', $resQuery->participant_wait));
        $arrayuser = array_diff($arrayuser, array($idUser));
        $newParticipant = "{" . implode(",", $arrayuser) . "}";

        DB::table('calendar')
            ->where('id', $idCalendar)
            ->update(['participant_wait' => $newParticipant]);


        $resQuery = DB::table('calendar')
            ->select('participant_conf')
            ->where('id', $idCalendar)
            ->first();
        //Add the user id to participant_conf
        $newParticipant = null;
        $arrayuser = explode(",", str_replace(array('{', '}'), '', $resQuery->participant_conf));
        print_r($arrayuser);
        if (!empty($arrayuser[0])) {
            $arrayuser = array_merge($arrayuser, array($idUser));
            $newParticipant = "{" . implode(",", $arrayuser) . "}";
        } else {
            $newParticipant = "{" . $idUser . "}";
        }


        DB::table('calendar')
            ->where('id', $idCalendar)
            ->update(['participant_conf' => $newParticipant]);
        $message = 'accept';
        return redirect()->route('notification')->with(['message' => $message]);
    }

    /**
     * Refuse an invitation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refuse(Request $request)
    {
        $idCalendar = $request->input('idCalendar');
        //Get the id of Calendar invitation
        $idUser = Auth::user()->id;
        //Delete the user id from participant_wait
        $resQuery = DB::table('calendar')
            ->select('participant_wait')
            ->where('id', $idCalendar)
            ->first();
        //Add the user id to participant_conf

        $arrayuser = explode(",", str_replace(array('{', '}'), '', $resQuery->participant_wait));
        $arrayuser = array_diff($arrayuser, array($idUser));
        $newParticipant = "{" . implode(",", $arrayuser) . "}";

        DB::table('calendar')
            ->where('id', $idCalendar)
            ->update(['participant_wait' => $newParticipant]);
        $message = 'refuse';
        return redirect()->route('notification')->with(['message' => $message]);
    }

    /**
     * Get the number of invitation
     *
     * @return int
     */
    public function getNbInvitation()
    {
        //Get the id of the user
        $idUser = Auth::user()->id;
        //Get all the calendar that invite the user
        $resQuery = DB::table('calendar')
            ->join('users', 'calendar.owner', '=', 'users.id')
            ->select('participant_wait', 'name', 'title', 'calendar.id')
            ->where('participant_wait', 'like', "%{$idUser}%")
            ->get();
        //Add to var invintation the information for the page
        $nbInvitation = 0;
        foreach ($resQuery as $calendar) {
            $flag = false;
            $arr = [];
            foreach ($calendar as $key => $value) {
                if ($flag) {
                    $arr[$key] = $value;
                }
                if ($key == "participant_wait") {
                    $arrayuser = explode(",", str_replace(array('{', '}'), '', $value));
                    if (in_array($idUser, $arrayuser)) {
                        $flag = true;
                    }
                }
            }
            if (sizeof($arr) != 0) {
                $nbInvitation = $nbInvitation + 1;
            }
        }
        return $nbInvitation;
    }

}
