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

    // Dashboard Statistics
    $router->get('/statistics', 'StatisticController@index');

    // User
    $router->get('/users', 'UserController@index'); // Show all items
    $router->get('/users/{id}', 'UserController@show'); // Show a specific item
    $router->post('/users', 'UserController@store'); // Create a new item
    $router->put('/users/{id}', 'UserController@update'); // Update an item
    $router->delete('/users/{id}', 'UserController@destroy'); // Delete an item

    // Employees
    $router->get('/employees', 'EmployeeController@index'); // Show all items
    $router->get('/employees/{id}', 'EmployeeController@show'); // Show a specific item
    $router->post('/employees', 'EmployeeController@store'); // Create a new item
    $router->put('/employees/{id}', 'EmployeeController@update'); // Update an item
    $router->delete('/employees/{id}', 'EmployeeController@destroy'); // Delete an item


    // Positions
    $router->get('/positions', 'PositionController@index'); // Show all items
    $router->get('/positions/{id}', 'PositionController@show'); // Show a specific item
    $router->post('/positions', 'PositionController@store'); // Create a new item
    $router->put('/positions/{id}', 'PositionController@update'); // Update an item
    $router->delete('/positions/{id}', 'PositionController@destroy'); // Delete an item

    // Attendance
    $router->get('/attendances', 'AttendanceController@index'); // Show all items
    $router->get('/attendances/{id}', 'AttendanceController@show'); // Show a specific item
    $router->post('/attendances', 'AttendanceController@store'); // Create a new item
    $router->put('/attendances/{id}', 'AttendanceController@update'); // Update an item
    $router->delete('/attendances/{id}', 'AttendanceController@destroy'); // Delete an item
    $router->post('/attendance/login', 'AttendanceController@login');
    $router->post('/attendance/logout', 'AttendanceController@logout');
});

$router->get('/{any:.*}', function () use ($router) {
    return view('errors.404');
});
