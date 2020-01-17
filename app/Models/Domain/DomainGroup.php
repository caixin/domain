<?php

namespace Models\Domain;

use Models\Model;

class DomainGroup extends Model
{
    protected $table = 'domain_group';

    protected $attributes = [
        'target_id' => 0,
        'status'    => 1,
    ];

    protected $fillable = [
        'name',
        'path',
        'verify_path',
        'target_id',
        'mode',
        'value1',
        'value2',
        'value3',
        'value4',
        'sort',
        'status',
        'created_by',
        'updated_by',
    ];

    const MODE = [
        1 => '检查码',
        2 => 'CSS检查字串',
        4 => 'HTML检查字串1',
        8 => 'HTML检查字串2',
    ];

    const STATUS = [
        0 => '关闭',
        1 => '启用',
    ];

    public function domains()
    {
        return $this->hasMany('Models\Domain\Domain', 'group_id');
    }

    public function target()
    {
        return $this->belongsTo('Models\Domain\DomainGroup', 'target_id');
    }
}
