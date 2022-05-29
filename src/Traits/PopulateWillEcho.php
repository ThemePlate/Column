<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Traits;

trait PopulateWillEcho {

	use CanPopulate;


	public function populate( string $column_name, int $object_id ): void {

		if ( $column_name !== $this->column_key ) {
			return;
		}

		$this->action_callback( $object_id );

	}

}
