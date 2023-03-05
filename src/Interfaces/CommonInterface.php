<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Interfaces;

interface CommonInterface {

	public function __construct( string $title, callable $callback, array $config = array() );

	public function position( int $position ): self;

	public function args( array $args ): self;

	public function init(): void;

	public function modify( array $columns ): array;

}
