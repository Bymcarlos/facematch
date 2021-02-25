<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nonfaceidentified extends Model
{
    protected $table = 'fs_nonfaceidentified';
	public $timestamps = false;

    protected $fillable = [
        'confirmed','topColor','bottomColor','height','probability','topLeftX','topLeftY','width','frame_id'
    ];
}
