<?php

class Doc extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'docs';
  public static $snakeAttributes = false;

  /**
   * Fillable fields on table
   * 
   * @var array
   */
  public static $uploadRule = array(
    'doc' => 'required|mimes:pdf'
    );

  protected $fillable = array(
    'title', 
    'identifier', 
    'filename', 
    'originalFilename', 
    'number', 
    'docType', 
    'publishedDate', 
    'validDate', 
    'description',
    'subject'
  );

  /**
   * Rule for validating new Doc creation
   *
   * @var array
   */
  public $createRules = array(
    'title'               => 'string',
    'number'              => 'string',
    'docType'             => 'string',
    'publishedDate'       => 'date',
    'validDate'           => 'date',
    'description'         => 'string|min:5', 
    );


  /**
   * Rule for validating update Doc
   * 
   * @var array
   */
  public $updateRules = array(
    'title'               => 'string',
    'number'              => 'string',
    'docType'             => 'string',
    'publishedDate'       => 'date',
    'validDate'           => 'date',
    'description'         => 'string|min:5',
    );
  

  /**
   * Each doc belongs to creator
   * 
   * @return Eloquent
   */
  public function creator() {
    return $this->belongsTo('Creator');
  }

  /**
   * Each doc has an enactment
   * 
   * @return Eloquent
   */
  public function enactment() {
    return $this->hasOne('Enactment', 'docId');
  }

  public function unappliedEffect() {
    return $this->hasMany('UnappliedEffect', 'docId');
  }
  
  public function getRules() {
    return $this->createRules;
  }

}
