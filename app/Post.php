<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $text
 * @property string $time
 * @property int $cat_id
 * @property int $views
 * @property int $likes
 * @property string $img
 */
class Post extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['title', 'text', 'time', 'cat_id', 'views', 'likes', 'img'];

}
