<?php
/**
 * Created by PhpStorm.
 * User: Elomir
 * Date: 11/3/2017
 * Time: 12:25 PM
 */

namespace App\Services;


use App\Activation;
use App\Token;
use App\User;


class UserService
{
public function isRegisteredUser($username){
$user=User::where('username',$username)->count();
    if ($user>=1)
        return true;
    else
        return false;
}
public function checkEamil($email){

}
public function isRegisteredEmail($email){
    $user=User::where('email',$email)->count();
    if ($user>=1)
        return true;

    else
        return false;
}
public function addUser($username,$email,$password,$name){
    //in to Users

    $data =  [
        "username" => $username,
        "email" => $email,
        "pw" => $password,
        "name" => $name
    ];
        $user=User::insertGetId($data);
    if (!$user)
        return false;

    $user_id = $user;
    $token = $this->rand_code(30);

    //Make New Token
    $info =[
        "token" => $token,
        "user_id" => $user_id,
        "time" => time()
    ];
    $insertToken=Token::insert($info);

    if (!$insertToken)
        return false;

    $activation_code =$this->rand_code(30);

    //Make new Activation Code
    $activeInfo =[
        "user_id" => $user_id,
        "type" => "email",
        "code" => $activation_code,
        "activated" => 0
    ];
    $insertActivation=Activation::insert($activeInfo);

    if (!$insertActivation)
        return false;

    return [
        "user_id" => $user_id,
        "username" => $username,
        "name" => $name,
        "pic" => 'test',
        "mail_activated" => 0,
        "token" => $token,
        "activation_code" => $activation_code
    ];

}
public function CheckActivationCode($activationCode){
    $code=Activation::where('code',$activationCode)->count();
    if ($code>=1)
        return true;
    else
        return false;
}
public function isActivate_Code($activationCode){
    $code=Activation::where('code',$activationCode)->update(['activated'=>1]);
    if ($code)
        return true;
    else
        return false;
}
public function  CheckRequest($request, $type = "free")
    {
        if( isset($request) && !empty($request) )
        {
            if($type == 'isInt')
                return (is_numeric($request) ? true:false);

            elseif($type == 'isEmail')
                return ( (!filter_var($request, FILTER_VALIDATE_EMAIL)) ? false:true );

            else
                return true;
        }
        else
            return false;
    }
public function login($username,$pass){
    $user=User::where('username',$username)->where('pw',$pass)->where('activations.type','email')->join('activations','users.id','=','activations.user_id')->
        select('users.id','users.username','users.name','users.pic','activations.activated as mail_activated')
        ->first();
    if(count($user)!=1){
        return false;
    }
    $token=$this->rand_code(30);
    $user_id=$user->id;

    $insertToken=$this->insertToken($token,$user_id,time());
    if (!$insertToken)
        return false;

    $user['token']=$token;
    return $user;

}
public function getUserFromToken($token){
    $user=array(Token::where('token',$token)->where('activations.type','email')->join('users','tokens.user_id','=','users.id')->
            join('activations','tokens.user_id','=','activations.user_id')->select('tokens.user_id','users.username','users.email','users.tel'
            ,'users.gender','users.age','users.height','users.weight','users.blood_type','users.pic','activations.activated as mail_activated')->first());

        if (count($user)!=1)
            return false;
                else
                    return $user;
}
public function isValidToken($token){
    $userToken=Token::where('token',$token)->first();
    if (count($userToken)!=1)
        return false;
    else
        return $userToken->user_id;
}
public function getUserInfo($userId){
    $user=User::where('id',$userId)->first();
    if (count($user)!=1)
        return false;
    else
        return $user;
}
public function deleteToken($token){
    $userToken=Token::where('token',$token)->delete();
    if ($userToken)
        return true;
    else
        return false;

}
public function rand_code($num){

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen( $chars );
        $str = "";
        for( $i = 0; $i < $num; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }
        return $str;
}
public function isActivatedEmail($email){
    $user=User::where('email',$email)->where('activations.activated','=',1)->join('activations','users.id','=','activations.user_id')
        ->first();
    if (count($user)!=1){
        return false;
    }else
        return true;
}
public function updateForgotPass($hash,$email){
    if (User::where('email',$email)->update(array('forget_pw_hash'=>$hash)))
        return true;
    else
        return false;

}
public function CheckForgetPwHash($hash){
    $user=User::where('forget_pw_hash',$hash)->select('id','name','email')->first();
    if (count($user)!=1)
        return false;
    else
        return $user;
}
public function randomPassword(){//generate a random code using the predefined array for using as an substitution for user password
    $list = [
        ["CgaYfw", "QXxs7OQs1KAnQdLHAZWC2g"],
        ["zvP9t0", "Zrx+HiR2cuDfJTq9GOUsZg"],
        ["mKBRhq", "MCygc3HLjMshbhtzqaetTA"],
        ["bSAKWq", "08sgtUlxdxanINRz+UEiDg"],
        ["JQ5kGh", "om8fxEuqBdHd+ylYrEl5BA"],
        ["lXUaV7", "yccjPD1YiWU1VzK2AM4fGA"],
        ["o5ybA8", "s84zqo4owCth9ZYLaHzfeg"],
        ["r0tUjT", "F/skT4FTy9dyLeYspTSc2A"],
        ["vvnIFw", "KZuWtaW4gW7y72PujQehpg"],
        ["mNq1u0", "P5qbdbtHt/JADiVNRiVbmA"],
        ["JDuF4r", "db54Eb1zjW4FcZdGV50o4Q"],
        ["yDp2pf", "jDkylNI/gZLaBMhsIHpioQ"],
        ["dvhmss", "wzV9NNo1qOTup1sY4LuUsQ"],
        ["SiBYrU", "4JGBHOseDSNV6NULpy3M5Q"],
        ["DEFyZA", "aKYwJwuipr+Zz8Srp3Qq2A"],
        ["M3FnvL", "xJNNb4uxAG1PmFci2nEYBw"],
        ["763xjm", "1fGtrHFh3Ymi3dWuKo5L6A"],
        ["8HJfZ6", "XYracIx6uGeoo4YHxzfR5g"],
        ["knk5AO", "1ixUhJj0NIvM29u21+RSXA"],
        ["nusaPG", "k1v9wl+D4Qls0NLC1r3bXg"],
        ["rwVutt", "+GqxxhxSMF2IJP7P3IzihA"],
        ["of17qN", "eMSo2HX4myt2Lu83wrl65w"],
        ["53Kv6n", "g0dOzuF811temnuRdsaqRA"],
        ["Ad4FFf", "GsF6pPKQin9PxFwbcsgpNg"],
        ["r1bsa3", "7PcXc/kllKCl+BJRg9zidA"],
        ["SbKPXh", "lFmriuOFAeqvP5wZkxo+Lg"],
        ["9X95Ow", "Vl9rKFh++mmTGCnqMlvJ/g"],
        ["FrJ8eH", "yianbEfjLMszSP3Xmva6tg"],
        ["mS3HGk", "txTcmRDXy1C5oIuVTrfSpA"],
        ["RMaCSM", "g392QDPUwMt5q6pQfRAcnw"],
        ["LjN4Wv", "n+zdR+StOO57+OOy3bbi3A"],
        ["RsHONj", "oc0Z/m7D1R5KUEr6DeTFbg"],
        ["tiNsfA", "6MDt+38JmsiDImqs3W9LBg"],
        ["AgvKoy", "RgDMstAAag+LH3NwykruCg"],
        ["bnWGLA", "qzfOm1AEHC2Z9Mu8SxuESA"],
        ["rudWBj", "WrokH7aS0wm333c1alR3HQ"],
        ["1zE082", "tZrAn0JdGw/BJAVb7YNePQ"],
        ["jU1sNR", "uaTi/l2hDx+fEvZzB+rBLw"],
        ["EpPdvZ", "cDMGNpOzBomRgVF6Q1wINQ"],
        ["CNxNTY", "2mFYq/XBCW6muGPyPW000g"],
        ["eVSzyX", "NlFlyaI/1Yvy6X2e4SrO8g"],
        ["gWX0iV", "XsUIaKsFW27s0KwHRSui0Q"],
        ["qDHSqY", "Dgi/3fYA4lN84K1d3iyYJg"],
        ["FDVBVw", "f7Fe3KCP5wP15cfImyfong"],
        ["oBIpMA", "og7FuT72hVz0LWwq+/luyg"],
        ["IDADb1", "9b1y3XPpW1kmn5bgT3V3bg"],
        ["LlOWMO", "PA6Hc0yvYw1me2IcsoTz1w"],
        ["PeBCXe", "DfuEmn1j/mk1qSrHU9dRmQ"],
        ["ISpL4a", "Y1TZzqwfurZs6rjAKNWCOw"],
        ["Ud9BGo", "kPnCC/bnCuEDXo6+Wg/dEQ"],
        ["UKqFOI", "tagX0YGs7BstGVdiUMh+OQ"],
        ["nRQ6ek", "3scZuzZL2YRM9hwr/kb02w"],
        ["tZfUFY", "SaHlq96HCTud3nUZBVkjGQ"],
        ["6WS92i", "fTLX0zxa6InQ5LG5WTLsVA"],
        ["67MhMY", "FVxNfFHuq3Q+s5ZHCJ/Plg"],
        ["wLTi04", "4lGnove8A/VSwDYKPpAylQ"],
        ["Ubq4Oe", "Omk3YkIvXQ//z3j9smzPaw"],
        ["x42ywX", "C4NoI9jQ0ESpPyVWH7qqIQ"],
        ["AH72VO", "mB83CdDL7TTiM3H0p9a/Zg"],
        ["xbZrvI", "CZraIOnmV2hZHn3nYZDIWQ"],
        ["hIeuLd", "PkOm8tWJAV6+ytr/wge+Pw"],
        ["sRT1mE", "eY+YkhI3tN1vFGbpRIevWQ"],
        ["EKHcut", "31vgXH5nZGFibeqeXew6dQ"],
        ["GnoO0w", "rqJuyKKCtaWjEzMFN9V+cg"],
        ["PPUsL9", "gJe6s3yShGDMR1PSycVeWA"],
        ["7xFPPB", "KvSRmjrw/s6iyl+YNe4/6g"],
        ["vvM1Ke", "UVF/TtDJv3Z0NFEaScFe3w"],
        ["bBmDKI", "wV+lvnASe+GjLK39GTq3+g"],
        ["OMHb4i", "pjkXgqToJ0U6F+ji5uj0BQ"],
        ["LmCea6", "lztg+H6/rPQseMJIBNalJQ"],
        ["pdBJVk", "7XttQiPo1NOs1XOnzeeBrg"],
        ["i67aYy", "IKeFjKSQVwMu8otdz7ARsA"],
        ["tPRVWT", "Z0yU9gtBB1CcvRFV0sBfmQ"],
        ["FA0tCm", "wonZ7JdiAHA9NMbndN/z3w"],
        ["D9pAh0", "vS/xCVQgcDIOuMaQnYHS+A"],
        ["h7WFXi", "GFItJ9Yg0q+14TH58Ye6zA"],
        ["3cDXBd", "9lXoqHbKGHt+QXCCFV2qCA"],
        ["3s4NqD", "45FolU3u49PX7V4q1K5Vaw"],
        ["oxsQ4w", "+qECDwzmmXM5RmNnt1MU+g"],
        ["oU3nVb", "cC+J+rDHTKspE1RRCYyG8Q"],
        ["Oahvku", "/AGz5YoUF/gnAC0zHRtVrA"],
        ["be3rRc", "pdlXuEtbg8tcC2YEVzCgSQ"],
        ["YD0Kx8", "u7VvGECm/umjXWBDrTK6Ag"],
        ["RIJx8t", "cns2hDEWdVIONPMHPuWWow"],
        ["1X3PmY", "rKYxEkQe5VkMCEu1CawaKA"],
        ["j9eGah", "uvwbD5e/Ku46OIEhr7oOxg"],
        ["BGiyW4", "nbBx+a+vj2QFmoXGY7asPg"],
        ["XOdqQp", "oxo2xp/QEVuGtpefJ8k6cQ"],
        ["TQabWV", "bK7i/FHsk+WpuExYmttEXw"],
        ["GtPLz8", "sM5ILBMmySDqevea8s1IIQ"],
        ["xXAvwA", "xQQg3mu6YiogZHuW/QeK5Q"],
        ["UqKghe", "s92uAeSC3Uu/oqOnsLPqWQ"],
        ["H3VNkA", "mgMueKvb+Rib698A2JrQMw"],
        ["xr7Vm0", "dUShRd0fiKqWK/SjakBr6Q"],
        ["1G6QxX", "jHfb2cgwErbxVIxp6wFQyQ"],
        ["ZNAjId", "f3XbrifxUBljXa9MCpAxkg"],
        ["GRxWMd", "+pqIRZ/0VAGPHCInHLtrxw"],
        ["8ikqL9", "Dfr01GLS6VtoU8n57xbKcw"],
        ["k87Qdo", "Gy/gp86ZoQ+Apmi7nC9S5Q"],
        ["qdZPoB", "bWNbpfa/hQyDfHCsGyriFA"],
        ["6vgRw2", "Fw/63YO9N7nZ9XMQuuhqaA"],
        ["JjIw2P", "TVwZqfJ+SiLV2WfAdRdKyg"],
        ["FxSKQb", "3KKcj0XWheJP01Hmw8S9ng"],
        ["tTb3zD", "2TACTdQYKfPv3D2MF+XE7g"],
        ["cemNPk", "p3vMtTKJKKcMhT7r2j3E+Q"],
        ["zJxrqh", "ihAGKFv2GJLUzzP25TwK8w"],
        ["k9CMDx", "ODmdQiMzPd2oQfkp+Hjgjw"],
        ["p6reuy", "vz8KVPSiC4upIllTJ5HWww"],
        ["di40Vl", "sQ+kbMuPAzAcNeJ830G2IQ"],
        ["TN2raK", "3LRLXJgGqMPSjsqplKrjqQ"],
        ["xdCeRK", "CZHNL4M9WVN3u5bx8hRsXw"],
        ["r50CoD", "TXvDUIl+5+ZywDZhRT4T7g"],
        ["qWEK5i", "KsS9Ara2xvDZ3EfL1VwJug"],
        ["YKtNxu", "9CBBcXusom+yXhtuKx5xhQ"],
        ["y06kL2", "evDlyHT0fgKFNf2+WdNcaQ"],
        ["R57tx6", "21kocIdY9ocPd6zXyiBqAg"],
        ["3qmfyt", "kuOClmk8+Zt6mwIffXRDbQ"],
        ["1R5IML", "4uFGdmq2IMIWx34hQMpCvg"],
        ["CKkjBF", "IORrN2kOsATaz4UJeCJPKA"],
        ["Sm23be", "MGLB5D2E2dewG+AcjSMZTA"],
        ["n0qMA7", "Efp/qxRbshlZGl5Zo8E4XQ"],
        ["km8u8N", "kQ8etbJQfJU5KUjLil7nXg"],
        ["ZIxsCS", "QWqGlFdHwFU3NNElq+v+5w"],
        ["M90zyf", "HjAZWPmD1FSuAU83jJK6AA"],
        ["CU6Aps", "mWZP3pb75Y01xtAXjCwHNw"],
        ["8wQlRR", "Muo9CBzpsPnpA81V+4ZHQw"],
        ["U80Z4V", "+0iTk+7QMtk88xDeqghOCQ"],
        ["NJijVq", "hHRBkTUcjSYF8fS7bhSOnw"],
        ["2oEVhM", "bmmdWvz+JLzk5tm5HVdxnA"],
        ["Fzuwcc", "sbkUcOrZ8eeaL4/d1xqhPw"],
        ["EgXbc3", "WZ78RV3o266CCnimIyCBUg"],
        ["ak7vyZ", "HFxjdh9Qh2nMgulgQ9CsuQ"],
        ["TJj1ti", "ud3m9qwKpgjFb7DnF4tX+A"],
        ["cLkio4", "i5xQPUUtbDPIjPTOY3I2cw"],
        ["qlEgfw", "Wwhi0AFMnwXwx8Grg4JnvA"],
        ["acLqEd", "fA1cCFvYCqhPMomDhrNSAg"],
        ["q8m9dh", "fz1Y1ZyaMPhJSrzJaqEjcQ"],
        ["YnAUJH", "3mhaxsBQPFU1erReZh6pSg"],
        ["wEkuuz", "YvM6455bEmv9sDP0vbii3A"],
        ["1Asivk", "BcffMwLdGSpTF24HTAWxGQ"],
        ["TT0yKY", "prhbgOUzVa4c24a2sRhU8A"],
        ["eCwBpm", "1aRoqcs+S4x/Rj+rV+DbKQ"],
        ["apg79t", "7j4uQ4TWayvTYCgyzgg7yQ"],
        ["0KsnlA", "HPRiDkwKFZ9rq7ahOO8rWQ"],
        ["wewtlC", "fkhbWuy5cOLRHNTc3sziNw"],
        ["fLoDIi", "nzJRwQyXm8j2HZXFHl4J3g"],
        ["CSGpvr", "nIon2auP2SXJqaSOmexSxw"],
        ["lW0Vrf", "DsnnxOX163ohzdu8HupXLg"],
        ["Bpabfu", "w/aOLpFgR5QAvRyKIYQJDw"],
        ["f3rfTK", "D9igOR2tB4zbb5x/YCVkRg"],
        ["7jQAML", "9hegTPzbuqSHljcuRVymog"],
        ["jpma0z", "CMkfj50l/UGojG3R/cXc5w"],
        ["lFaTIO", "XKIpmLEPnGTMa0bjgS2ArA"],
        ["uKVIbY", "9SKBZC/pWYK6rwLADWkqIA"],
        ["WX0UeE", "7dMNX7BIdUnisGqpJ7259w"],
        ["9oLokM", "VhNaiLIdJWZ7fCPfsb2B4Q"],
        ["2fMTYQ", "BPUDjvcA4lshLuZfIB4KUA"],
        ["d64fQe", "jnc0OAPbg0hUXG/EfT26eQ"],
        ["i4pD7o", "S73Lhd5H78friZJe5SqgfA"],
        ["69uOE8", "ZqlEBeTZgOKrsSv6Q4RV+A"],
        ["TrgpiQ", "sXAJk1FR22ARjFP7E3P1og"],
        ["Hla77K", "IsIFxK0uIErsQ3iStBMOcw"],
        ["tSi0Gd", "rz+b4KFraJaSQyOlte3M6Q"],
        ["pWLHcT", "G4U2LmPB0CBi4yD5nwd1jQ"],
        ["brSHU2", "1Y81iiQQL81g5aWTix97og"],
        ["aJqAgN", "ZsELH07Dnpk+5JL2cGoytA"],
        ["GFVham", "4Z1K/fTtulzlsOz9KervdA"],
        ["lZPifi", "fGF8vLcPActNtDsqhEaJSQ"],
        ["9yQ15I", "5VS2MWZI157782RwiU3WPA"],
        ["Sv7SxC", "aWHZVI2R7YVtGQC2XnfveA"],
        ["dEyHQ7", "d3FpNcRN3NX39tCReIxBBA"],
        ["9zXR2A", "RlVjCxvEwKuoZjyVntb4Tw"],
        ["3ZrB45", "d2sYYHvH4ucQdoaQtQN3SQ"],
        ["4Xvg9t", "Krg2UOX3+gxksWYxiOFbBQ"],
        ["i6pwWG", "s6T0ZzwlSR2XiLGtiASkFg"],
        ["3VoDO2", "jJlWP50iWLFI3IQ39mg7fg"],
        ["80TI7q", "4KTeQ853zNEJGXr2znHIBg"],
        ["1hOZpA", "fWPRyCCigUCs9Ce1mee2fA"],
        ["bgMZJm", "wkbPeqJov1SKAB8lE7Z76g"],
        ["lioRQT", "Y+buSbKxcj5XQcXuCjEKWw"],
        ["DStBsZ", "BJNaSnnLDfW7P2ik4IL8Ig"],
        ["XLiiwe", "s9QXKRU1mWGdbq3XjOEhfA"],
        ["8nSlrm", "gztU44CIuVWLdWDY3C4dhw"],
        ["fXbNlo", "Y51H7Cg6+VLIpCJ8fvNpLQ"],
        ["POpvlZ", "JKPAwZZW0ND1m3dGQOy4Fg"],
        ["8EdqKE", "J6k5cbev3pd/LSPxRshioQ"],
        ["nIYRkW", "h5vax/RQNZ4pb4f38h8NSA"],
        ["182HBN", "NHReDNgjE6N/eQKoRKGwlA"]
    ];
    $rand_code = rand(0, 187);

    return $list[$rand_code];
}
public function updateNewPassword($userId,$newPass){
    $user=User::where('id',$userId)->update(array('forget_pw_hash'=>'','pw'=>$newPass));
    if ($user)
        return true;
    else
        return false;
}
public function updateUserInfo($info,$userId){
    $update=User::where('id',$userId)->update($info);
    if ($update)
        return true;
    else
        return false;
}


protected function insertToken($token,$userId,$time){
        $tonkeInsert=Token::insert(array('token'=>$token,'user_id'=>$userId,'time'=>$time));
        if($tonkeInsert)
            return true;
        else
            return false;
}
}