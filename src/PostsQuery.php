<?php
namespace Cvy\WP\PostsQuery;

use \Exception;
use \WP_Query;

class PostsQuery extends \Cvy\WP\ObjectsQuery\ObjectsQuery
{
  protected $wp_query = null;

  public function __construct( array $args )
  {
    parent::__construct( $args );

    $this->args['fields'] = 'ids';
  }

  final public function get_wp_query() : WP_Query
  {
    if ( ! isset( $this->wp_query ) )
    {
      throw new Exception( 'WP Query is not inited or its execution is not completed yet!' );
    }

    return $this->wp_query;
  }

  final protected function execute() : array
  {
    $this->wp_query = new WP_Query( $this->args );

    return $this->wp_query->posts;
  }

  public function patch( array $args, array $merge_strategy = [] ) : void
  {
    if ( isset( $args['fields'] ) )
    {
      throw new Exception( '"fields" arg must not be patched!' );
    }

    parent::patch( $args, $merge_strategy );
  }
}
