<?php

namespace Models\System;

use Models\Model;

class ApiLog extends Model
{
    const UPDATED_AT = null;

    protected $table = 'api_log';

    protected $fillable = [
        'url',
        'route',
        'param',
        'return_str',
        'exec_time',
        'ip',
    ];
}
