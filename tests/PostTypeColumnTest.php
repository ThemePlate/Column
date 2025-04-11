<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Column\Interfaces\CommonInterface;
use ThemePlate\Column\PostTypeColumn;
use WP_Error;

class PostTypeColumnTest extends AbstractTester {
	protected function get_tested_class( string $identifier ): CommonInterface {
		return new PostTypeColumn( $identifier );
	}

	protected function factory_create_object(): int {
		$object = $this->factory()->post->create();

		return $object instanceof WP_Error ? 0 : $object;
	}

	/** @return array<string, array<int, bool|string[]>> */
	public function for_firing_init_actually_add_hooks(): array {
		return array(
			'with location specified'    => array(
				true,
				array( $this->default['location'] ),
			),
			'with no location specified' => array(
				false,
				array( 'post', 'page' ), // default locations
			),
			'with multiple locations'    => array(
				true,
				array( 'event', 'portfolio', 'testimonial' ),
			),
		);
	}

	protected function get_modify_filter_hook_name( string $location ): string {
		return sprintf( self::MODIFY_FILTER, $location . '_posts' );
	}

	protected function get_populate_filter_hook_name( string $location ): string {
		return sprintf( self::POPULATE_FILTER, $location . '_posts' );
	}

	protected function get_populate_output( string $column_name, int $object_id ): string {
		ob_start();
		// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-posts-list-table.php#L1349
		do_action( $this->get_populate_filter_hook_name( $this->default['location'] ), $column_name, $object_id );

		return (string) ob_get_clean();
	}
}
