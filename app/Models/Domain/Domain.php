<?php

namespace Models\Domain;

use Models\Model;

class Domain extends Model
{
    protected $table = 'domain';

    protected $attributes = [
        'ssl' => 0,
    ];

    protected $fillable = [
        'group_id',
        'domain',
        'deadline',
        'ssl',
        'supplier',
        'remark',
        'created_by',
        'updated_by',
    ];

    const SSL = [
        1 => '是',
        0 => '否',
    ];

    public function group()
    {
        return $this->belongsTo('Models\Domain\DomainGroup', 'group_id');
    }

    public function detects()
    {
        return $this->hasMany('Models\Domain\Detect', 'domain_id');
    }
}
