<?php
use Illuminate\Routing\Controller;
use Dingo\Api\Routing\ControllerTrait;
use Matome\Storage\CreatorRepository;

/**
 * CreatorsController Class
 *
 * Implements actions regarding creators management
 */
class CreatorsController extends Controller
{
  use ControllerTrait;

  /**
   * Injects model
   * 
   * @param Creator $creator
   */
  public function __construct(CreatorRepository $creator) {
    $this->creator = $creator;
  }

  /**
   * Fetch all listings
   * 
   * @return JSON array
   */
  public function index() {
    return $this->response->array($this->creator->all());
  }

  /**
   * Stores new creator
   * 
   * @return JSON array
   */
  public function store() {

    $user = JWTAuth::parseToken()->toUser();
    $creator = $this->creator->createCreator(Input::all(), $user['id']);

    return $this->response->array($creator);

  }

  public function show() {
    $user = JWTAuth::parseToken()->toUser();
    $creator = $this->creator->getByUser($user['id']);

    return $this->response->array($creator);

  }

  /**
   * Updates particular creator
   * 
   * @param  int $id
   * @return boolean
   */
  public function update() {
    $user = JWTAuth::parseToken()->toUser();
    $creator = $this->creator->createCreator(Input::all(), $user['id']);

    return $this->response->array($creator);
    
    // $user = JWTAuth::parseToken()->toUser();
    // $creator = $this->creator->updateCreator(Input::all(), $user['id']);

    // return $this->response->array($creator);

  }

  /**
   * Delete creator records by id
   * 
   * @param  creator $id
   * @return int
   */
  public function destroy($id) {

    $destroy = $this->creator->destroy($id);

    return $this->response->array($destroy);

  }

  public function getFillable() {
    return $this->creator->fillable();
  }
}