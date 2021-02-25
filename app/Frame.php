<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    protected $table = 'fs_frame';
	public $timestamps = false;

    protected $fillable = [
        'img_name','captureTime','channel_id'
    ];
}
