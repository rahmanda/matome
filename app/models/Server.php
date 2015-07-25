<?php

class Server extends Eloquent {

  /**
   * The database table used by the model.
   * 
   * @var string
   */
  protected $table = 'servers';

  public static $snakeAttributes = false;

  protected $fillable = array(
    'userId',
    'hostname',
    'address',
    'directory',
    'jobname',
    'schedule'
    );

  public $timestamps = false;

  public $createRules = array(
    'userId' => 'string',
    'hostname' => 'string',
    'address' => 'string',
    'directory' => 'string',
    'jobname' => 'string',
    'schedule' => 'string'
    );

  public $updateRules = array(
    'userId' => 'string',
    'hostname' => 'string',
    'address' => 'string',
    'directory' => 'string',
    'jobname' => 'string',
    'schedule' => 'string'
    );

  /**
   * Each server belongs to a doc.
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