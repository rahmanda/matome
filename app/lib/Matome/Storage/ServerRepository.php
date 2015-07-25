<?php namespace Matome\Storage;

class ServerRepository extends StorageAbstract {

  /**
   * Constructor
   * 
   * @param Server $model
   */
  public function __construct() {
    $this->model = new \Server();
  }

  public function getByUser($id) {
    $user = \User::find($id);
    $server = $user->server;

    return $server;
  }

  public function updateByUser($input, $id) {
    $user = \User::find($id);
    $server = $user->server();

    $server->fill($input);
    $server->save();
  }

}
