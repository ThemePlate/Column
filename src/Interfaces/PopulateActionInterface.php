<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Interfaces;

interface PopulateActionInterface {

	public function populate( string $column_name, int $object_id ): void;

}
