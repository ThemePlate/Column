<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Column\Interfaces\CommonInterface;
use ThemePlate\Column\TaxonomyColumn;

class TaxonomyColumnTest extends AbstractTest {
	protected function get_tested_class( string $identifier, callable $callback, array $config = array() ): CommonInterface {
		return new TaxonomyColumn( $identifier, $callback, $config );
	}

	protected function factory_create_object(): int {
		return $this->factory()->term->create();
	}

	public function for_firing_init_actually_add_hooks(): array {
		return array(
			'with location specified'    => array(
				true,
				array( $this->default['location'] ),
			),
			'with no location specified' => array(
				false,
				array( 'category', 'post_tag' ), // default locations
			),
			'with multiple locations'    => array(
				true,
				array( 'provider', 'client', 'state' ),
			),
		);
	}

	protected function get_modify_filter_hook_name( string $location ): string {
		return sprintf( self::MODIFY_FILTER, 'edit-' . $location );
	}

	protected function get_populate_filter_hook_name( string $location ): string {
		return sprintf( self::POPULATE_FILTER, $location );
	}

	protected function get_populate_output( string $column_name, int $object_id ): string {
		// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-terms-list-table.php#L638
		return apply_filters( $this->get_populate_filter_hook_name( $this->default['location'] ), '', $column_name, $object_id );
	}
}
