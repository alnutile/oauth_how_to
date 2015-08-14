<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Response;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

Route::get('/', function () {
    return view('welcome');
});


Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});


/**
 * Widget Angular hitting --> Uses Session
 * App --> This App --> Needs Oauth
 */
Route::get('test', ['middleware' => 'oauthOrAuth', function() {
    return Response::json("Welcome");
}]);