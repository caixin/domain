<?php

namespace Models\Domain;

use Models\Model;

class Node extends Model
{
    protected $table = 'node';

    protected $fillable = [
        'name',
        'server_ip',
        'created_by',
        'updated_by',
    ];
}
