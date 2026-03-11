<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhkArea;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ToppageController extends Controller
{
    public function toppage(Request $request){
        $areas = NhkArea::all();
        $programs = collect();


        if ($request->query()) {
            // バリデーションルール
            $validator = Validator::make($request->all(), [
                'area' => 'required',
            ]);
            if ($validator->fails()) {
                // withInput() で入力値をセッションへ一時保存
                return redirect()->back()->withErrors($validator)->withInput();
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
