<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Identified extends Model
{
    protected $table = 'fs_identified';
    public $timestamps = false;

    protected $fillable = [
        'face_id','person_id','confirmed','known_face_id'
    ];
}
