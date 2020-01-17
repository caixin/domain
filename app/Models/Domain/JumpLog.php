<?php

namespace Models\Domain;

use Models\Model;

class JumpLog extends Model
{
    const UPDATED_AT = null;
    
    protected $table = 'jump_log';

    protected $fillable = [
        'url',
        'domain_id',
        'status',
        'ip',
        'ip_info',
    ];

    const STATUS = [
        0 => '正常',
        1 => '失效',
        2 => '绑架',
    ];
    
    public function domain()
    {
        return $this->belongsTo('Models\Domain\Domain', 'domain_id');
    }
}
