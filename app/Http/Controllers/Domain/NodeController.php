<?php

namespace App\Http\Controllers\Domain;

use View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Domain\NodeForm;
use App\Services\Domain\NodeService;

class NodeController extends Controller
{
    protected $nodeService;

    public function __construct(NodeService $nodeService)
    {
        $this->nodeService = $nodeService;
    }

    public function index(Request $request)
    {
        return view('node.index', $this->nodeService->list($request->input()));
    }

    public function search(Request $request)
    {
        return redirect(get_search_uri($request->input(), 'node'));
    }

    public function create(Request $request)
    {
        View::share('sidebar', false);
        return view('node.create', $this->nodeService->create($request->input()));
    }

    public function store(NodeForm $request)
    {
        $this->nodeService->store($request->post());

        session()->flash('message', '添加成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function edit($id)
    {
        View::share('sidebar', false);
        return view('node.edit', $this->nodeService->show($id));
    }

    public function update(NodeForm $request, $id)
    {
        $this->nodeService->update($request->post(), $id);

        session()->flash('message', '编辑成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function save(Request $request, $id)
    {
        $this->nodeService->save($request->post(), $id);
        return 'done';
    }

    public function destroy(Request $request)
    {
        $this->nodeService->destroy($request->input('id'));
        session()->flash('message', '删除成功!');
        return 'done';
    }
}
