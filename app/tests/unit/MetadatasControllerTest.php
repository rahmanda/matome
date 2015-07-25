<?php 

class MetadatasControllerTest extends TestCase {

  public function setUp() {
    parent::setUp();

    $this->rep = Mockery::mock('\Matome\Storage\DocRepository');
    $this->formSchema = Mockery::mock('\Matome\Metadata\FormSchema');
  }

  public function testAll() {
    $this->mock->shouldReceive('pagination')->once();

    $this->call('GET', 'api/')
  }

  public function tearDown() {
    parent::tearDown();
    Mockery::close();
  }

}