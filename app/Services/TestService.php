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
use App\PostCat;
use App\Token;
use App\User;


class TestService{
    public function getPost($postId){
        if (Post::find($postId)){
            $this->addView($postId);
            return $this->filterPosts(Post::where('id',$postId)->get());
        }

    }
    public function addLike($id){
         Post::find($id)->increment('likes');

         $data=Post::find($id);

            return $data->likes;


    }
    public function disLike($id){
        Post::find($id)->decrement('likes');
        $data=Post::find($id);
        return $data->likes;

    }
    public function cats(){
       return PostCat::all();
    }
    public function getComments($id,$offset=null){//works like charm.setting default value for the offset its gonna be alright
        $offset-=1;
        $offset=$offset*10;
        if (is_null($offset)||$offset==0){
            $data=$this->filterComments(Comment::where('post_id',$id)->take(10)->get());
        }
        else{
            $data=$this->filterComments(Comment::where('post_id',$id)->skip($offset)->take(10)->get());
        }
        return $data;

    }
    public function addComment($token,$post_id,$body,$target_id=0){
       $userId=$this->getUserFromToken($token);

      return Comment::insert(array('user_id'=>$userId,'post_id'=>$post_id,'comment_body'=>base64_encode($body),'status'=>0,'target_id'=>$target_id,'date'=>time()));
    }
    /////protected functions
    protected function addView($id){//shoud set timestaps false if u dont want to have timestamps  errors
        Post::find($id)->increment('views');//fk this method is making errors for the update it also wants the updated at let do another method
    }


    protected function filterPosts($resultS){

        $data=[];

        foreach ($resultS as $result){
            $entry=[
                'id'=>$result->id,
                'title'=>trim($result->title),
                'views'=>$result->views
            ];
            $entry['comments']=$this->filterComments(Post::find($result->id)->comments->take(5));
            $data[]=$entry;
        }
        return $data;
    }
    protected function filterComments($comments)
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
    protected function filterUsers($users){//this function will only get one result so
        $data=[];

                $entry = [
                    'userName' => $users->username,
                    'Name' => $users->name,
                    'userImg' => $users->pic
                ];
                $data[] = $entry;

            return $data;
        }
    protected function getUserFromToken($token){
       $tokenId= Token::where('token',$token)->first();
        return $tokenId->user_id;
    }


}