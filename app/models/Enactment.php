<?php

class Enactment extends Eloquent {

  /**
   * The database table used by the model.
   * 
   * @var string
   */
  protected $table = 'enactments';

  public static $snakeAttributes = false;

  protected $fillable = array(
    'number', 
    'publishedDate',  
    'type', 
    'identifier');

  protected $rules = array(
    'number'        => 'required|string',
    'publishedDate' => 'required',
    );

  /**
   * Each enactment belongs to a doc.
   * 
   * @return Eloquent
   */
  public function doc() {
    return $this->belongsTo('Doc');
  }

  public function getRules() {
    return $this->rules;
  }

}