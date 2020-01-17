<?php

namespace App\Http\Controllers\Domain;

use View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Domain\DomainGroupForm;
use App\Services\Domain\DomainGroupService;
use App\Repositories\Domain\DomainGroupRepository;
use Validator;
use Exception;

class DomainGroupController extends Controller
{
    protected $domainGroupService;
    protected $domainGroupRepository;

    public function __construct(
        DomainGroupService $domainGroupService,
        DomainGroupRepository $domainGroupRepository
    ) {
        $this->domainGroupService = $domainGroupService;
        $this->domainGroupRepository = $domainGroupRepository;
    }

    public function index(Request $request)
    {
        return view('domain_group.index', $this->domainGroupService->list($request->input()));
    }

    public function search(Request $request)
    {
        return redirect(get_search_uri($request->input(), 'domain_group'));
    }

    public function create(Request $request)
    {
        View::share('sidebar', false);
        return view('domain_group.create', $this->domainGroupService->create($request->input()));
    }

    public function store(DomainGroupForm $request)
    {
        $this->domainGroupService->store($request->post());

        session()->flash('message', '添加成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function edit($id)
    {
        View::share('sidebar', false);
        return view('domain_group.edit', $this->domainGroupService->show($id));
    }

    public function update(DomainGroupForm $request, $id)
    {
        $this->domainGroupService->update($request->post(), $id);

        session()->flash('message', '编辑成功!');
        return "<script>parent.window.layer.close();parent.location.reload();</script>";
    }

    public function save(Request $request, $id)
    {
        $this->domainGroupService->save($request->post(), $id);
        return 'done';
    }

    public function destroy(Request $request)
    {
        $this->domainGroupService->destroy($request->input('id'));
        session()->flash('message', '删除成功!');
        return 'done';
    }

    public function hash(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ]);
            if ($validator->fails()) {
                $message = implode("\n", $validator->errors()->all());
                throw new Exception($message, 422);
            }
            //網域群組
            $group = $this->domainGroupRepository->row($request->group_id);
            if (count($group->domains) == 0) {
                throw new Exception('该群组底下无网域', 422);
            }

            foreach ($group->domains as $row) {
                if ($row->status == 0) {
                    $url = ($row['ssl'] ? 'https':'http')."://$row[domain]/$group[path]";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $output = curl_exec($ch);
                    curl_close($ch);

                    $group->value1 = md5($output);
                    $group->save();
                    return 'done';
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
