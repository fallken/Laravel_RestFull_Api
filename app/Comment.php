<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $user_img
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
    protected $fillable = ['user_id', 'user_img', 'status', 'post_id', 'comment_body', 'date', 'answer', 'target_id'];

}
