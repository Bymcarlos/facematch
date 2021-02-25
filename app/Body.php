<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Body extends Model
{
    protected $table = 'fs_body';
	public $timestamps = false;

    protected $fillable = [
        'topLeftX','topLeftY','width','height','topColor','bottomColor','probability','frame_id'
    ];
}
