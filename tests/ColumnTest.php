<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Column;
use WP_UnitTestCase;

class ColumnTest extends WP_UnitTestCase {
	private array $default = array(
		'id'       => 'test',
		'title'    => 'Tester',
		'callback' => array( __CLASS__, 'column_tester' ),
	);

	// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-posts-list-table.php#L739
	private array $columns = array(
		'cb'     => '<input type="checkbox" />',
		'title'  => 'Title',
		'author' => 'Author',
		'date'   => 'Date',
	);

	public function for_instantiating_actually_add_hooks(): array {
		return array(
			'with post type specified' => array(
				'post_type',
				'custom',
				array( 'custom_posts' ),
				array( 'custom_posts' ),
			),
			'with post type but empty' => array(
				'post_type',
				'',
				array( 'posts', 'pages' ),
				array( 'posts', 'pages' ),
			),
			'with taxonomy specified'  => array(
				'taxonomy',
				'custom',
				array( 'edit-custom' ),
				array( 'custom' ),
			),
			'with taxonomy but empty'  => array(
				'taxonomy',
				'',
				array( 'edit-category', 'edit-post_tag' ),
				array( 'category', 'post_tag' ),
			),
			'with users specified'     => array(
				'users',
				'custom',
				array( 'users' ),
				array( 'users' ),
			),
			'with users but empty'     => array(
				'users',
				'',
				array( 'users' ),
				array( 'users' ),
			),
		);
	}

	/**
	 * @dataProvider for_instantiating_actually_add_hooks
	 */
	public function test_instantiating_actually_add_hooks( string $location, string $specific, array $modifies, array $populates ): void {
		$column = new Column( $this->default['id'], $this->default['callback'], compact( 'location', 'specific' ) );

		$column->init();

		foreach ( $modifies as $modify ) {
			$this->assertSame( 10, has_filter( 'manage_' . $modify . '_columns', array( $column, 'modify' ) ) );
		}

		foreach ( $populates as $populate ) {
			$this->assertSame( 10, has_action( 'manage_' . $populate . '_custom_column', array( $column, 'populate' ) ) );
		}
	}

	public function for_modify_columns(): array {
		return array(
			'with class string'    => array( 'this', 'test this', 1 ),
			'with class numeric'   => array( '1', 'test 1', 2 ),
			'with no class set'    => array( '', 'test', 3 ),
			'with no position set' => array( '', 'test', 0 ),
		);
	}

	/**
	 * @dataProvider for_modify_columns
	 */
	public function test_modify_columns( string $class, string $key, int $position ): void {
		$config = array(
			'title'    => $this->default['title'],
			'location' => 'post_type',
			'specific' => 'custom',
			'position' => $position,
			'class'    => $class,
		);
		( new Column( $this->default['id'], $this->default['callback'], $config ) )->init();

		$output = apply_filters( 'manage_custom_posts_columns', $this->columns );
		$expect = $position > 0 ? $position : count( $output ) - 1;
		$this->assertIsArray( $output );
		$this->assertArrayHasKey( $key, $output );
		$this->assertSame( $config['title'], $output[ $key ] );
		$this->assertSame( $expect, array_search( $config['title'], array_values( $output ), true ) );
	}

	public function for_populate_columns(): array {
		return array(
			'with post type' => array( 'post_type' ),
			'with taxonomy'  => array( 'taxonomy' ),
			'with users'     => array( 'users' ),
		);
	}

	/**
	 * @dataProvider for_populate_columns
	 */
	public function test_populate_columns( string $type ): void {
		$config = array(
			'location' => $type,
			'specific' => 'custom',
		);
		( new Column( $this->default['id'], $this->default['callback'], $config ) )->init();

		$column_names = array_merge( array_keys( $this->columns ), array( $this->default['id'] ) );
		$object_id    = 0;
		$output       = 0;

		foreach ( $column_names as $column_name ) {
			if ( $this->default['id'] !== $column_name ) {
				continue;
			}

			if ( 'post_type' === $type ) {
				$object_id = $this->factory()->post->create();

				ob_start();
				// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-posts-list-table.php#L1349
				do_action( 'manage_custom_posts_custom_column', $column_name, $object_id );

				$output = ob_get_clean();
			} elseif ( 'taxonomy' === $type ) {
				$object_id = $this->factory()->term->create();
				// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-terms-list-table.php#L638
				$output = apply_filters( 'manage_custom_custom_column', '', $column_name, $object_id );
			} elseif ( 'users' === $type ) {
				$object_id = $this->factory()->user->create();
				// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-users-list-table.php#L615
				$output = apply_filters( 'manage_users_custom_column', '', $column_name, $object_id );
			}
		}

		$this->assertSame( (string) $object_id, $output );
	}

	public static function column_tester( int $object_id ): void {
		echo $object_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
