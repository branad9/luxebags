<?php

namespace Razzi\Addons\Modules\Product_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Settings  {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	const POST_TYPE     = 'razzi_product_tab';
	const OPTION_NAME   = 'razzi_product_tab';


	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array( $this, 'product_tabs_section' ), 30, 2 );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'product_tabs_settings' ), 30, 2 );

		// Make sure the post types are loaded for imports
		add_action( 'import_start', array( $this, 'register_post_type' ) );

		if ( get_option( 'razzi_product_tab' ) != 'yes' ) {
			return;
		}

		$this->register_post_type();

		$this->create_terms();

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Add options to product.
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta' ) );

		add_filter('woocommerce_screen_ids', array( $this, 'wc_screen_ids' ) );
	}

	/**
	 * Add Product Tabs settings section to the Products setting tab.
     *
	 * @since 1.0.0
	 *
	 * @param array $sections
	 * @return array
	 */
	public function product_tabs_section( $sections ) {
		$sections['razzi_addons_product_tabs'] = esc_html__( 'Product Tabs', 'razzi' );

		return $sections;
	}

	/**
	 * Adds a new setting field to products tab.
     *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function product_tabs_settings( $settings, $section ) {
		if ( 'razzi_addons_product_tabs' != $section ) {
			return $settings;
		}

		$settings_product_tabs = array(
			array(
				'name' => esc_html__( 'Product Tabs', 'razzi' ),
				'type' => 'title',
				'id'   => self::OPTION_NAME . '_options',
			),
			array(
				'name'    => esc_html__( 'Enable Product Tabs', 'razzi' ),
				'desc'    => esc_html__( 'Enable product tabs manager', 'razzi' ),
				'id'      => self::OPTION_NAME,
				'default' => 'no',
				'type'    => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'type' => 'sectionend',
				'id'   => self::OPTION_NAME . '_options',
			),
		);

		return $settings_product_tabs;
	}

	/**
	 * Register product tabs post type
     *
	 * @since 1.0.0
     *
     * @return void
	 */
	public function register_post_type() {
		if(post_type_exists(self::POST_TYPE)) {
			return;
		}

		register_post_type( self::POST_TYPE, array(
			'description'         => esc_html__( 'Product tabs', 'razzi' ),
			'labels'              => array(
				'name'                  => esc_html__( 'Product Tabs', 'razzi' ),
				'singular_name'         => esc_html__( 'Product Tabs', 'razzi' ),
				'menu_name'             => esc_html__( 'Product Tabs', 'razzi' ),
				'all_items'             => esc_html__( 'Product Tabs', 'razzi' ),
				'add_new'               => esc_html__( 'Add New', 'razzi' ),
				'add_new_item'          => esc_html__( 'Add New Product Tabs', 'razzi' ),
				'edit_item'             => esc_html__( 'Edit Product Tabs', 'razzi' ),
				'new_item'              => esc_html__( 'New Product Tabs', 'razzi' ),
				'view_item'             => esc_html__( 'View Product Tabs', 'razzi' ),
				'search_items'          => esc_html__( 'Search product tabs', 'razzi' ),
				'not_found'             => esc_html__( 'No product tabs found', 'razzi' ),
				'not_found_in_trash'    => esc_html__( 'No product tabs found in Trash', 'razzi' ),
				'filter_items_list'     => esc_html__( 'Filter product tabss list', 'razzi' ),
				'items_list_navigation' => esc_html__( 'Product tabs list navigation', 'razzi' ),
				'items_list'            => esc_html__( 'Product tabs list', 'razzi' ),
			),
			'supports'            => array( 'title', 'editor' ),
			'rewrite'             => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'show_in_menu'        => 'edit.php?post_type=product',
			'menu_position'       => 20,
			'capability_type'     => 'page',
			'query_var'           => is_admin(),
			'map_meta_cap'        => true,
			'exclude_from_search' => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'show_in_nav_menus'   => true,
			'taxonomies'          => array( 'product_cat' ),
		) );

		register_taxonomy(
			'razzi_product_tab',
			array( self::POST_TYPE ),
			array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false,
				'label'             => _x( 'Product Tabs', 'Taxonomy name', 'razzi' ),
			)
		);
	}

	/**
	 * Add custom column to product tabss management screen
	 * Add Thumbnail column
     *
	 * @since 1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		$columns = array_merge( $columns, array(
			'product_cat' => esc_html__( 'Categories', 'razzi' )
		) );

		return $columns;
	}

	/**
	 * Handle custom column display
     *
	 * @since 1.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'product_cat':
				$cats = get_the_terms( $post_id, 'product_cat' );
				$links = array();

				if ( ! is_wp_error( $cats ) && $cats && is_array( $cats ) ) {
					foreach ( $cats as $cat) {
						$links[] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_term_link( $cat->term_id, 'product_cat', 'product' ) ), $cat->name );

					}
				} else {
					$links[] = esc_html_e( 'No Category', 'razzi' );
				}

				echo implode( ', ', $links );
				break;
		}
	}

	/**
	 * Get option of product tabs.
     *
	 * @since 1.0.0
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_option( $option = '', $default = false ) {
		if ( ! is_string( $option ) ) {
			return $default;
		}

		if ( empty( $option ) ) {
			return get_option( self::OPTION_NAME, $default );
		}

		return get_option( sprintf( '%s_%s', self::OPTION_NAME, $option ), $default );
	}

	/**
	 * Add meta boxes
	 *
	 * @param object $post
	 */
	public function meta_boxes( $post ) {
		add_meta_box( 'razzi-product-tabs', esc_html__( 'Tabs Settings', 'razzi' ), array( $this, 'tabs_meta_box' ), self::POST_TYPE, 'advanced', 'high' );
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $post
	 */
	public function tabs_meta_box( $post ) {
		?>
		<div id="razzi-product-tabs-settings" class="razzi-product-tabs-settings">
			<p class="form-field">
				<label><?php esc_html_e('Tab Type', 'razzi'); ?></label>
				<input type="radio" id="razzi-product-tab-global" selected name="razzi_product_tab_type" value="global">
				<label for="razzi-product-tab-global"><?php esc_html_e('Global', 'razzi'); ?></label>
				<input type="radio" id="razzi-product-tab-product" name="razzi_product_tab_type" value="product">
				<label for="razzi-product-tab-product"><?php esc_html_e('Product', 'razzi'); ?></label>
				<input type="radio" id="razzi-product-tab-custom" name="razzi_product_tab_type" value="custom">
				<label for="razzi-product-tab-custom"><?php esc_html_e('Custom', 'razzi'); ?></label>
			</p>
			<p class="form-field">
				<label for="product-tab-id"><?php esc_html_e( 'Product', 'razzi' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="product-tab-id" name="razzi_product_tab_product_id[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'razzi' ); ?>" data-action="woocommerce_json_search_products">
					<?php
					$product_id = maybe_unserialize( get_post_meta( $post_id, 'razzi_product_tab_product_id', true ) );

					if ( $product_ids ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
						}
					}
					?>
				</select>
			</p>
			<p class="form-field">
				<label for="product_tab_categories"><?php esc_html_e( 'Categories', 'razzi' ); ?></label>
				<select class="wc-category-search" multiple="multiple" style="width: 50%;" id="product_tab_categories" name="product_tab_categories[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'razzi' ); ?>" data-action="woocommerce_json_search_categories">
					<?php
					$cat_slugs = maybe_unserialize( get_post_meta( $post_id, 'product_tab_categories', true ) );

					if ( $cat_slugs && is_array( $cat_slugs ) ) {
						foreach ( $cat_slugs as $cat_slug ) {
							$category = get_term_by( 'slug', $cat_slug, 'product_cat' );
							if ( is_object( $category ) ) {
								echo '<option value="' . esc_attr( $cat_slug ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $category->name ) . '(' . $category->count . ')' . '</option>';
							}
						}
					}
					?>
				</select>
			</p>
		</div>

		<?php
	}

	/**
	 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
	 */
	public function create_terms($taxonomy = 'razzi_product_tab') {
		$terms = array(
			'global',
			'custom',
		);

		foreach ( $terms as $term ) {
			if ( ! get_term_by( 'name', $term, $taxonomy ) ) { // @codingStandardsIgnoreLine.
				wp_insert_term( $term, $taxonomy );
			}
		}
		
	}

	/**
	 * Save meta box content.
     *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 * @param object $post
     *
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		// If not the flex post.
		if ( self::POST_TYPE != $post->post_type ) {
			return;
		}

		// Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
		}

		// Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
		}

		if ( ! empty( $_POST['_product tabs'] ) ) {
			update_post_meta( $post_id, 'product tabs', $_POST['_product tabs'] );
		}
	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function wc_screen_ids($screen_ids) {
		$screen_ids[] = 'razzi_product_tab';

		return $screen_ids;
	}

}