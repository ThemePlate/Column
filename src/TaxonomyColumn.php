<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column;

use ThemePlate\Column\Interfaces\PopulateFilterInterface;
use ThemePlate\Column\Traits\HasLocation;
use ThemePlate\Column\Traits\PopulateWillReturn;

class TaxonomyColumn extends BaseColumn implements PopulateFilterInterface {

	use HasLocation;
	use PopulateWillReturn;


	protected function context(): array {

		$context = array();

		$taxonomies = $this->locations;

		if ( empty( $taxonomies ) ) {
			$taxonomies = array( 'category', 'post_tag' );
		}

		foreach ( $taxonomies as $taxonomy ) {
			$context[] = array(
				'modify'   => 'edit-' . $taxonomy,
				'populate' => $taxonomy,
			);
		}

		return $context;

	}

}
