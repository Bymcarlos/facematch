<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vagidentified extends Model
{
    protected $table = 'fs_vagrancyidentified';
	public $timestamps = false;

    protected $fillable = [
        'confirmed','body_id','firstBody_id'
    ];
}
