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
    public function getTokenAttribute($value){
        return (string)$value;
    }
    public function getUserIdAttribute($value){
        return (string)$value;
    }
    public function getAgeAttribute($value){
        return (string)$value;
    }
    public function getGenderAttribute($value){
        return (string)$value;
    }
    public function getHeightAttribute($value){
        return (string)$value;
    }
    public function getWeightAttribute($value){
        return (string)$value;
    }
    public function getMailActivatedAttribute($value){
        return (string)$value;
    }
    public function getBloodTypeAttribute($value){
        return (string)$value;
    }
}
