<?php

namespace App\Services\Domain;

use App\Repositories\Domain\NodeRepository;
use App\Repositories\Domain\DomainRepository;
use App\Repositories\Domain\DetectRepository;

class NodeService
{
    protected $nodeRepository;
    protected $domainRepository;
    protected $detectRepository;

    public function __construct(
        NodeRepository $nodeRepository,
        DomainRepository $domainRepository,
        DetectRepository $detectRepository
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->domainRepository = $domainRepository;
        $this->detectRepository = $detectRepository;
    }

    public function list($input)
    {
        $search_params = param_process($input, ['id', 'asc']);
        $order         = $search_params['order'];
        $search        = $search_params['search'];
        $params_uri    = $search_params['params_uri'];

        $result = $this->nodeRepository->search($search)
            ->order($order)->paginate(session('per_page'))
            ->result()->appends($input);

        return [
            'result'     => $result,
            'search'     => $search,
            'order'      => $order,
            'params_uri' => $params_uri,
        ];
    }

    public function create($input)
    {
        $row = $this->nodeRepository->getEntity();
        if ($input['id'] > 0) {
            $row = $this->nodeRepository->row($input['id']);
        }

        return [
            'row'  => $row,
        ];
    }

    public function store($row)
    {
        $row = array_map('strval', $row);
        $id = $this->nodeRepository->create($row);

        $domain = $this->domainRepository->result();
        $insert = [];
        foreach ($domain as $arr) {
            $insert[] = [
                'node_id'    => $id,
                'domain_id'  => $arr['id'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => session('username') ?: '',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('username') ?: '',
            ];
        }
        if ($insert != []) {
            $this->detectRepository->insert_batch($insert);
        }
    }

    public function show($id)
    {
        $row = $this->nodeRepository->find($id);

        return [
            'row'  => $row,
        ];
    }

    public function update($row, $id)
    {
        $row = array_map('strval', $row);

        $this->nodeRepository->update($row, $id);
    }

    public function save($row, $id)
    {
        $this->nodeRepository->save($row, $id);
    }

    public function destroy($id)
    {
        $this->nodeRepository->delete($id);
        $this->detectRepository->search(['node_id'=>$id])->delete();
    }
}
