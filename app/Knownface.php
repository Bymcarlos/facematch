<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Knownface extends Model
{
	protected $table = 'fs_knownface';
	public $timestamps = false;

    protected $fillable = [
        'img_name', 'gender', 'age', 'quality', 'person_id', 'height', 'width'
    ];
}
