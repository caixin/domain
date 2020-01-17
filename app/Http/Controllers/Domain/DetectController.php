<?php

namespace App\Http\Controllers\Domain;

use View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Domain\DetectService;

class DetectController extends Controller
{
    protected $detectService;

    public function __construct(DetectService $detectService)
    {
        $this->detectService = $detectService;
    }

    public function index(Request $request)
    {
        return view('detect.index', $this->detectService->list($request->input()));
    }

    public function search(Request $request)
    {
        return redirect(get_search_uri($request->input(), 'detect'));
    }

    public function edit($id)
    {
        View::share('sidebar', false);
        return view('detect.edit', $this->detectService->show($id));
    }

    public function update(Request $request, $id)
    {
        $this->detectService->update($request->post(), $id);

        session()->flash('message', '编辑成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function save(Request $request, $id)
    {
        $this->detectService->save($request->post(), $id);
        return 'done';
    }
}
