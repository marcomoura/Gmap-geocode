<?php

require_once "geo_location/announce_geolocation.class.php" ;

class folha_address_updateTest extends PHPUnit_Framework_TestCase {

	private $address ;

	public function setUp(){
		$this->address = new Announce_Geolocation() ;
	}

	public function tearDown(){
		unset( $this->full_address ) ;
	}

	public function test_search_geo_no_result(){
		$result = $this->get_postal_code( "12518510" ) ;
		$this->assertEquals("{\n   \"results\" : [],\n   \"status\" : \"ZERO_RESULTS\"\n}\n", $result ) ;
	}
}
