# ThemePlate Column

## Usage

```php
use ThemePlate\Column;

function pretty_print( $object_id ) {
	echo '&hearts;&nbsp;<b>' . $object_id . '</b> &mdash;&rsaquo;';
};

$args = array(
	'id'       => 'test-column',
	'title'    => 'Tester',
	'callback' => 'pretty_print',
	// 'position'      => 0,
	// 'callback_args' => array(),
	// 'class'         => '',
);
```

### Post Type
```php
$args['post_type'] = 'posts';

new Column( $args );
```

### Taxonomy
```php
$args['taxonomy'] = 'category';

new Column( $args );
```

### Users
```php
$args['users'] = true;

new Column( $args );
```
