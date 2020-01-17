<?php

namespace App\Services\Domain;

use App\Repositories\Domain\DetectRepository;
use App\Repositories\Domain\NodeRepository;
use App\Repositories\Domain\DomainGroupRepository;
use App\Repositories\Domain\DomainRepository;

class DetectService
{
    protected $detectRepository;
    protected $nodeRepository;
    protected $domainGroupRepository;
    protected $domainRepository;

    public function __construct(
        DetectRepository $detectRepository,
        NodeRepository $nodeRepository,
        DomainGroupRepository $domainGroupRepository,
        DomainRepository $domainRepository
    ) {
        $this->detectRepository = $detectRepository;
        $this->nodeRepository = $nodeRepository;
        $this->domainGroupRepository = $domainGroupRepository;
        $this->domainRepository = $domainRepository;
    }

    public function list($input)
    {
        $search_params = param_process($input, ['id', 'asc']);
        $order         = $search_params['order'];
        $search        = $search_params['search'];
        $params_uri    = $search_params['params_uri'];

        $result = $this->detectRepository->search($search)
            ->order($order)->paginate(session('per_page'))
            ->result()->appends($input);

        return [
            'result'     => $result,
            'search'     => $search,
            'order'      => $order,
            'params_uri' => $params_uri,
            'node'       => $this->nodeRepository->getList(),
            'domain'     => $this->domainRepository->getList(),
        ];
    }

    public function show($id)
    {
        $row = $this->detectRepository->find($id);

        return [
            'row' => $row,
        ];
    }

    public function update($row, $id)
    {
        $row = array_map('strval', $row);

        $this->detectRepository->update($row, $id);
    }

    public function save($row, $id)
    {
        $row['lock_time'] = $row['lock'] == 1 ? date('Y-m-d H:i:s'):'1970-01-01';
        $row['status'] = $row['lock'];
        unset($row['lock']);

        $this->detectRepository->save($row, $id);
    }
}
