<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use App\Events\Reservationed;
use App\Listeners\SendQueueMail;

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
        $users = User::with(['reservations' => function($query) {
            $query->orderBy('start_time', 'desc');
        }])->get();
        return view('reservation.list', compact('users', 'user'));
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
        $user = Auth::user();

        $reservation = new Reservation();
        $reservation->nhk_title = session('nhk.title');
        $reservation->nhk_description = session('nhk.description') ?? '説明なし' ;
        $reservation->nhk_genres = session('nhk.genres');
        $reservation->start_time = session('nhk.start');
        $reservation->end_time = session('nhk.end');
        $reservation->nhk_tvEpisodeId = session('nhk.nhkId');
        $reservation->nhk_code = session('nhk.areaId');
        $reservation->is_active = true;
        $reservation->notify_before_min = $request->input('set');
        $reservation->user_id = $user->id;
        $reservation->save();

        // イベント発火 → キュー
        $now = now()->floorMinute(); 

        $reservations = Reservation::with('user')
            ->whereNotNull('notify_before_min')
            ->whereNull('notify_at')
            ->whereRaw(
                'DATE_FORMAT(DATE_SUB(start_time, INTERVAL notify_before_min MINUTE), "%Y-%m-%d %H:%i") BETWEEN ? AND ?',
                [$now->copy()->subMinutes(10)->format('Y-m-d H:i'), $now->format('Y-m-d H:i')]
            )
            ->get();
        foreach ($reservations as $reservation) {
            event(new Reservationed($reservation));
        }

        $request->session()->forget('nhk');
        return redirect()->route('top')->with('message', '予約完了しました。メール通知をお待ちください。');
    }
    public function mail(){

        return redirect()->route('top');
    }
}
