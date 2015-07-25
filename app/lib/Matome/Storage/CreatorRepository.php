<?php namespace Matome\Storage;

class CreatorRepository extends StorageAbstract {

  /**
   * Constructor
   * 
   * @param Publisher $model
   */
  public function __construct() {
    $this->model = new \Creator();
  }

  public function fillable() {
    return $this->model->fillable();
  }

  /**
   * Get creator by user id
   * @param  integer $id
   * @return model    
   */
  public function getByUser($id) {
    $creator = \Creator::where('userId', $id)->where('valid', 1)->first();

    return $creator;
  }

  public function createCreator($input, $id) {
    try {
      $creator = \Creator::where('userId', $id)->where('valid', 1)->first();
      if(!empty($creator)) {
        $creator->valid = 0;
        $creator->save();
      }
      $user = \User::find($id);
      $user->creator()->create($input);
    } catch (Exception $e) {
      return false;
    }
    return true;
  }

  /**
   * Update creator seamlessly
   * @param  integer $input
   * @return boolean or integer       
   */
  public function updateCreator($input, $id) {
    try {
      $creator = \Creator::where('userId', $id)->first();
      $creator->name = isset($input['name']) ? $input['name'] : '';
      $creator->type = isset($input['type']) ? $input['type'] : '';
      $creator->administrativeLevel = isset($input['administrativeLevel']) ? $input['administrativeLevel'] : '';
      $creator->region = isset($input['region']) ? $input['region'] : '';
      $creator->fields = isset($input['fields']) ? $input['fields'] : '';
      $creator->siteUrl = isset($input['siteUrl']) ? $input['siteUrl'] : '';

      $creator->save();

    } catch (Exception $e) {
      return false;
    }

    return true;
  }

}