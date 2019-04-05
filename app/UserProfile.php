<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name','last_name','company','birth_date','gender',
        'phone','cell_phone','document_type','avatar','nickname','user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function user() {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }

}