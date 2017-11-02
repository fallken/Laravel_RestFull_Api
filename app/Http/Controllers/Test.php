<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use App\Token;
use App\User;
use App\Post;
use Hamcrest\Thingy;
use Illuminate\Http\Request;

class Test extends Controller
{
    //lets first make a service container and add it to the controller then we will move on and try one of the requests in former api and add
    //use  it one here and see the changes
    protected $test;//define a protected variable
    public function __construct(TestService $service)
    {
        $this->test=$service;

    }

    public function index(){

            $parms=request()->input();
            $data=$this->test->getPost($parms['id']);

            if ($data) {
                return $this->jsonify(1,$data,0);
            }
            else {
                return $this->jsonify(0,0,1);
            }

    }
    public function like(){
            $parms=request()->input();
        $likes=$this->test->addLike($parms['id']);
        if ($likes){
            return $this->jsonify(1,$likes,0);
        }
        else {
            return $this->jsonify(0,0,3);
        }

    }
    public function disLike(){

            $parms=request()->input();
            $likes=$this->test->disLike($parms['id']);
            if ($likes){
                return $this->jsonify(1,$likes,0);
            }
        else{
            return $this->jsonify(0,0,3);
            }
    }
    public function cats(){

            $cats=$this->test->cats();
            if ($cats) {
                return $this->jsonify(1, $cats, 0);
            }
            else{
                return $this->jsonify(0, 0, 3);
            }

    }
    public function test(){
        $parms=request()->input();
        $postId=$parms['postId'];
        return response()->json($this->test->getCommentsNum($postId));

    }
    public function getComments(){//ill work on its security later not now . for now i will just define the functions and take tests from them

            $parms=[];
            $parms=request()->input();
            $data=$this->test->getComments($parms['id'],$parms['offset']);
            if ($data){
                return $this->jsonify(1, $data, 0);
            }
            else{
                return $this->jsonify(0, 0, 1);
            }


    }
    public function addComment(){
        $parms=[];
        $parms=request()->input();
        $targetId=isset($parms['target_id'])?$parms['target_id']:0;
        $data=$this->test->addComment($parms['user_id'],$parms['post_id'],$parms['comment_body'],$targetId);
        if ($data){
            $this->jsonify(1,$data,0);
        }
        else{
            $this->jsonify(0,0,3);
        }
}
    public function Search(){//i think i should be using ordinary strucrured data errors like the one used on
        //need to create another service container for handling errors and requests
        ///or ill pass the data and the error name to jsonifier and then the jsonifier will use the sevice container to get the error data and the will retun the error structure to me
        //i will add the offset section later.its fine
        $parms=[];
        $parms=request()->input();
        $word=isset($parms['word'])?$parms['word']:null;
        if (!is_null($word)){
            if (strlen($word)>=4){ //count the number of comments
                $posts = $this->test->searchPost($word);

                if ($posts) {
                    for ($i=0;$i<count($posts);$i++){
                        $posts[$i]['comments']=$this->test->getCommentsNum($posts[$i]['id']);
                    }
                    return $this->jsonify(1, $posts, 0);
                } else {
                    return $this->jsonify(0, 0, 9);
                }
            }else{
                return $this->jsonify(0,0,5);
            }
        }
        return $this->jsonify(0,0,8);
    }
    public function TopNewPosts(){//in close future i will also add the check if the cat id exists in the db and will generate responses according to that
        $parms=[];
        $parms=request()->input();
        $catId=null;
        $catId=(isset($parms['CatId'])&&is_numeric($parms['CatId'])&&!empty($parms['CatId']))?$parms['CatId']:null;
        $posts=$this->test->TopNewPosts($catId);
        for ($i=0;$i<count($posts);$i++){
            $posts[$i]['comments']=$this->test->getCommentsNum($posts[$i]['post_id']);
        }
        if ($posts){
            return $this->jsonify(1,$posts,0);
        }else
            return $this->jsonify(0,0,2);
    }
    public function MainPage(){//need to define a getCommentsNum function in testService
        $data['cats']=$this->test->cats();
        $data['slide_main']=$this->test->Slides();
        $data['most_viewed']=$this->test->TopNewPosts();
        $data['new_posts']=$this->test->NewPosts();
        for ($i=0;$i<count($data['most_viewed']);$i++){
            $data['most_viewed'][$i]['comments']=$this->test->getCommentsNum($data['most_viewed'][$i]['post_id']);
        }
        return $this->jsonify(1,$data,0);
    }
    /////list of local functions
    protected function jsonify($stat=0,$data=0,$errNo=null){
        //a function to create better structured json response
        $err= app('ErrorGen');//this will load the class in the services foler in App directory named ErrorGenerator
        return $err->errorMaker($stat,$data,$errNo);

    }

}
