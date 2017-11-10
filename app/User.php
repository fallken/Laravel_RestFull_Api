<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $username
 * @property string $pw
 * @property string $name
 * @property string $email
 * @property string $tel
 * @property int $gender
 * @property int $age
 * @property int $height
 * @property int $weight
 * @property string $forget_pw_hash
 * @property int $blood_type
 * @property string $pic
 */
class User extends Model
{
    /**
     * @var array
     */
//    its working dude
    protected $fillable = ['username', 'pw', 'name', 'email', 'tel', 'gender', 'age', 'height', 'weight', 'forget_pw_hash', 'blood_type', 'pic'];
    public $timestamps=false;
    public function comments(){
    return $this->hasMany('App\Comment');
}
public function tokens(){
    return $this->hasMany('App\Token');
}
}
