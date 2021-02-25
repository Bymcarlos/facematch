<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brightsignfag extends Model
{
    protected $table = 'fs_brightsignfemaleagegroup';
	public $timestamps = false;

    protected $fillable = [
        'minFemaleAge','maxFemaleAge','udpCommand','femaleEmotion'
    ];
}
