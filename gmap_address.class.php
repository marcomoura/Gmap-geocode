<?php

/**
 * Gmap_Address 
 *
 * tipo de precisão que o gmap retorna
 *  "ROOFTOP" indicates that the returned result is a precise geocode for which we have location information accurate down to street address precision.
 *   "RANGE_INTERPOLATED" indicates that the returned result reflects an approximation (usually on a road) interpolated between two precise points (such as intersections). Interpolated results are generally returned when rooftop geocodes are unavailable for a street address.
 *   "GEOMETRIC_CENTER" indicates that the returned result is the geometric center of a result such as a polyline (for example, a street) or polygon (region).
 *   "APPROXIMATE" indicates that the returned result is approximate.
 *	fonte: http://code.google.com/apis/maps/documentation/geocoding/#StatusCodes
 * @author Marco Moura 
 * @copyright 2011 Folha.com
 */
class Gmap_Address {

	/**
	 * Busca o endereço na api do google maps e retorna um objeto endereço
	 * 
	 * @param string $_address 
	 * @access public
	 * @return Object stdClass Address
	 */
	public function get( $_address ) {
		$json = $this->find( $_address ) ;
		return $this->parse( $json ) ;
	}

	/**
	 * Busca o endereço na api do google maps e retorna em formato json
	 * 
	 * @param string $_address 
	 * @access public
	 * @return JSON
	 */
	public function find( $_address) {
		return file_get_contents( "http://maps.googleapis.com/maps/api/geocode/json?language=pt-BR&region=br&sensor=false&address=" . urlencode( $_address ) ) ;
	}
	
	/**
	 * Converte json para um objeto 
	 * Quanto passa apenas o cep da rua resulta em dois componentes de endereço, um da rua e um da cidade, neste caso está sendo utilizado o ultimo componente (rua) 
	 *
	 * @TODO o gmap nem sempre retorna tds os campos, tratar isto
	 *
	 * address -> 
	 * 	street_number
	 * 	street
	 *	neighborhood
	 * 	city
	 * 	state
	 * 	country
	 * 	postal_code
	 *
	 * @param mixed $_json_content 
	 * @access public
	 * @return Object stdClass Adress
	 */
	public function parse( $_json_content ) {
		$_json = json_decode( $_json_content ) ;
		$address = new stdClass();
		if ( $_json->status == "OK" ) {
			$_address = $this->get_address_components( $_json );
			foreach( $_address->address_components as $address_components ) {
				$_type = $this->type( $address_components->types[ 0 ] ) ;
				$address->$_type = $address_components->long_name ;
			}

			$address->latitude = $_address->geometry->location->lat ;
			$address->longitude = $_address->geometry->location->lng ;
			$address->precision = $_address->geometry->location_type ;
		}
		$address->status = $_json->status ;
		return $address;
	}
	
	/**
	* Altera o nome dos nodes rua, cidade e uf do json do gmap
	* 
	* @param string $_type 
	* @access private
	* @return string
	*/
	private function type( $_type ) {
		switch( $_type ) {
			case 'locality' :
				return 'city' ;
				break ;
			case 'administrative_area_level_2' :
				return 'city_complement' ;
				break ;
			case 'administrative_area_level_1' :
				return 'state' ;
				break ;
			case 'route' :
				return 'street' ;
				break ;
			case 'sublocality' :
				return 'neighborhood' ;
				break ;
			default :
				return $_type ;
		}
	}

	/**
	 * Pega o último address_component, solução para quando a api retorna multiplos resultados, e o último parece ser o mais preciso
	 * @TODO  Melhorar o algoritmo, para escolher o resultado mais preciso 
	 * 
	 * @param array $_json 
	 * @access private
	 * @return stdClass
	 */
	private function get_address_components( $_json ) {
		return end( $_json->results ) ;
	}

}

