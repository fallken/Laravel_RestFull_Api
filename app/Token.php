<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $token
 * @property int $user_id
 * @property string $time
 */
class Token extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['token', 'user_id', 'time'];
public function user(){
    return $this->belongsTo('App\User','user_id','id');
}
}
