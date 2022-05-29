<?php

/**
 * @package ThemePlate
 */

namespace Tests;

trait TestCommon {
	protected array $default = array(
		'id'       => 'test',
		'title'    => 'Test',
		'callback' => array( __CLASS__, 'column_tester' ),
		'location' => 'custom',
	);

	// https://core.trac.wordpress.org/browser/tags/6.0/src/wp-admin/includes/class-wp-posts-list-table.php#L739
	protected array $columns = array(
		'cb'     => '<input type="checkbox" />',
		'title'  => 'Title',
		'author' => 'Author',
		'date'   => 'Date',
	);

	public function for_modify_columns(): array {
		return array(
			'with class string'    => array( $this->default['title'], 'this', $this->default['id'] . ' this', 1 ),
			'with class numeric'   => array( $this->default['title'], '1', $this->default['id'] . ' 1', 2 ),
			'with no class set'    => array( $this->default['title'], '', $this->default['id'], 3 ),
			'with no position set' => array( $this->default['title'], '', $this->default['id'], 0 ),
			'with spaced title'    => array( 'Wanted Title', '', 'wanted-title', 0 ),
			'with underscores'     => array( 'I_WANT_THIS', '', 'i-want-this', 0 ),
			'with crazy string'    => array( ' $aw;Pv_ 1@2 ', '', '-$aw;pv--1@2-', 0 ),
		);
	}

	public static function column_tester( int $object_id ): void {
		echo $object_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
