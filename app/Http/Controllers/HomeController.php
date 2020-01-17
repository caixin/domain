<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Domain\DomainService;

class HomeController extends Controller
{
    protected $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    public function index()
    {
        return view('home', [
            'health' => $this->domainService->healthList(),
        ]);
    }

    public function updateHealth()
    {
        return response()->json($this->domainService->healthList());
    }
}
