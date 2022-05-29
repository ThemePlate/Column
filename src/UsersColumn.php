<?php

/**
 * Setup admin columns
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Column;

use ThemePlate\Column\Interfaces\PopulateFilterInterface;
use ThemePlate\Column\Traits\PopulateWillReturn;

class UsersColumn extends BaseColumn implements PopulateFilterInterface {

	use PopulateWillReturn;


	protected function context(): array {

		$context = array();

		$context[0]['modify']   = 'users';
		$context[0]['populate'] = 'users';

		return $context;

	}

}
