<?php

namespace App\Http\Middleware;

use Route;
use Closure;
use App\Repositories\System\ApiLogRepository;

class Frontend
{
    protected $apiLogRepository;

    public function __construct(ApiLogRepository $apiLogRepository)
    {
        $this->apiLogRepository = $apiLogRepository;
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
        benchmark()->checkpoint();
        //API LOG
        $api_id = $this->apiLogRepository->create([
            'url'        => url()->current(),
            'route'      => Route::currentRouteName(),
            'param'      => json_encode($request->input()),
            'return_str' => json_encode([]),
        ]);
        //驗證token
        $key = config('app.encrypt_key');
        $time = openssl_decrypt($request->token, 'AES-256-ECB', $key, 0);
        if ($time < time()-600) {
            abort(403, 'Token错误，拒绝存取');
        }

        $response = $next($request);

        benchmark()->checkpoint();
        $this->apiLogRepository->update([
            'return_str' => json_encode($response->original, 320),
            'exec_time'  => benchmark()->getElapsedTime()->f,
        ], $api_id);

        return $response;
    }
}
