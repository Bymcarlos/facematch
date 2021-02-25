<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telegramuser extends Model
{
    protected $table = 'fs_telegramuser';
	public $timestamps = false;

    protected $fillable = [
        'firstName','lastName','telegramID','isBot','trusted','numTrials'
    ];
}
