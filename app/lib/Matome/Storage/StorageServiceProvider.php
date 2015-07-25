<?php namespace Matome\Storage;

use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider {

  /**
   * Register the service provider
   * 
   * @return void
   */
  public function register() {
    $this->registerPublisher();

    $this->registerSetting();

    $this->registerDoc();
  }
  
  /**
   * Register the publisher repository
   * 
   * @return void
   */
  public function registerPublisher() {
    $this->app->bind(
      'Matome\Storage\PublisherRepositoryInterface',
      'Matome\Storage\PublisherRepository'
      );
  }

  /**
   * Register the setting repository
   * 
   * @return void
   */
  public function registerSetting() {
    $this->app->bind(
      'Matome\Storage\SettingRepositoryInterface',
      'Matome\Storage\SettingRepository'
      );
  }

  /**
   * Register the document repository
   * 
   * @return void
   */
  public function registerDoc() {
    $this->app->bind(
      'Matome\Storage\DocRepositoryInterface',
      'Matome\Storage\DocRepository'
      );
  }

} 