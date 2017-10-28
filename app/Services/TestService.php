<?php
/**
 * Created by PhpStorm.
 * User: Elomir
 * Date: 10/28/2017
 * Time: 9:31 AM
 */
namespace App\Services;
use App\Comment;
use App\Post;


class TestService{
    public function getPost($postId){

        return $this->filterPosts(Post::where('id',$postId)->get());
    }
    public function filterPosts($resultS){

        $data=[];

        foreach ($resultS as $result){
            $entry=[
                'id'=>$result->id,
                'title'=>trim($result->title),
                'views'=>$result->views
            ];
            $entry['comments']=$this->filterComments(Post::find($result->id)->comments);
            $data[]=$entry;
        }
        return $data;
    }
    public function filterComments($comments)
    {
        $data=[];
        foreach ($comments as $comment){
            $entry=[
                'content'=>trim($comment->comment_body),
                'userId'=>$comment->user_id,
                'date'=>$comment->date
            ];
            $entry['userInfo']=$this->filterUsers(Comment::find($comment->id)->user);
            $data[]=$entry;
        }
        return $data;

        }//now its been fixed
    public function filterUsers($users){//this function will only get one result so
        $data=[];

                $entry = [
                    'userName' => $users->username,
                    'Name' => $users->name,
                    'userImg' => $users->pic
                ];
                $data[] = $entry;

            return $data;
        }


}