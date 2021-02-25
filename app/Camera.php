<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $table = 'fs_camera';
	public $timestamps = false;

    protected $fillable = [
        'camera_model','rtsp_url'
    ];
}
