<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Column\Interfaces\CommonInterface;
use ThemePlate\Column\Traits\HasLocation;
use WP_UnitTestCase;

abstract class AbstractTest extends WP_UnitTestCase {
	use TestCommon;

	public const MODIFY_FILTER = 'manage_%s_columns';

	public const POPULATE_FILTER = 'manage_%s_custom_column';

	abstract protected function get_tested_class( string $identifier, callable $callback, array $config = array() ): CommonInterface;

	abstract public function for_firing_init_actually_add_hooks(): array;

	abstract protected function factory_create_object(): int;

	abstract protected function get_modify_filter_hook_name( string $location ): string;

	abstract protected function get_populate_filter_hook_name( string $location ): string;

	abstract protected function get_populate_output( string $column_name, int $object_id ): string;

	/**
	 * @dataProvider for_firing_init_actually_add_hooks
	 */
	public function test_firing_init_actually_add_hooks( bool $has_location, array $locations ): void {
		$column = $this->get_tested_class( $this->default['title'], $this->default['callback'] );

		if ( $has_location ) {
			/** @var HasLocation $column */
			foreach ( $locations as $location ) {
				$column->location( $location );
			}
		}

		$column->init();

		foreach ( $locations as $location ) {
			$this->assertSame( 10, has_filter( $this->get_modify_filter_hook_name( $location ), array( $column, 'modify' ) ) );
			$this->assertSame( 10, has_action( $this->get_populate_filter_hook_name( $location ), array( $column, 'populate' ) ) );
		}
	}

	/**
	 * @dataProvider for_modify_columns
	 */
	public function test_modify_columns( string $title, string $class, string $column_key, int $position ): void {
		$config = array(
			'position' => $position,
			'class'    => $class,
		);
		$column = $this->get_tested_class( $title, $this->default['callback'], $config );

		if ( method_exists( $column, 'location' ) ) {
			$column->location( $this->default['location'] );
		}

		$column->init();

		$output = apply_filters( $this->get_modify_filter_hook_name( $this->default['location'] ), $this->columns );
		$expect = $position > 0 ? $position : count( $output ) - 1;

		$this->assertIsArray( $output );
		$this->assertArrayHasKey( $column_key, $output );
		$this->assertSame( $title, $output[ $column_key ] );
		$this->assertSame( $expect, array_search( $title, array_values( $output ), true ) );
	}

	public function test_populate_columns(): void {
		$column = $this->get_tested_class( $this->default['title'], $this->default['callback'] );

		if ( method_exists( $column, 'location' ) ) {
			$column->location( $this->default['location'] );
		}

		$column->init();

		$column_names = array_merge( array_keys( $this->columns ), array( $this->default['id'] ) );
		$object_id    = $this->factory_create_object();

		foreach ( $column_names as $column_name ) {
			$expect = ''; // Current column not ours

			if ( $this->default['id'] === $column_name ) {
				$expect = (string) $object_id;
			}

			$this->assertSame( $expect, $this->get_populate_output( $column_name, $object_id ) );
		}
	}
}
