<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vagfloorarea extends Model
{
    protected $table = 'fs_vagrancyfloorarea';
	public $timestamps = false;

    protected $fillable = [
        'channel_id','weekdayEndTime','weekdayStartTime','weekendEndTime','weekendStartTime','coordinates','names'
    ];
}
