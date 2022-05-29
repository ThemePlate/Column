<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class Column {

	protected array $defaults = array(
		'location'      => 'post_type',
		'specific'      => '',
		'position'      => 0,
		'callback_args' => array(),
		'class'         => '',
	);
	protected string $identifier;
	/**
	 * @var callable
	 */
	protected $callback;
	protected array $config;


	public const LOCATIONS = array(
		'post_type',
		'taxonomy',
		'users',
	);


	public function __construct( string $identifier, callable $callback, array $config = array() ) {

		$this->identifier = $identifier;
		$this->callback   = $callback;
		$this->config     = $this->check( $config );

	}


	protected function check( array $config ): array {

		$config['title']    = $this->maybe_convert( $config['title'] ?? '' );
		$config['location'] = $this->fool_proof( $config['location'] ?? '' );

		$config = array_merge( $this->defaults, $config );

		$config['key'] = trim( $this->identifier . ' ' . $config['class'] );

		return $config;

	}


	protected function maybe_convert( string $title ): string {

		if ( '' !== $title ) {
			return $title;
		}

		return mb_convert_case( $this->identifier, MB_CASE_TITLE, 'UTF-8' );

	}


	protected function fool_proof( string $location ): string {

		$location = strtolower( $location );

		if ( ! in_array( $location, self::LOCATIONS, true ) ) {
			$location = 'post_type';
		}

		return $location;

	}


	public function init(): void {

		foreach ( $this->context() as $item ) {
			add_filter( 'manage_' . $item['modify'] . '_columns', array( $this, 'modify' ), 10 );
			add_action( 'manage_' . $item['populate'] . '_custom_column', array( $this, 'populate' ), 10, 3 );
		}

	}


	protected function context(): array {

		$config  = $this->config;
		$context = array();

		if ( 'post_type' === $config['location'] ) {
			if ( ! empty( $config['specific'] ) ) {
				$context[0]['modify']   = $config['specific'] . '_posts';
				$context[0]['populate'] = $config['specific'] . '_posts';
			} else {
				$context[0]['modify']   = 'posts';
				$context[0]['populate'] = 'posts';
				$context[1]['modify']   = 'pages';
				$context[1]['populate'] = 'pages';
			}
		} elseif ( 'taxonomy' === $config['location'] ) {
			if ( ! empty( $config['specific'] ) ) {
				$context[0]['modify']   = 'edit-' . $config['specific'];
				$context[0]['populate'] = $config['specific'];
			} else {
				$taxonomies   = get_taxonomies( array( '_builtin' => false ) );
				$taxonomies[] = 'category';
				$taxonomies[] = 'post_tag';

				foreach ( $taxonomies as $index => $taxonomy ) {
					$context[ $index ]['modify']   = 'edit-' . $taxonomy;
					$context[ $index ]['populate'] = $taxonomy;
				}
			}
		} elseif ( 'users' === $config['location'] ) {
			$context[0]['modify']   = 'users';
			$context[0]['populate'] = 'users';
		}

		return $context;

	}


	public function modify( array $columns ): array {

		$config = $this->config;

		$columns[ $config['key'] ] = $config['title'];

		$position = $config['position'];

		if ( $position > 0 ) {
			$item    = array_slice( $columns, -1, 1, true );
			$start   = array_slice( $columns, 0, $position, true );
			$end     = array_slice( $columns, $position, count( $columns ) - 1, true );
			$columns = array_merge( $start, $item, $end );
		}

		return $columns;

	}


	public function populate( $content_name, string $name_id, int $object_id = 0 ) {

		$config = $this->config;

		if ( 'post_type' === $config['location'] ) {
			$column_name = $content_name;
			$object_id   = (int) $name_id;
		} else {
			$column_name = $name_id;
		}

		if ( $column_name !== $config['key'] ) {
			return $content_name;
		}

		if ( 'post_type' === $config['location'] ) {
			return call_user_func( $this->callback, $object_id, $config['callback_args'] );
		}

		ob_start();
		call_user_func( $this->callback, $object_id, $config['callback_args'] );

		return ob_get_clean();

	}

}
