<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhkArea;
use Illuminate\Support\Facades\Http;

class ToppageController extends Controller
{
    public function toppage(Request $request){
    $areas = NhkArea::all();
    $programs = collect();

    // 入力値取得
    $date = $request->input('date');
    $area = $request->input('area');

    // バリデーション
    if ($request->filled(['date', 'area']) || empty($request)) {
        $errors = [];
        if (!$date) {
            $errors['date'] = '日付を選択してください';
        }
        if (!$area) {
            $errors['area'] = 'エリアを選択してください';
        }
        if (!empty($errors)) {
            return redirect()->route('top')
                            ->withErrors($errors)
                            ->withInput();
            }
        }

        $response = Http::get(
        config('services.nhk.base'),
        [
            'service' => 'g1',
            'area' => $request->area,
            'date' => $request->date,
            'key' => config('services.nhk.key'),
        ]);
        $programs = collect($response->json());
        return view('top', compact('areas', 'programs', 'request'));
    }
}
