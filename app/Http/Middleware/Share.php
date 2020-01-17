<?php

namespace App\Http\Middleware;

use View;
use Closure;

class Share
{
    public function __construct()
    {
        //
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //讀取Version
        $json_string = file_get_contents("./backend/version.json");
        $version = json_decode($json_string, true);
        $share['version'] = $version['version'];
        //側邊欄顯示與否
        $share['sidebar'] = $request->input('sidebar') !== null && $request->input('sidebar') == 0 ? false:true;
        //一頁顯示筆數
        $share['per_page'] = session('per_page') ?: 20;

        View::share($share);
        return $next($request);
    }
}
