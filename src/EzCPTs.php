<?php

namespace BK\EZCPT;

use BK\EZCPT\Controllers\{
	PostTypeController as EzCPT,
	TaxonomyController as EzTax
};

class EzCPTs {
	/** @var EzCPTs Singleton instance */
	private static EzCPTs $instance;

	/** @var array Instances of controllers */
	private array $controllers = [];

	public function __construct () {
		if (isset(self::$instance)) {
			return;
		}

		self::$instance = $this;

		$this->init();
	}

	private function init () {
		EzTax::init();
		EzCPT::init();
	}
}
