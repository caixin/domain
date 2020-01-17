<?php

namespace App\Http\Controllers\Domain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Domain\JumpLogService;

class JumpLogController extends Controller
{
    protected $jumpLogService;

    public function __construct(JumpLogService $jumpLogService)
    {
        $this->jumpLogService = $jumpLogService;
    }

    public function index(Request $request)
    {
        return view('jump_log.index', $this->jumpLogService->list($request->input()));
    }

    public function search(Request $request)
    {
        return redirect(get_search_uri($request->input(), 'jump_log'));
    }
}
