# ThemePlate Column

## Usage

```php
use ThemePlate\Column\PostTypeColumn;
use ThemePlate\Column\TaxonomyColumn;
use ThemePlate\Column\UsersColumn;

function pretty_print( $object_id ) {
	echo '&hearts;&nbsp;<b>' . $object_id . '</b> &mdash;&rsaquo;';
};

( new PostTypeColumn( 'Post ID', 'pretty_print' ) )->init();
( new TaxonomyColumn( 'Term ID', 'pretty_print' ) )->init();
( new UsersColumn( 'User ID', 'pretty_print' ) )->init();
```

### Available config
```php
$defaults = array(
	'position'      => 0,
	'callback_args' => array(),
	'class'         => '',
);
```

### Specific location
```php
( new PostTypeColumn( 'Model', $my_callback ) )->location( 'custom_post_type' )->init();
( new TaxonomyColumn( 'Value', $my_callback ) )->location( 'tax_1' )->location( 'tax_2' )->init();
```
