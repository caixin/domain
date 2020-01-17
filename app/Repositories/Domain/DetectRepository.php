<?php

namespace App\Repositories\Domain;

use App\Repositories\AbstractRepository;
use Models\Domain\Detect;

class DetectRepository extends AbstractRepository
{
    public function __construct(Detect $entity)
    {
        parent::__construct($entity);
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
        if (isset($this->_search['domain_ids'])) {
            $this->db = $this->db->whereIn('domain_id', $this->_search['domain_ids']);
        }

        if (isset($this->_search['lock'])) {
            if ($this->_search['lock'] == 1) {
                $this->db = $this->db->where('lock_time', '>', '2000-01-01 00:00:00');
            } else {
                $this->db = $this->db->where('lock_time', '<=', '2000-01-01 00:00:00');
            }
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

        if (isset($this->_search['group_id'])) {
            $this->db = $this->db->whereHas('domain', function ($query) {
                $query->where('group_id', $this->_search['group_id']);
            });
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
        'lock_time' => '锁定时间',
        'status'    => '状态',
    ];
}
