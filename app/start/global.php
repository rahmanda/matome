<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Event Listener for Cron Job
|--------------------------------------------------------------------------
|
| The event runs every time event fired on cronjob. There are two task, 
| to pull xml and pdf from remote server and to fill out database 
| from xml every day.
|
*/
Event::listen('cron.collectJobs', function() {

  /**
   * Read metadata from storage/docs/logger/Y-m-d 
   * and store/update to database periodically
   */
  Cron::add('storeMetadataToDB', $_ENV['CRON_SCHEDULE_META'], function() {
    $doc = new Matome\Storage\DocRepository();
    $yesterday = date("Y-m-d", time());
    
    $directory = $_ENV['SYNC_DIRECTORY'].'logger/'.$yesterday.'/new/';
    $files = File::files($directory);
    if(!empty($files)) {
      foreach($files as $file) {
        $xml = (array)simplexml_load_file($file);
        $save = $doc->storeMetadata($xml, true); 
      }
    }

    $directory = $_ENV['SYNC_DIRECTORY'].'logger/'.$yesterday.'/update/';
    $files = File::files($directory);
    if(!empty($files)) {
      foreach($files as $file) {
        $xml = (array)simplexml_load_file($file);
        $save = $doc->updateMetadata($xml, null, true);
      }
    }
  });

  $servers = Server::all();
  foreach ($servers as $server) {
    /**
     * Pull file from remote servers periodically
     * Depends on each server schedule configuration
     */
    Cron::add($server['jobname'], $server['schedule'], function() use ($server) {
      exec('rsync -a -e "ssh -p 2222" '.$server['hostname'].'@'.$server['address'].':'.$server['directory'].' '.$_ENV['SYNC_DIRECTORY']);
    });

    /**
     * Sync metadata.xsd (push)
     */
    // Cron::add("schema", $_ENV['SCHEMA_SYNC_SCHEDULE'], function() use ($server) {
    //   exec('rsync -a -e "ssh -p 2222" '.$_ENV['SYNC_DIRECTORY'].' '.$server['hostname'].'@'.$server['address'].':public');
    // });
  }
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
