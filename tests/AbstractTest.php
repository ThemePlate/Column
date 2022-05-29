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

	abstract protected function get_default_location_modify_filter_hook_name(): string;

	abstract protected function get_default_location_populate_filter_hook_name(): string;

	abstract protected function get_default_location_populate_output( string $column_name, int $object_id ): string;

	/**
	 * @dataProvider for_firing_init_actually_add_hooks
	 */
	public function test_firing_init_actually_add_hooks( bool $location, array $modifies, array $populates ): void {
		$column = $this->get_tested_class( $this->default['id'], $this->default['callback'] );

		if ( $location ) {
			/** @var HasLocation $column */
			$column->location( $this->default['location'] );
		}

		$column->init();

		foreach ( $modifies as $modify ) {
			$hook_name = sprintf( self::MODIFY_FILTER, $modify );

			$this->assertSame( 10, has_filter( $hook_name, array( $column, 'modify' ) ) );
		}

		foreach ( $populates as $populate ) {
			$hook_name = sprintf( self::POPULATE_FILTER, $populate );

			$this->assertSame( 10, has_action( $hook_name, array( $column, 'populate' ) ) );
		}
	}

	/**
	 * @dataProvider for_modify_columns
	 */
	public function test_modify_columns( string $class, string $key, int $position ): void {
		$config = array(
			'title'    => $this->default['title'],
			'position' => $position,
			'class'    => $class,
		);
		$column = $this->get_tested_class( $this->default['id'], $this->default['callback'], $config );

		if ( method_exists( $column, 'location' ) ) {
			$column->location( $this->default['location'] );
		}

		$column->init();

		$output = apply_filters( $this->get_default_location_modify_filter_hook_name(), $this->columns );
		$expect = $position > 0 ? $position : count( $output ) - 1;

		$this->assertIsArray( $output );
		$this->assertArrayHasKey( $key, $output );
		$this->assertSame( $config['title'], $output[ $key ] );
		$this->assertSame( $expect, array_search( $config['title'], array_values( $output ), true ) );
	}

	public function test_populate_columns(): void {
		$column = $this->get_tested_class( $this->default['id'], $this->default['callback'] );

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

			$this->assertSame( $expect, $this->get_default_location_populate_output( $column_name, $object_id ) );
		}
	}
}
