<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Traits;

trait PopulateWillReturn {

	use CanPopulate;


	public function populate( string $output, string $column_name, int $object_id ): string {

		if ( $column_name !== $this->column_key ) {
			return '';
		}

		return $this->action_callback( $object_id, true );

	}

}
