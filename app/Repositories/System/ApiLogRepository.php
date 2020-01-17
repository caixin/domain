<?php

namespace App\Repositories\System;

use App\Repositories\AbstractRepository;
use Models\System\ApiLog;

class ApiLogRepository extends AbstractRepository
{
    public function __construct(ApiLog $entity)
    {
        parent::__construct($entity);
        $this->is_action_log = false;
    }

    public function create($row)
    {
        $row['ip'] = request()->getClientIp();
        return parent::create($row);
    }

    public function _do_search()
    {
        if (isset($this->_search['route'])) {
            $this->db = $this->db->where('route', '=', $this->_search['route']);
        }

        if (isset($this->_search['ip'])) {
            $this->db = $this->db->where('ip', '=', $this->_search['ip']);
        }

        return $this;
    }
}
