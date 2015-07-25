<?php

class UnappliedEffect extends Eloquent {

  /**
   * The database table used by the model.
   * 
   * @var string
   */
  protected $table = 'unappliedEffects';

  protected $rules = array(
    'docNumber'        => 'required|string',
    'status'        => 'string'
    );

  protected $fillable = array('docId', 'docNumber', 'status');

  protected $hidden = array('docId');

  public $timestamps = false;

  public static $snakeAttributes = false;

  /**
   * Each unappliedEffect belongs to a doc.
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