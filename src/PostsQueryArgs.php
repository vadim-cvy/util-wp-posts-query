<?php
namespace Cvy\WP\PostsQuery;

class PostsQueryArgs extends \Cvy\WP\ObjectsQuery\ObjectsQueryArgs
{
  static public function merge( array $query_args_a, array $query_args_b, array $merge_strategy = [] ) : array
  {
    // todo: handle order by

    $tax_query = static::merge_tax_query(
      static::normalize_tax_query( $query_args_a )['tax_query'],
      static::normalize_tax_query( $query_args_b )['tax_query'],
      $merge_strategy['tax_query_default_relation'] ?? 'AND',
    );

    $merged_args = parent::merge( $query_args_a, $query_args_b );

    $merged_args['tax_query'] = $tax_query;

    return $merged_args;
  }

  static protected function normalize_tax_query( array $query_args ) : array
  {
    $query_args['tax_query'] = $query_args['tax_query'] ?? [];

    return $query_args;
  }

  static protected function merge_tax_query( array $a, array $b, string $default_merge_relation ) : array
  {
    /**
     * Tax query merge works the same as meta query merge.
     * No need to reinvent the wheel
     */
    return static::merge_meta_query( $a, $b, $default_merge_relation );
  }
}
