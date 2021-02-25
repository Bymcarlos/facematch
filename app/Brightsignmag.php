<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brightsignmag extends Model
{
    protected $table = 'fs_brightsignmaleagegroup';
	public $timestamps = false;

    protected $fillable = [
        'minMaleAge','maxMaleAge','udpCommand','maleEmotion'
    ];
}
