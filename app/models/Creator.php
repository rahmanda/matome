<?php

class Creator extends Eloquent {

  /**
   * The database table used by the model.
   * 
   * @var string
   */
  protected $table = 'creators';

  public $timestamps = false;
  public static $snakeAttributes = false;

  protected $fillable = array(
    'type',
    'userId',
    'administrativeLevel',
    'region',
    'name',
    'fields',
    'siteUrl'
    );

  /**
   * Rule for create new creator
   * 
   * @var array
   */
  public $createRules = array(
    'type'                => 'string',
    'administrativeLevel' => 'string',
    'region'              => 'string',
    'name'                => 'string|unique:creators',
    'fields'              => 'string',
    'siteUrl'             => 'string'
    );

  /**
   * Rule for update creator
   * 
   * @var array
   */
  public $updateRules = array(
    'administrativeLevel' => 'string',
    'region'              => 'string',
    'name'                => 'string|unique',
    'fields'              => 'string',
    'siteUrl'             => 'url'
    );

  /**
   * Each creator belongs to a doc.
   * 
   * @return Eloquent
   */
  public function docs() {
    return $this->hasMany('Doc');
  }

  /**
   * Each creator belongs to a user
   * 
   * @return Eloquent
   */
  public function user() {
    return $this->belongsTo('User');
  }

  public function getRules() {
    return $this->createRules;
  }

}