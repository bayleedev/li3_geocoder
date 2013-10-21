<?php

namespace li3_geocoder\tests\cases\extensions\adapter\data\source\http;

use li3_geocoder\extensions\adapter\data\source\http\geocoder\Mock as Geocoder;
use lithium\test\Mocker;

class GeocoderTest extends \lithium\test\Unit {

	public function setup() {
		Mocker::register();
	}
	public function setupGeo(array $options = array()) {
		$options = $options + array(
			'adapter' => 'curl',
			'provider' => 'google',
			'providerOptions' => array(
				'locale' => 'en_US',
				'region' => 'USA',
				'useSsl' => true,
			),
		);
		return new Geocoder($options);
	}

	public function testProviderOptions() {
		$geocoder = $this->setupGeo(array(
			'provider' => 'standard',
			'providerOptions' => array(
				'foo' => 'bar',
				'baz' => 'qux',
			),
		));
		$geocoder->_providers = array(
			'standard' => array(
				'args' => array('baz', 'bar', 'foo'),
			),
		);
		$this->assertEqual(array('qux', 'bobar', 'bar'), $geocoder->providerOptions(array(
			'bar' => 'bobar',
		)));
	}

}

?>