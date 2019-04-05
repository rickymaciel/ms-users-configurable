<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Exception;


class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use EntrustUserTrait;

    // Status user
    const BANNED = 0;
    const ACTIVE = 1;
    const CONFIRMED = 1;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'document_number', 'id', 'confirmed', 'banned', 'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
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

    public function profile() {
        return $this->hasOne('App\UserProfile');
    }



    /**
     * Retrieving user data from token.
     *
     * @return user
     */
    public static function getUserFromToken()
    {
        try {
            if (!$user = JWTAuth::user()) {
                throw new Exception('user_not_found', 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            throw new Exception('token_expired', $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            throw new Exception('token_invalid', $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            throw new Exception('token_absent', $e->getStatusCode());
        }

        return $user;
    }



    /**
     * Retrieving user data.
     *
     * @return data
     */
    public function getUserData()
    {
        $user = $this;
        $user->profile;
        $role = $this->roles()->get()->first();

        return [
            'user' => $user,
            'role' => $role,
            'permissions' => $role->permissions()->get()
        ];
    }



    /**
     * Retrieving premissions.
     *
     * @return void
     */
    public function getPermissions()
    {
        return $this->roles()->get()->first()->permissions()->get();
    }
}