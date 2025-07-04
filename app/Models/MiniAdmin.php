<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MiniAdmin extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $guard = 'mini_admin';

	protected $fillable = [
		'name', 'email', 'password', 'photo',
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
