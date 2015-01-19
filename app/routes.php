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
/**
 * www.learnlaravel.com:8888
 */
Route::get( '/', function() {
        //dump function
        //dd('foo');
        ad( '3' );
        ad( '30.33' );
        ad( with( new stdClass )->foo = 'bar' );
        ad( ['name' => 'zhangsan', 'age' => 14] );

        //timers
        Anbu::timers()->start( 'test' );
        sleep( 1 ); // Do something interesting.
        Anbu::timers()->end( 'test', 'Completed doing something.' );

        //db query
        \DB::table( 'anbu' )->get();

        //log entries
        \Log::info( 'info message' );
        \Log::info( 'another message' );
        \Log::error( 'wrong message' );


        return View::make( 'hello' );
    } );

Route::get( 'admin/logout', array( 'as' => 'admin.logout', 'uses' => 'App\Controllers\Admin\AuthController@getLogout' ) );
Route::get( 'admin/login', array( 'as' => 'admin.login', 'uses' => 'App\Controllers\Admin\AuthController@getLogin' ) );
Route::post( 'admin/login', array( 'as' => 'admin.login.post', 'uses' => 'App\Controllers\Admin\AuthController@postLogin' ) );

Route::group( array( 'prefix' => 'admin', 'before' => 'auth.admin' ), function() {
        Route::any( '/', 'App\Controllers\Admin\PagesController@index' );
        Route::resource( 'articles', 'App\Controllers\Admin\ArticlesController' );
        Route::resource( 'pages', 'App\Controllers\Admin\PagesController' );
    } );

Route::get( 'monkey', function() {

    header("Content-type: text/html; charset=utf-8"); 
    $a=['北\京'=>['a',123=>[] ],'热门'];
     
    echo json_encode($a, JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE );echo '<br/>';
    echo json_encode($a, JSON_UNESCAPED_UNICODE );
    exit; 
    $str=file_get_contents('c:/demos/1.txt');

    $json=json_decode($str,true);
    print_r($json);

    echo json_last_error_msg();
    
});