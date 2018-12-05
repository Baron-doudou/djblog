<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * [返回json信息 description]
     * @param  [type] $errNO  [description]
     * @param  [type] $errMsg [description]
     * @return [type]         [description]
     */
    public function renderSuccessJson( $psResult,$errno='0',$errMsg='success' )
    {

        $result = array(
            'errno' => $errno,
            'errmsg' =>$errMsg,
            'data' => $psResult,
            'timestamp' => time(),
        );
        echo json_encode($result);exit;
    }

    public function renderFailJson( $psResult,$errno='1',$errMsg='fail' )
    {

        $result = array(
            'errno' => $errno,
            'errmsg' =>$errMsg,
            'data' => $psResult,
            'timestamp' => time(),
        );
        echo json_encode($result);exit;
    }



}
