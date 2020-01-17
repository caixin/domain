<?php

namespace App\Repositories\Domain;

use App\Repositories\AbstractRepository;
use Models\Domain\Node;

class NodeRepository extends AbstractRepository
{
    public function __construct(Node $entity)
    {
        parent::__construct($entity);
    }

    public function _do_search()
    {
        if (isset($this->_search['name'])) {
            $this->db = $this->db->where('name', '=', $this->_search['name']);
        }

        if (isset($this->_search['server_ip'])) {
            $this->db = $this->db->where('server_ip', '=', $this->_search['server_ip']);
        }

        if (isset($this->_search['created_at1'])) {
            $this->db = $this->db->where('created_at', '>=', $this->_search['created_at1'] . ' 00:00:00');
        }
        if (isset($this->_search['created_at2'])) {
            $this->db = $this->db->where('created_at', '<=', $this->_search['created_at2'] . ' 23:59:59');
        }

        return $this;
    }

    public function getList($id=0)
    {
        $where = [];
        if ($id > 0) {
            $where[] = ['id', '!=', $id];
        }
        $result = $this->where($where)->result()->toArray();

        return array_column($result, 'name', 'id');
    }

    /**
     * For 操作日誌用
     *
     * @var array
     */
    public static $columnList = [
        'id'        => '流水号',
        'name'      => '节点名称',
        'server_ip' => '伺服器IP',
    ];
}
