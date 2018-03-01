<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // attributes that are mass assignable to use with ::create()
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
