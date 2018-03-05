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
        'email', 'name', 'password',
    ];

    // attributes not returned when accesed the table
    protected $hidden = [
        'password',
    ];
}
