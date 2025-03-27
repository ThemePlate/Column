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

	protected array $config = array();


	public function __construct( string $title, callable $callback, array $config = array() ) {

		$this->title    = $title;
		$this->callback = $callback;

		$this->initialize( $config );

	}


	protected function initialize( array $config ): void {

		$this->column_key = strtolower( str_replace( array( ' ', '_' ), '-', $this->title ) );

		$this->config( $config );

	}


	public function config( array $config ): self {

		$this->config = array_merge( $this->defaults, $this->config, $config );

		$this->callback_args = $this->config['callback_args'];

		return $this;

	}


	public function position( int $position ): self {

		$this->config['position'] = $position;

		return $this;

	}


	public function args( array $args ): self {

		$this->callback_args = $args;

		return $this;

	}


	public function class( string $classname ): self {

		$this->config['class'] = $classname;

		return $this;

	}


	public function init(): void {

		$args = ( $this instanceof PopulateActionInterface ) ? 2 : 3;

		$this->column_key = trim( $this->column_key . ' ' . $this->config['class'] );

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
