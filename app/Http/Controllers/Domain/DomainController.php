<?php

namespace App\Http\Controllers\Domain;

use View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Domain\DomainForm;
use App\Services\Domain\DomainService;

class DomainController extends Controller
{
    protected $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    public function index(Request $request)
    {
        return view('domain.index', $this->domainService->list($request->input()));
    }

    public function search(Request $request)
    {
        return redirect(get_search_uri($request->input(), 'domain'));
    }

    public function create(Request $request)
    {
        View::share('sidebar', false);
        return view('domain.create', $this->domainService->create($request->input()));
    }

    public function store(DomainForm $request)
    {
        $this->domainService->store($request->post());

        session()->flash('message', '添加成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function edit($id)
    {
        View::share('sidebar', false);
        return view('domain.edit', $this->domainService->show($id));
    }

    public function update(DomainForm $request, $id)
    {
        $this->domainService->update($request->post(), $id);

        session()->flash('message', '编辑成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function save(Request $request, $id)
    {
        $this->domainService->save($request->post(), $id);
        return 'done';
    }

    public function destroy(Request $request)
    {
        $this->domainService->destroy($request->input('id'));
        session()->flash('message', '删除成功!');
        return 'done';
    }

    public function export(Request $request)
    {
        $this->domainService->export($request->input());
    }

    public function import(Request $request)
    {
        $this->domainService->import($request);
    }
}
