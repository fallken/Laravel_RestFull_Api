<?php
/**
 * Created by PhpStorm.
 * User: Elomir
 * Date: 10/28/2017
 * Time: 9:31 AM
 */
namespace App\Services;
use App\Comment;
use App\Main;
use App\Post;
use App\PostCat;
use App\Token;
use App\User;


class TestService{
    public function getPost($postId){
        if (Post::find($postId)){
            $this->addView($postId);
            return $this->filterPosts(Post::where('id',$postId)->get());
        }else
            return false;

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
    public function searchPost($word){
        $post=Post::where('title','LIKE',"%$word%")->take(10)->get();
            if (count($post)>=1){
                return $this->filterPosts($post,1);
            }else{
                $word=urlencode($word);
                $post=Post::where('text','LIKE',"%$word%")->take(10)->get();
                if (count($post)>=1){
                    return $this->filterPosts($post,1);
                }
            }

    }
    public function TopNewPosts($catId=null){//i will have to make sure its working correctly . well in my tests it seems to be working like charm
        if (!is_null($catId)){
            $posts=$this->getCommentsForTopNewPosts(Post::where('posts.cat_id',$catId)->where('posts.time','>',time()-604800)->join('post_cats','posts.cat_id','=','post_cats.id')
                ->select('posts.id as post_id','posts.title','posts.time','posts.cat_id','posts.time','posts.views','posts.likes','posts.img as post_img',
                    'post_cats.img as cat_img','post_cats.name as cat_name')
                ->orderBy('views','desc')->limit(10)
                ->get());
        }else{
            $posts=$this->getCommentsForTopNewPosts(Post::where('posts.time','>',time()-9072000)->join('post_cats','posts.cat_id','=','post_cats.id')
                ->select('posts.id as post_id','posts.title','posts.time','posts.cat_id','posts.time','posts.views','posts.likes','posts.img as post_img',
                    'post_cats.img as cat_img','post_cats.name as cat_name')
                ->orderBy('views','desc')->limit(10)
                ->get());
        }

        return $posts;

    }
    public function Slides(){
        $result["slides"]=[];
        $result["main_image"]=[];
           $slides= Main::where('var','LIKE','slide%')->where('var','NOT LIKE','slide_count')->get();
           $mainImages=Main::where('var','Like','main_image%')->orderBy('id','asc')->get();
           if (count($slides)>=1){
               $result["slides"]=$this->filterSlides($slides);
           }
           if (count($mainImages)>=1){
               $result["main_image"]=$this->filterSlides($mainImages);
           }
           return $result;
    }
    public function NewPosts($catId=null){
        $data = $this->cats();
        $info=[];
        $entry = [];
        foreach ($data as $cat){
            $entry[ $cat->id]= $this->filterPosts(Post::where('posts.cat_id',$cat->id)->orderBy('time','desc')->take(10)->get(),1);
            $info=$entry;
        }
        return $info;
    }
    public function getCommentsNum($postId){
        $comments=Comment::where('post_id',$postId)->count();
        return $comments;
    }
    public function getUserFromToken($token){
        $tokenId= Token::where('token',$token)->first();
        return $tokenId->user_id;
    }
    public function getUserIdFromCommentId($commentId){
        $comment = Comment::where('id',$commentId)->get();
        return $comment->user_id;
    }
    public function getUser($senderId){
        $user = User::where('id',$senderId)->get();
        return $user;
    }

    /////protected functions
    protected function addView($id){//shoud set timestaps false if u dont want to have timestamps  errors
        Post::find($id)->increment('views');//fk this method is making errors for the update it also wants the updated at let do another method
    }
    protected function filterPosts($resultS,$stat=0){//stat=0 for ordianary processing like get post func but stat 1 for  func like Search which does no t need more

        $data=[];

        foreach ($resultS as $result){
            $entry=[
                'id'=>$result->id,
                'img'=>$result->img,
                'title'=>trim($result->title),
                'cat_id'=>$result->cat_id,
                'time'=>$result->time,
                'views'=>$result->views,
                'likes'=>$result->likes,
                'comments'=>(string)$this->getCommentsNum($result->id)
            ];
            if ($stat==0){
            $entry['comments']=$this->filterComments(Post::find($result->id)->comments->take(5));
            }
            $data[]=$entry;
        }
        return $data;
    }
    protected function getCommentsForTopNewPosts($results){
        foreach ($results as $result):
                $result['comments']=(string)$this->getCommentsNum($result->post_id);
        endforeach;
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

    protected function filterSlides($slides){
        $data=[];
            foreach ($slides as $slide){
                $entry=[
                'img'=>$slide->val1,
                'desc'=>$slide->val2,
                'link'=>$slide->link,
                'type'=>$slide->type
                ];
                $data[]=$entry;
            }
            return $data;
    }



}
