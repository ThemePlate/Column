<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Interfaces;

interface CommonInterface {

	/** @param array<string, mixed> $config */
	public function __construct( string $title, ?callable $callback = null, array $config = array() );

	public function callback( callable $callback ): self;

	/** @param array<string, mixed> $config */
	public function config( array $config ): self;

	public function position( int $position ): self;

	/** @param string[] $args */
	public function args( array $args ): self;

	public function class( string $classname ): self;

	public function init(): void;

	/**
	 * @param array<string, string> $columns
	 * @return array<string, string>
	 */
	public function modify( array $columns ): array;

}
