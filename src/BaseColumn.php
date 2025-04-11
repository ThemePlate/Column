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

	/**
	 * @var array{
	 *     position: int,
	 *     callback_args: string[],
	 *     class: string
	 * }
	 */
	protected array $defaults = array(
		'position'      => 0,
		'callback_args' => array(),
		'class'         => '',
	);
	protected string $title;

	/** @var array<string, mixed> */
	protected array $config = array();


	/** @param array<string, mixed> $config */
	public function __construct( string $title, ?callable $callback = null, array $config = array() ) {

		$this->title = $title;

		if ( null !== $callback ) {
			$this->callback( $callback );
		}

		$this->column_key = strtolower( str_replace( array( ' ', '_' ), '-', $this->title ) );

		$this->config( $config );

	}


	public function callback( callable $callback ): self {

		$this->callback = $callback;

		return $this;

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


	/** @param string[] $args */
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
			// @phpstan-ignore argument.type
			add_action( 'manage_' . $item['populate'] . '_custom_column', array( $this, 'populate' ), 10, $args );
		}

	}


	/** @return array<int, array{modify: string, populate: string}> */
	abstract protected function context(): array;


	/**
	 * @param array<string, string> $columns
	 * @return array<string, string>
	 */
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
