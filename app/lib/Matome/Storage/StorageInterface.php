<?php namespace Matome\Storage;

interface StorageInterface {

  public function create();

  public function update();

  public function find();

  public function destroy();

  public function isValid();

}