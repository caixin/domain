<?php

namespace App\Services\Domain;

use App\Repositories\Domain\DetectLogRepository;
use App\Repositories\Domain\NodeRepository;
use App\Repositories\Domain\DomainRepository;

class DetectLogService
{
    protected $detectLogRepository;
    protected $nodeRepository;
    protected $domainRepository;

    public function __construct(
        DetectLogRepository $detectLogRepository,
        NodeRepository $nodeRepository,
        DomainRepository $domainRepository
    ) {
        $this->detectLogRepository = $detectLogRepository;
        $this->nodeRepository = $nodeRepository;
        $this->domainRepository = $domainRepository;
    }

    public function list($input)
    {
        $search_params = param_process($input, ['id', 'desc']);
        $order         = $search_params['order'];
        $search        = $search_params['search'];
        $params_uri    = $search_params['params_uri'];

        $result = $this->detectLogRepository->search($search)
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
}
