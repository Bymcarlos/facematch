<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brightsign extends Model
{
    protected $table = 'fs_brightsigndevice';
	public $timestamps = false;

    protected $fillable = [
        'ip','udpPort','maxFemaleGroupNumber','maxMaleGroupNumber','staffUdpCommand','vipUdpCommand','channel_id'
    ];
}
