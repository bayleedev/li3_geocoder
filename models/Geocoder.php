<?php

namespace app\models;

use lithium\data\Model;

class Geocoder extends Model {

	public $_meta = array('connection' => 'geocoder');

	protected $_schema = array(
		'id' => array('type' => 'int'),
		'latitude' => array('type' => 'string'),
		'longitude' => array('type' => 'string'),
	);

	/**
	 * Helper method for cleaner Geo finds.
	 */
	public static function findByLocation($location) {
		return static::find('first', array(
			'conditions' => compact('location'),
		));
	}

}

?>