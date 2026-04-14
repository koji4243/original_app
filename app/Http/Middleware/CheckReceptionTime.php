<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class CheckReceptionTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!session('nhk.start')) {
            return redirect()
                ->route('top')
                ->with('message', "申し訳ございません。\nセッションの有効時間が切れました。")
                ->with('type', 'warning');
            }
        $target = Carbon::parse(session('nhk.start'))->subHours(4);
        if (now()->isAfter($target)) {
            return redirect()
                ->route('top')
                ->with('message', "申し訳ございません。\n予約受付時間外です。")
                ->with('type', 'warning');
            }
        return $next($request); // ← OKなら処理続行
    }
}
