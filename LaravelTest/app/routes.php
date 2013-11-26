<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::any('entrance','SSOController@entrance');

Route::resource('users', 'UsersController');

Route::get('/',function(){
   
    session_start();
    echo session_id();

    $callback_address = 'http://'.$_SERVER['HTTP_HOST'] 
                    .str_replace('index.php','',$_SERVER['SCRIPT_NAME']) 
                    .'callback.php'; //callback地址用于回调设置cookie 
     echo $callback_address;
     echo "<br>";
     echo $_SERVER['HTTP_HOST'];
     echo "<br>";
     echo $_SERVER['SCRIPT_NAME'];
});