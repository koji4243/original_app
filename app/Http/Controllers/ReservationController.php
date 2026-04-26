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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Mail\SendChangeTitle;
use App\Mail\SendOfQueueMail;


use function Laravel\Prompts\progress;

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


        //開発用　jobで使用
    public function apiCheck(){
        $user = Auth::user();
        $reservation = $user->reservations()->latest()->first();


        $response = Http::get(
            config('services.nhk.base'),
            [
                'service' => 'g1',
                'area' => $reservation->nhk_code,
                'date' => Carbon::parse($reservation->start_time)->format('Y-m-d'),
                'key' => config('services.nhk.key'),
            ]);

        $programs = data_get($response->json(), 'g1.publication', []);
        $programMap = collect($programs)
            ->groupBy(fn ($p) => data_get($p, 'identifierGroup.tvSeriesId'));
        // 番組をIDで引けるようにする
        $programsForId = $programMap->get($reservation->nhk_tvEpisodeId);

        if (!$programsForId) return;

        if($reservation->notify_at === null){
            foreach ($programsForId as $program) {
                $apiTime = Carbon::parse(data_get($program, 'startDate'))->format('H:i');
                $resTime = Carbon::parse($reservation->start_time)->format('H:i');

                if ($apiTime === $resTime) {
                    $apiSeriesId = data_get($program, 'identifierGroup.tvSeriesId');
                    //  同じ番組
                    if ($apiSeriesId === $reservation->nhk_tvEpisodeId) {
                        dd('デバック用1');
                        // Mail::to($user->email)
                        //     ->send(new SendOfQueueMail($user, $reservation));
                    //  差し替え
                    } else {
                        dd('デバック用2');
                        // Mail::to($user->email)
                        //     ->send(new SendChangeTitle($user, $reservation));
                    }
                break;
                }
            }
        }
        return redirect()->route('top');
    }
}
