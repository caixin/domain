<?php

namespace App\Repositories\Domain;

use App\Repositories\AbstractRepository;
use Models\Domain\DomainGroup;

class DomainGroupRepository extends AbstractRepository
{
    public function __construct(DomainGroup $entity)
    {
        parent::__construct($entity);
    }

    public function _do_search()
    {
        if (isset($this->_search['name'])) {
            $this->db = $this->db->where('name', '=', $this->_search['name']);
        }

        if (isset($this->_search['mode'])) {
            $this->db = $this->db->where('mode', '&', $this->_search['mode']);
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

    public function getGroupList($id=0)
    {
        $where[] = ['status', '=', 1];
        if ($id > 0) {
            $where[] = ['id', '!=', $id];
        }
        $result = $this->where($where)
            ->order(['sort','asc'])
            ->result()
            ->toArray();

        return array_column($result, 'name', 'id');
    }

    /**
     * For 操作日誌用
     *
     * @var array
     */
    public static $columnList = [
        'id'     => '流水号',
        'name'   => '节点名称',
        'mode'   => '检查模式',
        'status' => '状态',
    ];
}
