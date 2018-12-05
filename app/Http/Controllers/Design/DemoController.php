<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;

class DemoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
    public function getRandomNums($min,$max,$num,$reduplicatable){

        return 123;

    }
}
