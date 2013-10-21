<?php

namespace app\extensions\adapter\data\source\http;

use Geocoder\Geocoder as Geo;
use ReflectionClass;

class Geocoder extends \lithium\data\source\Http {

	/**
	 * Geocoder object.
	 */
	protected $_geocoder = null;

	/**
	 * The config array contains all config options
	 *
	 * @var  array
	 */
	protected $_config = array();

	/**
	 * Key/value adapter pairs
	 *
	 * @var array
	 */
	protected $_adapters = array(
		'curl' => 'Geocoder\HttpAdapter\CurlHttpAdapter',
	);

	/**
	 * Classes to use for creation of pages
	 *
	 * @var  array
	 */
	protected $_classes = array(
		'service' => 'lithium\net\http\Service',
		'entity'  => 'lithium\data\entity\Document',
		'set'     => 'lithium\data\collection\DocumentSet',
		'relationship' => 'lithium\data\model\Relationship',
		'schema' => 'lithium\data\DocumentSchema',
	);

	/**
	 * Maps the key to the class and necessary args.
	 *
	 * @var array
	 */
	protected $_providers = array(
		'google' => array(
			'class' => 'Geocoder\Provider\GoogleMapsProvider',
			'args' => array(
				'adapter',
				'locale',
				'region',
				'useSsl',
			),
		),
		'google_business' => array(
			'class' => 'Geocoder\Provider\GoogleMapsBusinessProvider',
			'args' => array(
				'adapter',
				'client_id',
				'private_key',
				'locale',
				'region',
				'useSsl',
			),
		),
	);

	/**
	 * Add configuration to the WordPress data source
	 *
	 * @param array $config The optional config
	 */
	public function __construct(array $config = array()) {
		$config += array(
			'provider' => 'google',
			'providerAdapter' => 'curl',
			'providerOptions' => array(),
		);
		parent::__construct($config);
	}

	/**
	 * Creates the provider options array in order.
	 *
	 * @param array $defaults Overwrites options set in `$_config`
	 * @return array
	 */
	public function providerOptions(array $defaults = array()) {
		extract($this->_config('providerOptions'), EXTR_OVERWRITE);
		extract($defaults, EXTR_OVERWRITE);
		return array_values(compact($this->_providers[$this->_config('provider')]['args']));
	}

	/**
	 * Creates or returns a new geocoder object.
	 *
	 * @return object
	 */
	public function geocoder() {
		if (is_null($this->_geocoder)) {
			$adapter = $this->_adapters[$this->_config('providerAdapter')];

			$class = new ReflectionClass($this->_providers[$this->_config('provider')]['class']);
			$provider = $class->newInstanceArgs($this->providerOptions(array(
				'adapter' => new $adapter,
			)));
			$this->_geocoder = new Geo;
			$this->_geocoder->registerProvider($provider);
		}
		return $this->_geocoder;
	}

	/**
	 * Reader for Geocoder
	 */
	public function read($query, array $options = array()) {
		if (empty($options['conditions']['location'])) {
			throw new \Exception('Required option `location` missing.');
		}
		$location = $options['conditions']['location'];
		$geocode = $this->geocoder()->geocode($location)->toArray();
		return $this->item($query->model(), array($geocode), array('type' => 'set'));
	}

	/**
	 * Helper method for accessing config variables.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	protected function _config($key) {
		return $this->_config[$key];
	}

	/**
	 * This method is responsible for factorying a new instance of a single entity object of correct
	 * type, matching the current data source class.
	 *
	 * @param string $model A fully-namespaced class name representing the model class to which the
	 *        `Entity` object will be bound.
	 * @param array $data The default data with which the new `Entity` should be populated.
	 * @param array $options Any additional options to pass to the `Entity`'s constructor
	 * @return object Returns a new, un-saved `Entity` object bound to the model class specified
	 *        in `$model`.
	 */
	public function item($model, array $data = array(), array $options = array()) {
		$defaults = array('class' => 'entity');
		$options += $defaults;

		$class = $options['class'];
		unset($options['class']);
		return $this->_instance($class, compact('model', 'data') + $options);
	}

}

?>