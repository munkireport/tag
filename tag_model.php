<?php

use munkireport\models\MRModel as Eloquent;

class Tag_model extends Eloquent
{
    protected $table = 'tag';

    protected $fillable = [
        'serial_number',
        'tag',
        'user',
        'timestamp',
    ];

    public $timestamps = false;
}
