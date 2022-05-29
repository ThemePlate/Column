<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column\Interfaces;

interface PopulateFilterInterface {

	public function populate( string $output, string $column_name, int $object_id ): string;

}
