<?php
/**
 * Created by PhpStorm.
 * User: guillaumevandecasteele
 * Date: 30/05/2017
 * Time: 11:30
 */
get_header(); ?>

    <div id="primary" class="site-content">
        <div id="content" role="main">

			<?php while ( have_posts() ) :
			the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
					<?php the_content(); ?>
                    <p>
                        <div id="respond">
							<?php echo $response; ?>
                            <form action="<?php the_permalink(); ?>" method="post">
								<?php
								$categories   = json_decode( wp_remote_retrieve_body( wp_remote_get( 'http://10.3.51.8:8280/services/hotel.HTTPEndpoint/products/categories', array( 'headers' => array( 'Accept' => 'application/json' ) ) ) ), false )->categories;
								$sortedOrders = array();
								if ( ! empty( $categories ) ) {
									foreach ( $categories->category as $category ) {
										$orders = json_decode( wp_remote_retrieve_body( wp_remote_get( 'http://10.3.51.8:8280/services/hotel.HTTPEndpoint/orders?category=' . $category->name, array( 'headers' => array( 'Accept' => 'application/json' ) ) ) ), false )->orders;
										if ( ! empty( $orders ) ) {
											foreach ( $orders->order as $order ) {
												if ( ! array_key_exists( $order->roomNumber, $sortedOrders ) || ! isset( $sortedOrders[ $order->roomNumber ] ) ) {
													$sortedOrders[ $order->roomNumber ] = array();
												}
												if ( ! array_key_exists( $category->name, $sortedOrders[ $order->roomNumber ] ) || ! isset( $sortedOrders[ $order->roomNumber ][ $category-name ] ) ) {
													$sortedOrders[ $order->roomNumber ][ $category->name ] = array();
												}
												array_push( $sortedOrders[ $order->roomNumber ][ $category->name ], $order );
											}
										}
									}
									echo "<table>";
									foreach ( $sortedOrders as $roomNumber => $sortedCategories ) {
										echo "<tr><th colspan='4'>Room $roomNumber</th></tr>";
										foreach ( $sortedCategories as $category => $orders ) {
											echo "<tr><th colspan='4'>" . ucfirst( $category ) . "</th></tr>";
											echo "<tr><th>Name</th><th>Created on</th><th>Created by</th><th>Processed</th></tr>";
											foreach ( $orders as $order ) {
												echo "<tr><td>" . ucfirst( $order->name ) . "</td>";
												echo "<td>" . ucfirst( $order->createdOn ) . "</td>";
												echo "<td>" . ucfirst( $order->createdBy ) . "</td>";
												echo "<td><input type='checkbox' name='toProcess[]' value='$order->id'></td></tr>";
											}
										}
									}
									echo "</table>";
								}
								?>
                                <p><input type="submit"></p>
                            </form>
                        </div>
                    </p>
                </div><!-- .entry-content -->

            </article><!-- #post -->

		    <?php endwhile; // end of the loop. ?>

        </div><!-- #content -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
