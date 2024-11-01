<?php

/*
Plugin Name: Woocommerce Hot products by Category and Vendor
Plugin URI: http://suoling.net/woocommerce-hot-products-by-category-and-vendor
Description: This is a Wordpress Widget that displays a specified number of best sold products by the same vendor  in the same category on the single/vendor page,and displays a specified number of best sold products in the same category on others page.
Version: 1.0
Author: Suifengtec
Author URI: http://suolling.net
Text Domain: cwp_hot_product_for_vendor
Domain Path: /lang/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/*
Notice: Recommended used with a multi-seller woocommerce add-on,More info:
http://suoling.net/woocommerce-hot-products-by-category-and-vendor
This is a Wordpress Widget that displays a specified number of best sold products by the same vendor  in the same category on the single/vendor page,and displays a specified number of best sold products in the same category on others page.
*/

$active_plugins = get_option( 'active_plugins', array() );
$is_woocommerce_activated  = in_array( 'woocommerce/woocommerce.php', $active_plugins );
if ( $is_woocommerce_activated ) {
	class Hot_Products_by_Vendor extends WP_Widget {
		public function __construct() {
			add_action( 'init', array( $this, 'cwp_load_widget_textdomain' ) );

			parent::__construct(
				'cwp-hot-product-for-vendor',
				__( 'Hot Product for Vendor Widget', 'cwp_hot_product_for_vendor' ),
				array(
					'classname'		=>	'cwp-hot-product-for-vendor',
					'description'	=>	__( 'Widget that displays a specified number of best sold products in the same category.', 'cwp_hot_product_for_vendor' )
				)
			);

			$this->init_plugin_constants();
			add_action( 'wp_enqueue_scripts', array( $this, 'cwp_enqueue_woo_hot_products_widget_styles' ) );
		}


		public function cwp_enqueue_woo_hot_products_widget_styles() {
			wp_enqueue_style( 'cwp_enqueue_woo_hot_products_widget_styles', plugins_url( 'woocommerce-hot-products-by-seller-and-category/css/style.css' ) );
		}

		public function widget( $args, $instance ) {

			extract( $args, EXTR_SKIP );

			$title = empty($instance['title']) ? '' : apply_filters('title', $instance['title']);
	    $category_id = empty($instance['category_id']) ? '' : apply_filters('category_id', $instance['category_id']);
	    $display_count = empty($instance['display_count']) ? '' : apply_filters('display_count', $instance['display_count']);

	    global $post;
	    $terms = get_the_terms( $post->ID, 'product_cat' );
	    $best_sellers = array();
	    $best_seller_categories = array();
	    $this_id = array($post->ID);
	    foreach ($terms as $term) {
	      if (term_is_ancestor_of($category_id, $term, 'product_cat')) {
	        $product_cat_id = $term->term_id;
	        $product_cat_name = $term->name;
	        $product_cat_slug = $term->slug;
			$vendor_id=$post->post_author;
			if(isset($vendor_id)!=''){
		        $args = array(
					'post_type' => 'product',
					'post_author' => $vendor_id,
					'posts_per_page' => $display_count,
					'post_status' 	 => 'publish',
					'product_cat' => $product_cat_slug,
					'post__not_in' => $this_id,
					'meta_key' 		 => 'total_sales',
					'orderby' 		 => 'meta_value_num',
					'order' => 'DESC',
		        );
			}else{
		        $args = array(
					'post_type' => 'product',
					'posts_per_page' => $display_count,
					'post_status' 	 => 'publish',
					'product_cat' => $product_cat_slug,
					'post__not_in' => $this_id,
					'meta_key' 		 => 'total_sales',
					'orderby' 		 => 'meta_value_num',
					'order' => 'DESC',
		        );
	        }
	        $partner_products = new WP_Query( $args );

	        if ( $partner_products->have_posts() ) {
	          $best_seller_categories[$product_cat_id] = array(
	            'name' => $product_cat_name,
	            'slug' => $product_cat_slug
	          );

	          $best_sellers[$product_cat_id] = array();
	          global $product;
	          while ( $partner_products->have_posts() ) {
	            $partner_products->the_post();
	            $img = null;
	            if (has_post_thumbnail( $partner_products->post->ID )) {
	              $img = get_the_post_thumbnail($partner_products->post->ID, 'small');
	            }

	            $best_sellers[$product_cat_id][$partner_products->post->ID] = array(
	              'name' => $partner_products->post->post_title,
	              'img' => $img,
	              'price' => get_post_meta( $partner_products->post->ID, '_regular_price', true),
	              'sale_price' => get_post_meta( $partner_products->post->ID, '_sale_price', true)
	            );
	          }
	        }
	      }
	    }
		include( plugin_dir_path( __FILE__ ) . '/inc/widget.php' );
		}


		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

	    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
	    $instance['category_id'] = strip_tags(stripslashes($new_instance['category_id']));
	    $instance['display_count'] = strip_tags(stripslashes($new_instance['display_count']));

			return $instance;

		} // end widget


		public function form( $instance ) {
			$instance = wp_parse_args(
				(array) $instance,
				array(
				  'title' => '',
				  'category_id' => 13,
				  'display_count' => 5
				)
			);

	    $args = array(
				'hierarchical'       => 1,
				'show_option_none'   => '',
				'hide_empty'         => 0,
				'orderby'                  => 'name',
				'taxonomy'           => 'product_cat'
			);
	  	$categories = get_categories($args);

			$new_instance = $instance;

			$title = strip_tags(stripslashes($new_instance['title']));
			$category_id = strip_tags(stripslashes($new_instance['category_id']));
			$display_count = strip_tags(stripslashes($new_instance['display_count']));
			include( plugin_dir_path(__FILE__) . '/inc/admin.php' );

		}

		private function init_plugin_constants() {
	    if(!defined('CWP_PLUGIN_WOO_HOT_SLUG'))
	      define('CWP_PLUGIN_WOO_HOT_SLUG', 'cwp_hot_product_for_vendor');
	  }


		public function cwp_load_widget_textdomain() {
			load_plugin_textdomain( 'cwp_hot_product_for_vendor', false, plugin_dir_path( __FILE__ ) . '/lang/' );
		}

	}

	add_action( 'widgets_init', create_function( '', 'register_widget("Hot_Products_by_Vendor");' ) );
} else {

	add_action( 'admin_notices', 'fue_admin_notice' );
	function fue_admin_notice() {
	    $wc_url = 'http://www.woothemes.com/woocommerce/';
	    $info_url = 'http://suoling.net/woocommerce-hot-products-by-category-and-vendor';
	    printf( '<div class="updated"><p>'. __( 'The plugin "Woocommerce Hot products by Category and Vendor" requires <a href="%1$s" target="_blank">WooCommerce</a> to be installed. <a href="%2$s" target="_blank">More info</a>', 'cwp_hot_product_for_vendor' ) .'</div>', $wc_url,$info_url );
	}

}
//ALL DONE.