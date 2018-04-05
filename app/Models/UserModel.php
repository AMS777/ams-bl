<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Model implements JWTSubject, AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // attributes that are mass assignable to use with ::create()
    protected $fillable = [
        'email', 'name', 'password', 'remember_token', 'verify_email_token',
    ];

    // attributes not returned when the table is accessed
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
