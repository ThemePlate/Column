<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Column\Interfaces\CommonInterface;
use ThemePlate\Column\UsersColumn;
use WP_Error;

class UsersColumnTest extends AbstractTester {
	protected function get_tested_class( string $identifier ): CommonInterface {
		return new UsersColumn( $identifier );
	}

	protected function factory_create_object(): int {
		$object = $this->factory()->user->create();

		return $object instanceof WP_Error ? 0 : $object;
	}

	/** @return array<string, array<int, bool|string[]>> */
	public function for_firing_init_actually_add_hooks(): array {
		return array(
			'with no location intended' => array(
				false,
				array( 'users' ),
			),
		);
	}

	protected function get_modify_filter_hook_name( string $location ): string {
		return sprintf( self::MODIFY_FILTER, 'users' );
	}

	protected function get_populate_filter_hook_name( string $location ): string {
		return sprintf( self::POPULATE_FILTER, 'users' );
	}

	protected function get_populate_output( string $column_name, int $object_id ): string {
		// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-users-list-table.php#L615
		return apply_filters( $this->get_populate_filter_hook_name( $this->default['location'] ), '', $column_name, $object_id );
	}
}
