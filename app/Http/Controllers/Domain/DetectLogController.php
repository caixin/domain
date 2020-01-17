<?php

namespace App\Http\Controllers\Domain;

use View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Domain\DetectLogService;

class DetectLogController extends Controller
{
    protected $detectLogService;

    public function __construct(DetectLogService $detectLogService)
    {
        $this->detectLogService = $detectLogService;
    }

    public function index(Request $request)
    {
        return view('detect_log.index', $this->detectLogService->list($request->input()));
    }

    public function search(Request $request)
    {
        return redirect(get_search_uri($request->input(), 'detect_log'));
    }
}
