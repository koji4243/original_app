<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhkArea;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ToppageController extends Controller
{
    public function toppage(Request $request){
        $areas = NhkArea::all();
        $programs = collect();
        // ログインユーザーの予約を取得
        $userId = Auth::id();
        $reservedReservations = Reservation::where('user_id', $userId)->get();

        if ($request->has('date') && $request->has('area') ) {
            $request->validate([
                'area' => 'required',
            ]);
            
            $response = Http::get(
            config('services.nhk.base'),
            [
                'service' => 'g1',
                'area' => $request->area,
                'date' => $request->date,
                'key' => config('services.nhk.key'),
            ]);
            $programs = collect($response->json());
        }
    return view('top', compact('areas', 'programs', 'request', 'reservedReservations'));
    }
}
