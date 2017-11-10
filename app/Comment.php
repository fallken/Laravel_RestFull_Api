<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property int $post_id
 * @property string $comment_body
 * @property string $date
 * @property int $answer
 * @property int $target_id
 */
class Comment extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'status', 'post_id', 'comment_body', 'date', 'answer', 'target_id'];
    public $timestamps=false;
    public function post(){
        return $this->belongsTo('App\Post','post_id','id');//the second argument is the local and the second is foreign i stil wonder if
        //it is the same on hasMany relationship or not
    }
    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
