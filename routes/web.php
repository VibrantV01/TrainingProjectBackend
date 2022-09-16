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


Route::group([

    'prefix' => 'api',

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

});

$router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
$router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);


$router->group(['prefix'=>'','middleware' => ['auth', 'verified']], function () use ($router) {
    $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);

});
$router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);


$router->group(['prefix' => 'api','middleware' => ['auth', 'verified', 'auth.role']], function () use ($router) {


  $router->delete('users/{id}', ['uses' => 'UsersController@delete']);

  $router->put('users/{id}', ['uses' => 'UsersController@update']);

  $router->options('users', ['middleware' => 'CorsMiddleware', 'uses' => 'UsersController@create']);

  $router->delete('deleteUsers', ['uses' => 'UsersController@deleteUsers']);

  $router->post('users', ['uses' => 'UsersController@create']);

});




$router->group(['prefix' => 'api', 'middleware' => ['auth', 'verified']], function () use ($router) {


    $router->get('users', ['uses' => 'UsersController@showAllUsers']);


});


$router->post('usethis', function(){
    return 'Welcome';
});

$router->group(['middleware' => ['auth', 'verified']], function () use ($router) {

    //Form normal user
    $router->post('/createtask', 'TaskController@create');
    $router->delete('/delete/{id}', 'TaskController@delete');
    $router->put('/updatetask/{id}', 'TaskController@update');
    $router->put('/edittask/{id}', 'TaskController@edittask');
    $router->get('/showtasks/{id}', 'TaskController@showAllTasks');
    $router->get('search/', 'UsersController@showUsersSearch');
    $router->get('/search/{input}', 'UsersController@searchText');
    $router->get('/filterrole/{field}/{value}', 'UsersController@filterUser');
    $router->get('/searchtask/{input}/{id}', 'TaskController@searchtaskuser');
    $router->get('/searchtask//{id}', 'TaskController@showAllTasks');
    $router->get('/filtertask/{field}/{value}/{id}','TaskController@filtertask');
    $router->get('/sorttask/{field}/{order}/{id}','TaskController@sorttask');
    $router->post('/deletebulktasks', 'TaskController@deleteTasks');
    $router->get('/stats', 'TaskController@stats');
    $router->get('/statsOwner', 'TaskController@statsOwner');
    $router->get('/listNotifs',  ['uses' => 'NotificationController@listNotification']); 
    $router->delete('/notif/{id}',  ['uses' => 'NotificationController@deleteNotification']); 
  $router->delete('/clear-notif',  ['uses' => 'NotificationController@clearNotification']);

});


$router->group(['middleware' => ['auth', 'verified', 'auth.role']], function () use ($router) {
   
    $router->get('/showtasks', 'TaskController@showAllTasksAdmin');
    $router->get('/searchtask/', 'TaskController@showAllTasksAdmin');
    $router->get('/searchtask/{input}', 'TaskController@searchTask');
    $router->get('/filtertaskadmin/{field}/{value}','TaskController@filtertaskadmin');
    $router->get('/sorttaskadmin/{field}/{order}','TaskController@sorttaskadmin');

});

// $router->post('/createtask', 'TaskController@create');
// $router->delete('/delete/{id}', 'TaskController@delete');
// $router->put('/updatetask/{id}', 'TaskController@update');
// $router->put('/edittask/{id}', 'TaskController@edittask');
// $router->get('/showtasks/{id}', 'TaskController@showAllTasks');
// $router->get('/showtasks', 'TaskController@showAllTasksAdmin');
// $router->get('search/', 'UsersController@showAllUsers');
// $router->get('/search/{input}', 'UsersController@searchText');
// $router->get('/searchtask/{input}', 'TaskController@searchTask');
// $router->get('/searchtask/', 'TaskController@showAllTasksAdmin');
// $router->get('/searchtask/{input}/{id}', 'TaskController@searchtaskuser');
// $router->get('/searchtask//{id}', 'TaskController@showAllTasks');
// $router->get('/filterrole/{field}/{value}', 'UsersController@filterUser');


// $router->get('/filtertaskadmin/{field}/{value}','TaskController@filtertaskadmin');
// $router->get('/sorttaskadmin/{field}/{order}','TaskController@sorttaskadmin');
// $router->get('/filtertask/{field}/{value}/{id}','TaskController@filtertask');
// $router->get('/sorttask/{field}/{order}/{id}','TaskController@sorttask');
// $router->post('/deletebulktasks', 'TaskController@deleteTasks');

