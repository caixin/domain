<?php

namespace Models\Domain;

use Models\Model;

class DetectLog extends Model
{
    const UPDATED_AT = null;
    
    protected $table = 'detect_log';

    protected $fillable = [
        'node_id',
        'domain_id',
        'status',
    ];

    const STATUS = [
        0 => '正常',
        1 => '失效',
        2 => '绑架',
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
