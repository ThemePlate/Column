<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column;

use ThemePlate\Column\Interfaces\CommonInterface;
use ThemePlate\Column\Interfaces\PopulateActionInterface;
use ThemePlate\Column\Traits\CanPopulate;

abstract class BaseColumn implements CommonInterface {

	use CanPopulate;

	protected array $defaults = array(
		'position'      => 0,
		'callback_args' => array(),
		'class'         => '',
	);
	protected string $title;
	protected array $config;


	public function __construct( string $title, callable $callback, array $config = array() ) {

		$this->title    = $title;
		$this->callback = $callback;
		$this->config   = $this->check( $config );

	}


	protected function check( array $config ): array {

		$config = array_merge( $this->defaults, $config );
		$sluggy = strtolower( str_replace( array( ' ', '_' ), '-', $this->title ) );

		$this->column_key    = trim( $sluggy . ' ' . $config['class'] );
		$this->callback_args = $config['callback_args'];

		return $config;

	}


	public function init(): void {

		$args = ( $this instanceof PopulateActionInterface ) ? 2 : 3;

		foreach ( $this->context() as $item ) {
			add_filter( 'manage_' . $item['modify'] . '_columns', array( $this, 'modify' ) );
			add_action( 'manage_' . $item['populate'] . '_custom_column', array( $this, 'populate' ), 10, $args );
		}

	}


	abstract protected function context(): array;


	public function modify( array $columns ): array {

		$columns[ $this->column_key ] = $this->title;

		$position = $this->config['position'];

		if ( $position > 0 ) {
			$item    = array_slice( $columns, -1, 1, true );
			$start   = array_slice( $columns, 0, $position, true );
			$end     = array_slice( $columns, $position, count( $columns ) - 1, true );
			$columns = array_merge( $start, $item, $end );
		}

		return $columns;

	}

}
