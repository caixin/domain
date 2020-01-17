<?php

namespace App\Services\Domain;

use App\Repositories\Domain\DomainGroupRepository;

class DomainGroupService
{
    protected $domainGroupRepository;

    public function __construct(DomainGroupRepository $domainGroupRepository)
    {
        $this->domainGroupRepository = $domainGroupRepository;
    }

    public function list($input)
    {
        $search_params = param_process($input, ['sort', 'asc']);
        $order         = $search_params['order'];
        $search        = $search_params['search'];
        $params_uri    = $search_params['params_uri'];

        $result = $this->domainGroupRepository->search($search)
            ->order($order)->paginate(session('per_page'))
            ->result()->appends($input);

        return [
            'result'     => $result,
            'search'     => $search,
            'order'      => $order,
            'params_uri' => $params_uri,
            'group'      => $this->domainGroupRepository->getGroupList(),
        ];
    }

    public function create($input)
    {
        $row = $this->domainGroupRepository->getEntity();
        if ($input['id'] > 0) {
            $row = $this->domainGroupRepository->row($input['id']);
        }
        $row['mode'] = bindec_array($row['mode']);

        return [
            'row'   => $row,
            'group' => $this->domainGroupRepository->getGroupList($input['id']),
        ];
    }

    public function store($row)
    {
        $row['mode'] = array_sum($row['mode']);
        $row = array_map('strval', $row);
        $this->domainGroupRepository->create($row);
    }

    public function show($id)
    {
        $row = $this->domainGroupRepository->find($id);
        $row['mode'] = bindec_array($row['mode']);

        return [
            'row'   => $row,
            'group' => $this->domainGroupRepository->getGroupList($id),
        ];
    }

    public function update($row, $id)
    {
        $row['mode'] = array_sum($row['mode']);
        $row = array_map('strval', $row);

        $this->domainGroupRepository->update($row, $id);
    }

    public function save($row, $id)
    {
        $this->domainGroupRepository->save($row, $id);
    }

    public function destroy($id)
    {
        $this->domainGroupRepository->delete($id);
    }
}
