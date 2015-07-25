<?php

class StagingSeeder extends Seeder {

  public function run() {

    DB::table('publishers')->delete();

    Publisher::create(array(
      'type'                => 'bla',
      'administrativeLevel' => 'bla',
      'region'              => 'bla',
      'name'                => 'bla',
      'fields'              => 'bla',
      'siteUrl'             => 'https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens',
      ));

  } 

}