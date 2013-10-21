<?php

namespace app\tests\cases\models;

use lithium\data\Connections;
use app\models\Geocoder;

class GeocoderTest extends \lithium\test\Unit {

	public function setup() {
		Connections::add('geocoder', array(
			'type' => 'http',
			'adapter' => 'Geocoder',
			'provider' => 'google',
			'providerAdapter' => 'curl',
			'providerOptions' => array(
				'locale' => 'en_US',
				'region' => 'USA',
				'useSsl' => true,
			),
		));
	}

	public function testBasicFind() {
		$location = Geocoder::find('first', array(
			'conditions' => array(
				'location' => '74105',
			),
		));
		$this->assertNotNull($location->latitude);
		$this->assertNotNull($location->longitude);
	}

	public function testFindByLocation() {
		$location = Geocoder::findByLocation('74105');
		$this->assertNotNull($location->latitude);
		$this->assertNotNull($location->longitude);
	}
}

?>