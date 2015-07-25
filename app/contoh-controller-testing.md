<?php

class PublishersControllerTest extends TestCase {

  public function setUp() {
    parent::setUp();

    $this->mock = $this->mock('Matome\Storage\CreatorRepository');
  }

  public function mock($class) {
    $mock = Mockery::mock($class);

    $this->app->instance($class, $mock);

    return $mock;
  }

  public function testIndex() {
    $this->mock->shouldReceive('all')->once();

    $this->call('GET', 'api/publishers');

    $this->assertResponseOk();
  }

  public function testStore() {
    Input::replace(
      $input = array(
      'administrativeLevel' => 'bla',
      'region'              => 'bla',
      'name'                => 'bla',
      'fields'              => 'bla',
      'siteUrl'             => 'https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens',
      )
    );

    $this->mock->shouldReceive('create')->once();

    $this->call('POST', 'api/publishers');

    $this->assertResponseOk();
  }

  public function testShow() {

    $this->mock->shouldReceive('getById')->once();

    $this->call('GET', 'api/publishers/1');

    $this->assertResponseOk();

  }

  public function testUpdate() {

    $this->mock->id = 1;

    Input::replace($input = array(
      'type'                => 'bla',
      'administrativeLevel' => 'bla',
      'region'              => 'bla',
      'name'                => 'bla',
      'fields'              => 'bla',
      'siteUrl'             => 'https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens',
      ));

    $this->mock->shouldReceive('update')->once();

    $this->call('PUT', 'api/publishers/1');

    $this->assertResponseOk();
  }

  public function testDestroy() {

    $this->mock->id = 1;

    $this->mock->shouldReceive('destroy')->once();

    $this->call('DELETE', 'api/publishers/1');

    $this->assertResponseOk();

  }

  public function tearDown()
  {
    parent::tearDown();
    Mockery::close();
  }

}