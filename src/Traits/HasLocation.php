<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Traits;

trait HasLocation {

	protected string $location = '';


	public function location( string $location ): self {

		$this->location = $location;

		return $this;

	}

}
