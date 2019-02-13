<?php

namespace App\Http\Controllers\Api\Design;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class DemoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     *@$min,$max,$num,$reduplicatable
     */
    public function getRandomNums(Request $request){
        $min = $request['min'];
        $max = $request['max'];
        $num = $request['num'];
        $reduplicatable = $request['reduplicatable'];

        $data = array();
        if($num > ($max-$min+1) && $reduplicatable == 0){
            $this->renderFailJson('','411','num数量不对');
        }
        set_time_limit(0);
        if($reduplicatable == 0 ){
            $data =$this->unique_rand($min,$max,$num);
        }else{
            while(count($data) < $num){
                $data[] =mt_rand($min,$max);
            }
        }


        $res['rand_num'] = array_values($data);
        $res['count'] = $num;

        $this->renderSuccessJson($res);

    }


    function  unique_rand($min,$max,$num){
        $count = 0;
        $return_arr = array();
        while($count < $num){
            $return_arr[] = mt_rand($min,$max);
            $return_arr = array_flip(array_flip($return_arr));
            $count = count($return_arr);
        }
        shuffle($return_arr);
        return $return_arr;
    }

}
