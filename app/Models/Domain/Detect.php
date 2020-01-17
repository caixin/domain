<?php

namespace Models\Domain;

use Models\Model;

class Detect extends Model
{
    protected $table = 'detect';

    protected $fillable = [
        'node_id',
        'domain_id',
        'lock_time',
        'status',
        'created_by',
        'updated_by',
    ];

    const STATUS = [
        0 => '正常',
        1 => '失效',
        2 => '绑架',
    ];

    const LOCK = [
        1 => '锁定',
        0 => '解除',
    ];

    public function node()
    {
        return $this->belongsTo('Models\Domain\Node', 'node_id');
    }

    public function domain()
    {
        return $this->belongsTo('Models\Domain\Domain', 'domain_id');
    }
}
