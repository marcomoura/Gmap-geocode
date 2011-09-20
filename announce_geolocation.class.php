<?php


require "scripts/init.inc.php" ;

require_once ( $common_directory . "classes/spiffy_db_connection.class.php" ) ;
require ( $common_directory . "classes/hast/getters.inc.php" ) ;


class Announce_Geolocation {

	public function get_postal_code( $postal_code) {
		return get_zip_code( as_number( $postal_code ) ) ;
	}

}
