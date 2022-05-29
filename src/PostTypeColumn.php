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

		$post_types = $this->locations;

		if ( empty( $post_types ) ) {
			$post_types = array( 'post', 'page' );
		}

		foreach ( $post_types as $location ) {
			$context[] = array(
				'modify'   => $location . '_posts',
				'populate' => $location . '_posts',
			);
		}

		return $context;

	}

}
