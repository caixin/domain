<?php

namespace App\Repositories\Domain;

use App\Repositories\AbstractRepository;
use Models\Domain\Domain;

class DomainRepository extends AbstractRepository
{
    public function __construct(Domain $entity)
    {
        parent::__construct($entity);
    }

    public function _do_search()
    {
        if (isset($this->_search['group_id'])) {
            $this->db = $this->db->where('group_id', '=', $this->_search['group_id']);
        }

        if (isset($this->_search['ssl'])) {
            $this->db = $this->db->where('ssl', '=', $this->_search['ssl']);
        }

        if (isset($this->_search['domain'])) {
            $this->db = $this->db->where('domain', '=', $this->_search['domain']);
        }

        if (isset($this->_search['deadline1'])) {
            $this->db = $this->db->where('created_at', '>=', $this->_search['deadline1']);
        }
        if (isset($this->_search['deadline2'])) {
            $this->db = $this->db->where('created_at', '<=', $this->_search['deadline2']);
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
        $result = $this->where($where)->order(['group_id','asc'])->result();
        $data = [];
        foreach ($result as $row) {
            $data[$row->group->name][$row->id] = $row->domain;
        }
        return $data;
    }

    /**
     * For 操作日誌用
     *
     * @var array
     */
    public static $columnList = [
        'id'       => '流水号',
        'group_id' => '群组ID',
        'domain'   => '网域名称',
        'deadline' => '网域到期日',
        'ssl'      => 'SSL凭证',
        'supplier' => '购买地点',
        'remark'   => '备注',
    ];
}
