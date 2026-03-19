<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;



class ReservationController extends Controller
{
    public function create(Request $request){
        //GET送信で来てほしくない
        if (!session()->has('nhk')) {
            return redirect()->route('top');
        }
        return view('reservation.create');
    }
    public function list(User $user){
        return view('reservation.list');
    }
    public function setting(Request $request){
        $request->session()->put('nhk', $request->all());
        return view('reservation.create');
    }
    public function check(Request $request, User $user){
        $request->validate(['set' => 'required']);
        //通知時間
        $set = $request->input('set');
        $start = Carbon::parse(session('nhk.start'));

        $notifyTime = $start->copy()->subMinutes($set);
        return view('reservation.check', compact('set', 'notifyTime', 'user'));
    }
    public function store(Request $request){

        $reservation = new Reservation();
        $reservation->nhk_title = session('nhk.title');
        $reservation->nhk_description = session('nhk.description');
        $reservation->nhk_genres = session('nhk.genres');
        $reservation->start_time = session('nhk.start');
        $reservation->end_time = session('nhk.end');
        $reservation->nhk_tvEpisodeId = session('nhk.nhkId');
        $reservation->nhk_code = session('nhk.areaId');
        $reservation->is_active = true;
        $reservation->notify_before_min = $request->input('set');
        $reservation->user_id = Auth::id();
        $reservation->save();

        $request->session()->forget('nhk');
        return redirect()->route('top')->with('message', '予約完了しました。\nメール通知をお待ちください。');
    }

    public function sendMail(){
        $user = User::find(1);
        Mail::to($user)->send(new SendMail($user));
    }
}
