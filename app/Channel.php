<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
	protected $table = 'fs_channel';
	public $timestamps = false;

    protected $fillable = [
        'username','password','ip','portNum','cameraNum','description','maxFaces','enabled','camera_id','minFaceSize','matchPercentage','maxFaceSize','trackerQuality','usages','coordinates','channel_type','names'
    ];
}
