<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function()
{
	return View::make('login');
});

Route::get('login', function()
{
  return View::make('login');
});

Route::get('register', array('as' => 'register', function() {
  return View::make('register');
}));

Route::post('upload', array('as' => 'upload', 'uses' => 'UploadController@upload'));

Route::group(array('prefix' => 'metadata'), function() {

  // GET metadata/ -- View metadata list page
  Route::get('', array('as' => 'metadata','uses' => 'MetadatasController@index'));
  // GET metadata/count -- Get number of complete, incomplete and all metadata in database
  Route::get('count', array('as' => 'countMetadata', 'uses' => 'MetadatasController@getCount'));
  // POST metadata/ -- Create new metadata
  Route::post('', array('uses' => 'MetadatasController@store'));
  // PUT metadata/{docId} -- Update metadata, accept input with relationship
  Route::put('{docId}', array('uses' => 'MetadatasController@update'));
  // GET formSchema{ext} -- Get form schema that is used for generating dynamic form based on XSD
  Route::get('formSchema{ext}', array('as' => 'formSchema', 'uses' => 'MetadatasController@getFormSchema'));
  // GET item/{id} -- Get single item metadata 
  Route::get('item/{id}', array('uses' => 'MetadatasController@show'));
  // GET view/{id} -- View single metadata page 
  Route::get('view/{id}', function($id) {
    return View::make('view', array('route' => 'view', 'itemId' => $id));
  });
  // GET add/{id} -- View metadata add page
  Route::get('add', array('as' => 'addMetadata','uses' => 'MetadatasController@add'));
  // GET fetchIncomplete/{orderBy}/{order} -- Get incomplete metadatas
  Route::get('fetchIncomplete/{orderBy}/{order}', array('uses' => 'MetadatasController@incomplete'));
  // GET fetchComplete/{orderBy}/{order} -- Get complete metadatas
  Route::get('fetchComplete/{orderBy}/{order}', array('uses' => 'MetadatasController@complete'));
  // GEt fetchAll/{orderBy}/{order} -- Get all metadatas
  Route::get('fetchAll/{orderBy}/{order}', array('uses' => 'MetadatasController@all'));

});

// Confide RESTful route
Route::api(array('version' => 'v1', 'prefix' => 'api'), function() {

  Route::post('users', array(
    'uses' => 'UsersController@postIndex',
    'as'  => 'register'
    ));

  Route::post('users/login', array(
    'uses' => 'UsersController@postLogin',
    'as'  => 'login'
    ));

  Route::post('users/forgot', array(
    'uses' => 'UsersController@postForgot',
    'as' => 'forgot'
    ));

  Route::post('users/reset', array(
    'uses' => 'UsersController@postReset',
    'as' => 'reset'
    ));

  Route::get('users/confirm/{code}', array(
    'uses' => 'UsersController@getConfirm',
    'as'  => 'confirm'
    ));

  Route::get('users/reset_password/{token}', array(
    'uses' => 'UsersController@getReset',
    'as'  => 'reset'
    ));

  Route::post('users/reset_password', array(
    'uses' => 'UsersController@postReset'
    ));

  Route::resource('docs', 'DocsController',
    array('except' => array('create', 'edit')));

  Route::group(array('protected' => true, 'providers' => 'jwt'), function() {

    Route::get('users', function() { return API::user(); });

    Route::put('users/{id}', array('as' => 'updateUser', 'uses' => 'UsersController@putIndex'));

    Route::get('users/logout', array('as' => 'logout', 'uses' => 'UsersController@getLogout'));

    Route::post('creators', array('as' => 'create.creator', 'uses' => 'CreatorsController@store'));

    Route::put('creators', array('as' => 'update.creator', 'uses' => 'CreatorsController@update'));

    Route::get('creators', array('as' => 'get.creator', 'uses' => 'CreatorsController@show'));

    /**
     * Scheduler routes
     */
    Route::resource('scheduler', 'SchedulersController');

  });
});

Route::post('refresh', function() {
  $token = Input::get('token');

  return JWTAuth::refresh($token);
});

Route::get('storage_path', function() {
  return storage_path();
});

Route::get('storeMetaCron', array('uses' => 'MetadatasController@storeMetaCron'));
