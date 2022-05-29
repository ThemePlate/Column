<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column;

use ThemePlate\Column\Interfaces\CommonInterface;
use ThemePlate\Column\Traits\CanPopulate;

abstract class BaseColumn implements CommonInterface {

	use CanPopulate;

	protected array $defaults = array(
		'position'      => 0,
		'callback_args' => array(),
		'class'         => '',
	);
	protected string $identifier;
	protected array $config;


	public function __construct( string $identifier, callable $callback, array $config = array() ) {

		$this->identifier = $identifier;
		$this->callback   = $callback;
		$this->config     = $this->check( $config );

	}


	protected function check( array $config ): array {

		$config['title'] = $this->maybe_convert( $config['title'] ?? '' );

		$config = array_merge( $this->defaults, $config );

		$config['key'] = trim( $this->identifier . ' ' . $config['class'] );

		$this->column_key    = $config['key'];
		$this->callback_args = $config['callback_args'];

		return $config;

	}


	protected function maybe_convert( string $title ): string {

		if ( '' !== $title ) {
			return $title;
		}

		return mb_convert_case( $this->identifier, MB_CASE_TITLE, 'UTF-8' );

	}


	public function init(): void {

		$args = static::class === PostTypeColumn::class ? 2 : 3;

		foreach ( $this->context() as $item ) {
			add_filter( 'manage_' . $item['modify'] . '_columns', array( $this, 'modify' ) );
			add_action( 'manage_' . $item['populate'] . '_custom_column', array( $this, 'populate' ), 10, $args );
		}

	}


	abstract protected function context(): array;


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

}
