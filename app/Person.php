<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
	protected $table = 'fs_person';
	public $timestamps = false;

    protected $fillable = [
        'person_type', 'info', 'address','date_created','description',
    ];
}
