<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use Exception;
use ThemePlate\Core\Helper\Main;

class Column {

	private array $config;


	public function __construct( array $config ) {

		$expected = array(
			'id',
			'title',
			'callback',
			array(
				'post_type',
				'taxonomy',
				'users',
			),
		);

		if ( ! Main::is_complete( $config, $expected ) ) {
			throw new Exception();
		}

		$defaults     = array(
			'position'      => 0,
			'callback_args' => array(),
			'class'         => '',
		);
		$this->config = Main::fool_proof( $defaults, $config );

		$context = $this->context();

		foreach ( $context['list'] as $item ) {
			add_filter( 'manage_' . $item['modify'] . '_columns', array( $this, 'modify' ), 10 );
			add_action( 'manage_' . $item['populate'] . '_custom_column', array( $this, 'populate' ), 10, 3 );
		}

	}


	private function context(): array {

		$config  = $this->config;
		$context = array();

		if ( isset( $config['post_type'] ) ) {
			$context['type'] = 'post_type';

			if ( ! empty( $config['post_type'] ) ) {
				$context['list'][0]['modify']   = $config['post_type'] . '_posts';
				$context['list'][0]['populate'] = $config['post_type'] . '_posts';
			} else {
				$context['list'][0]['modify']   = 'posts';
				$context['list'][0]['populate'] = 'posts';
				$context['list'][1]['modify']   = 'pages';
				$context['list'][1]['populate'] = 'pages';
			}
		} elseif ( isset( $config['taxonomy'] ) ) {
			$context['type'] = 'taxonomy';

			if ( ! empty( $config['taxonomy'] ) ) {
				$context['list'][0]['modify']   = 'edit-' . $config['taxonomy'];
				$context['list'][0]['populate'] = $config['taxonomy'];
			} else {
				$taxonomies   = get_taxonomies( array( '_builtin' => false ) );
				$taxonomies[] = 'category';
				$taxonomies[] = 'post_tag';

				foreach ( $taxonomies as $index => $taxonomy ) {
					$context['list'][ $index ]['modify']   = 'edit-' . $taxonomy;
					$context['list'][ $index ]['populate'] = $taxonomy;
				}
			}
		} elseif ( isset( $config['users'] ) ) {
			$context['type']                = 'users';
			$context['list'][0]['modify']   = 'users';
			$context['list'][0]['populate'] = 'users';
		}

		$this->config['context'] = $context;

		return $context;

	}


	public function modify( array $columns ): array {

		$config = $this->config;
		$column = trim( $config['id'] . ' ' . $config['class'] );

		$columns[ $column ] = $config['title'];

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

		if ( 'post_type' === $config['context']['type'] ) {
			$column_name = $content_name;
			$object_id   = (int) $name_id;
		} else {
			$column_name = $name_id;
		}

		$wanted = trim( $config['id'] . ' ' . $config['class'] );

		if ( $column_name !== $wanted ) {
			return $content_name;
		}

		if ( 'post_type' === $config['context']['type'] ) {
			return call_user_func( $config['callback'], $object_id, $config['callback_args'] );
		}

		ob_start();
		call_user_func( $config['callback'], $object_id, $config['callback_args'] );

		return ob_get_clean();

	}

}
