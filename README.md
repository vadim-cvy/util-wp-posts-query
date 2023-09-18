_# util-wp-posts-query

A wrapper for WP_Query

## Usage

### Example 1

```php
// your-plugin-dir/MyCustomPostTypeQuery.php

class MyCustomPostTypeQuery extends \Cvy\WP\PostsQuery\PostsQuery
{
  // You may override constructor and set your custom default args like this:
  public function __constructor( array $args = [] )
  {
    $my_defaults = [
      'post_type' => 'my_custom_post_post_type',
    ];

    parent::__constructor( static::merge_args( $my_defaults, $args ) );
  }
```

```php
// your-plugin-dir/foo-bar.php

$query = new MyCustomPostTypeQuery([
  'posts_per_page' => 20,
]);

$ids = $query->get_results();

// ...
```

### Example 2

```php
// your-plugin-dir/HotelsQuery.php

class HotelsQuery extends \Cvy\WP\PostsQuery\PostsQuery
{
  // You may override constructor and set your custom default args like this:
  public function __constructor( array $args = [] )
  {
    $my_defaults = [
      'post_type' => 'my_hotels_post_type',
      // Query will return only approved hotels by default
      'meta_query' => [[
        'key' => 'is_approved',
        'value' => '1',
      ]],
    ];

    parent::__constructor( static::merge_args( $my_defaults, $args ) );
  }

  // You may create your custom shorthand for excluding unavailable hotels like this:
  public function set_available_only() : void
  {
    $this->patch([
      'meta_query' => [[
        'key' => 'is_available',
        'value' => '1',
      ]],
    ]);
  }

  // You may create your custom shorthand for updating sorting order like this:
  public function set_order( $order, $direction = 'DESC' ) : void
  {
    // Your predefined sorting orders
    switch ( $order )
    {
      case 'reviews':
        $this->patch([
          // your rules here
        ]);
      // ...
    }
  }
}
```

```php
// your-plugin-dir/top-5-hotels.php

$query = new HotelsQuery([
  'posts_per_page' => 5,
]);

$query->set_order( 'reviews' );

$ids = $query->get_results();

// ...
```

```php
// your-plugin-dir/available-hotels-sorted-by-reviews-from-worse-to-best.php

$query = new HotelsQuery();
$query->set_available_only();
$query->set_order( 'reviews', 'ASC' );

$ids = $query->get_results();

// ...
```

### Example 3

```php
// your-theme-dir/PlayersQuery.php

class PlrayersQuery extends \Cvy\WP\PostsQuery\PostsQuery
{
  protected $hide_banned_players = true;

  // You may override constructor and set your custom default args like this:
  public function __constructor( array $args = [] ) : array
  {
    $my_defaults = [
      'post_type' => 'my_players_post_type',
      // WP_Query instance will contain 'is_inited_by_me' query var.
      'is_inited_by_me' => true,
    ];

    parent::__constructor( static::merge_args( $my_defaults, $args ) );
  }

  // Setter for $hide_banned_players
  public function set_hide_banned_players( bool $hide_banned_players )
  {
    $this->hide_banned_players = $hide_banned_players;
  }

  // Returns query args that should be used to hide banned players
  static public function get_query_args__hide_banned() : array
  {
    return [
      'meta_query' => [[
        'key' => 'has_ban',
        'value' => '1',
        'compare' => '!=',
      ]],
    ];
  }

  public function get_results() : array
  {
    // Maybe patch query args for excluding banned players before query execution
    if ( $this->hide_banned_players )
    {
      $this->patch( static::get_query_args__hide_banned() );
    }

    return parent::get_results();
  }
}
```

```php
// your-theme-dir/functions.php

/*
 * Hide banned players for all WP_Query instances that are not created inside of PlrayersQuery class.
 *
 * You may face such WP_Query(ies)
 *   - on post type archive page;
 *   - in page builder posts grid elements (like BeaverBuilder posts grid module, etc).
 */
add_action( 'pre_get_posts', function( \WP_Query $wp_query ) : void
{
  // Check if WP_Query is created by our PlrayersQuery and hide banned players if one is not
  if ( ! $wp_query->get_var( 'is_inited_by_me' ) )
  {
    $wp_query->query_vars = PlayersQuery::merge_args(
      $wp_query->query_vars,
      PlayersQuery::get_query_args__hide_banned()
    );
  }
});
```

```php
// your-theme-dir/all-players-including-banned.php

$query = new PlrayersQuery([
  'posts_per_page' => -1,
]);

// Our "pre_get_posts" hook won't affect this query as one will contain "is_inited_by_me" arg
$ids = $query->get_results();
```

## Installation
See [Loading a package from a VCS repository](https://getcomposer.org/doc/05-repositories.md#loading-a-package-from-a-vcs-repository).
_
