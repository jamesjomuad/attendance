<?php

use Illuminate\Support\Facades\Hash;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    // return $router->app->version();
    return view('app');
});

// API
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('users',  'UserController@index');
    $router->get('user/{id}', 'UserController@show');
});

$router->get('/test', function () use ($router) {
    dd(
        Hash::make('123456')
    );
});
