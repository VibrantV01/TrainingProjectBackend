<?php

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
    echo "<center> Welcome </center>";
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

//Auth::routes(['verified' => true]);

Route::group([

    'prefix' => 'api'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

});

$router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
$router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);


$router->group(['middleware' => ['auth', 'verified']], function () use ($router) {
    $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);

});
$router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);
// $router->options('/email/verify', ['middleware' => 'CorsMiddleware','as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);

$router->group(['prefix' => 'api'], function () use ($router) {

  $router->get('users', [/*'middleware' => 'auth.role:admin,user',*/ 'uses' => 'UsersController@showAllUsers']);

  $router->get('users/{id}', ['uses' => 'UsersController@showOneUser']);

  $router->post('users', ['uses' => 'UsersController@create']);

  $router->delete('users/{id}', ['uses' => 'UsersController@delete']);

  $router->put('users/{id}', ['uses' => 'UsersController@update']);

  $router->options('users', ['middleware' => 'CorsMiddleware', 'uses' => 'UsersController@create']);

});

$router->post('usethis', function(){
    return 'Welcome';
});


$router->post('/createtask', 'TaskController@create');
$router->delete('/delete/{id}', 'TaskController@delete');
$router->put('/updatetask/{id}', 'TaskController@update');
$router->put('/edittask/{id}', 'TaskController@edittask');
$router->get('/showtasks/{id}', 'TaskController@showAllTasks');
$router->get('/showtasks', 'TaskController@showAllTasksAdmin');
$router->get('search/', 'UsersController@showAllUsers');
$router->get('/search/{input}', 'UsersController@searchText');
$router->get('/searchtask/{input}', 'TaskController@searchTask');
$router->get('/searchtask/', 'TaskController@showAllTasksAdmin');
$router->get('/searchtask/{input}/{id}', 'TaskController@searchtaskuser');
$router->get('/searchtask//{id}', 'TaskController@showAllTasks');
$router->get('/filterrole/{field}/{value}', 'UsersController@filterUser');
$router->get('/filtertaskadmin/{field}/{value}','TaskController@filtertaskadmin');
$router->get('/sorttaskadmin/{field}/{order}','TaskController@sorttaskadmin');
$router->get('/filtertask/{field}/{value}/{id}','TaskController@filtertask');
$router->get('/sorttask/{field}/{order}/{id}','TaskController@sorttask');

//Filter keyword will use searchtask/{input}/{id}