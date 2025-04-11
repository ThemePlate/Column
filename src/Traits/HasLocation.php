<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Traits;

trait HasLocation {

	/** @var string[] */
	protected array $locations = array();


	public function location( string $location ): self {

		$this->locations[] = $location;

		return $this;

	}

}
