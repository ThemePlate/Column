<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Traits;

trait CanPopulate {

	/**
	 * @var callable
	 */
	protected $callback;
	protected array $callback_args;
	protected string $column_key = '';


	protected function action_callback( int $object_id, bool $return = false ) {

		if ( $return ) {
			ob_start();
		}

		$output = call_user_func( $this->callback, $object_id, $this->callback_args );

		if ( $return ) {
			return ob_get_clean();
		}

		return $output;

	}

}
