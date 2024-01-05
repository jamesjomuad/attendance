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
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('login',  'AuthController@login');
    });


    // User
    $router->get('/users', 'UserController@index'); // Show all items
    $router->get('/users/{id}', 'UserController@show'); // Show a specific item
    $router->post('/users', 'UserController@store'); // Create a new item
    $router->put('/users/{id}', 'UserController@update'); // Update an item
    $router->delete('/users/{id}', 'UserController@destroy'); // Delete an item

});


$router->get('/{any:.*}', function () use ($router) {
    return view('errors.404');
});
