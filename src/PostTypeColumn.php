<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column;

use ThemePlate\Column\Interfaces\PopulateActionInterface;
use ThemePlate\Column\Traits\HasLocation;
use ThemePlate\Column\Traits\PopulateWillEcho;

class PostTypeColumn extends BaseColumn implements PopulateActionInterface {

	use HasLocation;
	use PopulateWillEcho;


	protected function context(): array {

		$context = array();

		if ( ! empty( $this->location ) ) {
			$context[0]['modify']   = $this->location . '_posts';
			$context[0]['populate'] = $this->location . '_posts';
		} else {
			$context[0]['modify']   = 'posts';
			$context[0]['populate'] = 'posts';
			$context[1]['modify']   = 'pages';
			$context[1]['populate'] = 'pages';
		}

		return $context;

	}

}
