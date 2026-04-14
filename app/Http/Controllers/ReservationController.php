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
use App\Jobs\ReservationJob;

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
        $deadline = Carbon::parse($request->input('start'))->subHours(4);
        //  予約受付時間外
        if (now()->isAfter($deadline)) {
            return redirect()
                ->route('top')
                ->with('message', "申し訳ございません。\n予約受付時間外です。")
                ->with('type', 'warning');
        }

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
        $reservation = $user->reservations()->create([
            'nhk_title'        => session('nhk.title'),
            'nhk_description'  => session('nhk.description') ?? '説明なし',
            'nhk_genres'       => session('nhk.genres'),
            'start_time'       => session('nhk.start'),
            'end_time'         => session('nhk.end'),
            'nhk_tvEpisodeId'  => session('nhk.nhkId'),
            'nhk_code'         => session('nhk.areaId'),
            'is_active'        => true,
            'notify_before_min'=> $request->input('set'),
        ]);
        //通知時間
        $notifyAt = $reservation->start_time
            ->copy()
            ->subMinutes($reservation->notify_before_min);
        // キューに入れる
        ReservationJob::dispatch($reservation)
            ->delay($notifyAt);

        $request->session()->forget('nhk');
        return redirect()
            ->route('top')
            ->with('type', 'success')
            ->with('message', "予約完了しました。\nメール通知をお待ちください。");
    }
    
    public function destroy(User $user, Reservation $reservation){
        $reservation->delete();
        return redirect()->route('reservation.list', $user)->with('message', '削除しました。');
    }
}
