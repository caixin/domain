<?php

namespace App\Repositories\Domain;

use App\Repositories\AbstractRepository;
use Models\Domain\DetectLog;

class DetectLogRepository extends AbstractRepository
{
    public function __construct(DetectLog $entity)
    {
        parent::__construct($entity);
        $this->is_action_log = false;
    }

    public function _do_search()
    {
        if (isset($this->_search['node_id'])) {
            $this->db = $this->db->where('node_id', '=', $this->_search['node_id']);
        }

        if (isset($this->_search['group_id'])) {
            $this->db = $this->db->whereHas('domain', function ($query) {
                $query->where('group_id', '=', $this->_search['group_id']);
            });
        }

        if (isset($this->_search['domain_id'])) {
            $this->db = $this->db->where('domain_id', '=', $this->_search['domain_id']);
        }

        if (isset($this->_search['status'])) {
            $this->db = $this->db->where('status', '=', $this->_search['status']);
        }

        if (isset($this->_search['created_at1'])) {
            $this->db = $this->db->where('created_at', '>=', $this->_search['created_at1'] . ' 00:00:00');
        }
        if (isset($this->_search['created_at2'])) {
            $this->db = $this->db->where('created_at', '<=', $this->_search['created_at2'] . ' 23:59:59');
        }

        return $this;
    }

    /**
     * For 操作日誌用
     *
     * @var array
     */
    public static $columnList = [
        'id'        => '流水号',
        'node_id'   => '节点ID',
        'domain_id' => '网域ID',
        'status'    => '状态',
    ];
}
