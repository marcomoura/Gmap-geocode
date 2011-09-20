<?php

require_once "geo_location/gmap_address.class.php" ;

class Gmap_AddressTest extends PHPUnit_Framework_TestCase {

	private $address ;

	public function setUp(){
		$this->address = new Gmap_Address() ;
	}

	public function tearDown(){
		unset( $this->full_address ) ;
	}

	public function test_search_geo_no_result(){
		$result = $this->address->find( "" ) ;
		$this->assertEquals("{\n   \"results\" : [],\n   \"status\" : \"ZERO_RESULTS\"\n}\n", $result ) ;
	}

	public function test_parse_json(){
		$json = $this->address->find( "" ) ;
		$result = $this->address->parse( $json ) ;
		$this->assertEquals( "ZERO_RESULTS" , $result->status ) ;
	}

	private $full_address;
	private function get_guaratingueta(){
		$json = $this->address->find( "rua evandro alves da silva guaratinguetá são paulo brasil" ) ;
		$this->full_address = $this->address->parse( $json ) ;
	}

	public function test_city(){
		$this->get_guaratingueta();
		$this->assertObjectHasAttribute( "city" , $this->full_address ) ;	
		$this->assertEquals( "Guaratinguetá" , $this->full_address->city ) ;	
	}

	public function test_country(){
		$this->get_guaratingueta();
		$this->assertObjectHasAttribute( "country" , $this->full_address ) ;	
		$this->assertEquals( "Brasil" , $this->full_address->country ) ;	
	}

	public function test_state() {
		$this->get_guaratingueta();
		$this->assertObjectHasAttribute( "state" , $this->full_address ) ;	
		$this->assertEquals( "São Paulo" , $this->full_address->state ) ;	
	}

	public function test_postal_code() {
		$this->get_guaratingueta();
		$this->assertObjectHasAttribute( "postal_code" , $this->full_address ) ;	
		$this->assertEquals( "12518-510" , $this->full_address->postal_code ) ;	
	}

	public function test_latitude() {
		$this->get_guaratingueta();
		$this->assertObjectHasAttribute( "latitude" , $this->full_address ) ;	
		$this->assertEquals( "-22.7817165" , $this->full_address->latitude ) ;	
	}

	public function test_longitude(){
		$this->get_guaratingueta();
		$this->assertObjectHasAttribute( "longitude" , $this->full_address ) ;	
		$this->assertEquals( "-45.1859222" , $this->full_address->longitude ) ;	
	}

	public function test_city_without_city(){
		$json = $this->address->find( "rua evandro alves da silva são paulo brasil" ) ;
		$full_address = $this->address->parse( $json ) ;
		$this->assertEquals( "R. Evandro Alves da Silva" , $full_address->street ) ;	
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
		$this->assertEquals( "Brasil" , $full_address->country ) ;	
		$this->assertEquals( "-45.1859222" , $full_address->longitude ) ;	
	}

	public function test_only_city_and_country(){
		$full_address = $this->address->get( "guaratingueta brazil" ) ;
		$this->assertObjectHasAttribute( "city" , $full_address ) ;	
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
	}

	public function test_only_country() {
		$full_address = $this->address->get( "brazil" ) ;
		$this->assertObjectHasAttribute( "country" , $full_address ) ;	
		$this->assertEquals( "Brasil" , $full_address->country ) ;	
	}

	public function test_only_city() {
		$full_address = $this->address->get( "guaratinguetá" ) ;
		$this->assertObjectHasAttribute( "city" , $full_address ) ;	
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
		$this->assertEquals( "Brasil" , $full_address->country ) ;	
	}

	public function test_only_state() {
		$full_address = $this->address->get( "São Paulo" ) ;
		$this->assertObjectHasAttribute( "state" , $full_address ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_only_address() {
		$full_address = $this->address->get( "R: Evandro Alves da Silva" ) ;
		$this->assertEquals( "12518-510" , $full_address->postal_code ) ;	
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_only_postal_code() {
		$full_address = $this->address->get( "12518510" ) ;
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_only_postal_code_with_slash() {
		$full_address = $this->address->get( "12518-510" ) ;
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_with_postal_code_and_city() {
		$full_address = $this->address->get( "12518510 guaratinguetá" ) ;
		$this->assertEquals( "12518-510" , $full_address->postal_code ) ;	
		$this->assertEquals( "Guaratinguetá" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_folha_address_alameda() {
		$full_address = $this->address->get( "Al. Barão de Limeira, 425, Campos Elísios, 01202-900" ) ;
		$this->assertEquals( "São Paulo" , $full_address->city ) ;	
		$this->assertEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_folha_address_fail() {
		$full_address = $this->address->get( "Folha de são paulo jornal, Santa Cecília, 01202-900" ) ;
		$this->assertNotEquals( "OK" , $full_address->status ) ;	
	}

	public function test_santa_cecilia_postal_code_with_zero() {
		$full_address = $this->address->get( "01225010" ) ;
		$this->assertEquals( "São Paulo" , $full_address->city ) ;	
	}

	public function test_santa_cecilia_postal_code_without_zero_fail() {
		$full_address = $this->address->get( "1225010" ) ;
		$this->assertNotEquals( "OK" , $full_address->status ) ;	
	}

	public function test_folha_postal_code_local_error() {
		$full_address = $this->address->get( "01202900" ) ;
		$this->assertEquals( "Pittsfield" , $full_address->city ) ;	
		$this->assertNotEquals( "São Paulo" , $full_address->state ) ;	
	}

	public function test_folha_postal_code_with_slash_local_error() {
		$full_address = $this->address->get( "01202-900" ) ;
		$this->assertEquals( "Canton" , $full_address->city ) ;	
		$this->assertNotEquals( "São Paulo" , $full_address->state ) ;	
	}

}
