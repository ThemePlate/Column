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

		if ( ! empty( $this->location ) ) {
			$context[0]['modify']   = 'edit-' . $this->location;
			$context[0]['populate'] = $this->location;
		} else {
			$taxonomies   = get_taxonomies( array( '_builtin' => false ) );
			$taxonomies[] = 'category';
			$taxonomies[] = 'post_tag';

			foreach ( $taxonomies as $index => $taxonomy ) {
				$context[ $index ]['modify']   = 'edit-' . $taxonomy;
				$context[ $index ]['populate'] = $taxonomy;
			}
		}

		return $context;

	}

}
