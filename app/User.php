<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // the attributes that are mass assignable to use with ::create()
    protected $fillable = [
        'email', 'name',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
//    protected $hidden = [
//        'password',
//    ];
}
