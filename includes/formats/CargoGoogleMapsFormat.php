<?php

/**
 * @author Yaron Koren
 * @ingroup Cargo
 */
class CargoGoogleMapsFormat extends CargoMapsFormat {

	function __construct( $output ) {
		parent::__construct( $output );
		self::$mappingService = "Google Maps";
	}

	public static function getScripts() {
		global $wgCargoGoogleMapsKey, $wgCargoGoogleMapsUseWikiLang, $wgLanguageCode;

		$params['key'] = $wgCargoGoogleMapsKey;

		if ( $wgCargoGoogleMapsUseWikiLang ) {
			$params['language'] = $wgLanguageCode;
		}

		return [
			"https://maps.googleapis.com/maps/api/js?" . wfArrayToCgi( $params )
		];
	}

	public static function getStyles() {
		return [];
	}

}
