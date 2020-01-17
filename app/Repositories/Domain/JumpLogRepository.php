<?php

namespace App\Repositories\Domain;

use App\Repositories\AbstractRepository;
use App\Repositories\System\Ip2locationRepository;
use Models\Domain\JumpLog;

class JumpLogRepository extends AbstractRepository
{
    protected $ip2locationRepository;

    public function __construct(JumpLog $entity, Ip2locationRepository $ip2locationRepository)
    {
        parent::__construct($entity);
        $this->ip2locationRepository = $ip2locationRepository;
        $this->is_action_log = false;
    }

    public function create($row)
    {
        $ip_info = $this->ip2locationRepository->getIpData($row['ip']);
        $row['ip_info'] = json_encode($ip_info ?? []);

        return parent::create($row);
    }

    public function _do_search()
    {
        if (isset($this->_search['domain_id'])) {
            $this->db = $this->db->where('domain_id', '=', $this->_search['domain_id']);
        }
        if (isset($this->_search['domain_ids'])) {
            $this->db = $this->db->whereIn('domain_id', $this->_search['domain_ids']);
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
        'ip'        => 'IP',
        'url'       => '當前網址',
        'domain_id' => '网域ID',
        'status'    => '状态',
    ];
}
