<?php
/*
Plugin Name: Bbioon Theme Showcase
Plugin URI: https://bbioon.com
Description: Use this plugin to showcase your theme demos.
Version: 0.5
Author: BbioonThemes
Author URI: https://themeforest.net/user/bbioonthemes/portfolio
License: GPLv2 or later
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//plugin activation indicator
if ( ! defined( 'BBIOON_THEME_SHOWCASE' ) ) {
	define( 'BBIOON_THEME_SHOWCASE', true );
}

//plugin version
if ( ! defined( 'BBIOON_THEME_SHOWCASE_VERSION' ) ) {
	define( 'BBIOON_THEME_SHOWCASE_VERSION', 0.5 );
}

//plugin admin settings page
include( plugin_dir_path( __FILE__ ) . 'admin.php' );

//load plugin assets
add_action( 'wp_enqueue_scripts', 'bb_showcase_scripts' );
function bb_showcase_scripts() {
	wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css', false, BBIOON_THEME_SHOWCASE_VERSION );
	wp_enqueue_style( 'bbioon-showcase-style', plugin_dir_url( __FILE__ ) . 'assets/style.css', false, BBIOON_THEME_SHOWCASE_VERSION );
	wp_enqueue_script( 'bbioon-showcase-requests', plugin_dir_url( __FILE__ ) . 'assets/init.js', array( 'jquery' ), BBIOON_THEME_SHOWCASE_VERSION, true );
}

//set demos best size
add_action( 'after_setup_theme', 'bb_demo_showcase_image_sizes' );
if ( ! function_exists( 'bb_demo_showcase_image_sizes' ) ) {
	function bb_demo_showcase_image_sizes() {
		add_image_size( 'bb_demo_showcase_image_size', 550, 550, true );
	}
}

//refresh urls
add_action( 'init', 'bb_renew_urls' );
function bb_renew_urls() {
	if ( 'done' !== get_option( 'bbioon_showcase_themes_urls' ) ) {
		flush_rewrite_rules();
		update_option( 'bbioon_showcase_themes_urls', 'done' );
	}
}

//load plugin translation
add_action( 'init', 'bb_load_text_domain' );
function bb_load_text_domain() {
	load_plugin_textdomain( 'bbioon', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

//set post type to add demos
add_action( 'init', 'bb_themedemo_cpt', 0 );
function bb_themedemo_cpt() {
	$labels = array(
		'name'                  => __( 'Theme Demos', 'Post Type General Name', 'bbioon' ),
		'singular_name'         => __( 'Theme Demo', 'Post Type Singular Name', 'bbioon' ),
		'menu_name'             => __( 'Theme Demos', 'bbioon' ),
		'name_admin_bar'        => __( 'Theme Demo', 'bbioon' ),
		'archives'              => __( 'Theme Demo Archives', 'bbioon' ),
		'attributes'            => __( 'Theme Demo Attributes', 'bbioon' ),
		'parent_item_colon'     => __( 'Parent Theme Demo:', 'bbioon' ),
		'all_items'             => __( 'All Theme Demos', 'bbioon' ),
		'add_new_item'          => __( 'Add New Theme Demo', 'bbioon' ),
		'add_new'               => __( 'Add New', 'bbioon' ),
		'new_item'              => __( 'New Theme Demo', 'bbioon' ),
		'edit_item'             => __( 'Edit Theme Demo', 'bbioon' ),
		'update_item'           => __( 'Update Theme Demo', 'bbioon' ),
		'view_item'             => __( 'View Theme Demo', 'bbioon' ),
		'view_items'            => __( 'View Theme Demos', 'bbioon' ),
		'search_items'          => __( 'Search Theme Demo', 'bbioon' ),
		'not_found'             => __( 'Not found', 'bbioon' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'bbioon' ),
		'featured_image'        => __( 'Featured Image', 'bbioon' ),
		'set_featured_image'    => __( 'Set featured image', 'bbioon' ),
		'remove_featured_image' => __( 'Remove featured image', 'bbioon' ),
		'use_featured_image'    => __( 'Use as featured image', 'bbioon' ),
		'insert_into_item'      => __( 'Add to Theme Demo', 'bbioon' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Theme Demo', 'bbioon' ),
		'items_list'            => __( 'Theme Demos list', 'bbioon' ),
		'items_list_navigation' => __( 'Theme Demos list navigation', 'bbioon' ),
		'filter_items_list'     => __( 'Filter Theme Demos list', 'bbioon' ),
	);
	$args   = array(
		'label'               => __( 'Theme Demo', 'bbioon' ),
		'description'         => __( 'Showcase you theme demos', 'bbioon' ),
		'labels'              => $labels,
		'menu_icon'           => 'dashicons-format-gallery',
		'supports'            => array( 'title', 'thumbnail', ),
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'show_in_rest'        => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'theme_demos', $args );
}

//set metaboxes to add demo details
class bb_themelinkMetabox {
	private $screen = array(
		'theme_demos',
	);
	private $meta_fields = array(
		array(
			'label'   => 'Theme Demo Url',
			'id'      => 'showcase_theme_url',
			'default' => '',
			'type'    => 'text',
		),
	);

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );
	}

	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'themelink',
				__( 'Theme Link', 'bbioon' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'advanced',
				'default'
			);
		}
	}

	public function meta_box_callback( $post ) {
		wp_nonce_field( 'themelink_data', 'themelink_nonce' );
		echo 'Add theme demo url';
		$this->field_generator( $post );
	}

	public function field_generator( $post ) {
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
			$label      = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = $meta_field['default'];
			}
			switch ( $meta_field['type'] ) {
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value
					);
			}
			$output .= $this->format_rows( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}

	public function format_rows( $label, $input ) {
		return '<tr><th>' . $label . '</th><td>' . $input . '</td></tr>';
	}

	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['themelink_nonce'] ) ) {
			return $post_id;
		}
		$nonce = $_POST['themelink_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'themelink_data' ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		foreach ( $this->meta_fields as $meta_field ) {
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'email':
						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
						break;
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
				}
				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, $meta_field['id'], '0' );
			}
		}
	}
}

if ( class_exists( 'bb_themelinkMetabox' ) ) {
	new bb_themelinkMetabox;
};

//display demos box in the front-end
add_action( 'wp_footer', 'bb_front_end_demos' );
function bb_front_end_demos() {
	if ( is_array( get_option( 'bb_activate_demo' ) ) && 'on' != get_option( 'bb_activate_demo' )[0] ) {
		return;
	}
	$buy_url = get_option( 'bb_buy_url' );
	$args    = array(
		'post_type'              => 'theme_demos',
		'posts_per_page'         => - 1,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'                 => 'ids'
	);
	$query   = new WP_Query( $args );
	if ( $query->have_posts() ) {
		?>
        <div class="bb_theme_demos_floating_buttons">
            <a href="#" class="bb_open_showcase">
                <i class="fa fa-desktop" aria-hidden="true"></i>
                <div class="bb_title">
					<?php esc_html_e( 'Demos', 'bbioon' ); ?>
                </div>
            </a>
            <a href="<?php echo esc_url( $buy_url ); ?>" target="_blank" class="bb_buy_now">
                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                <div class="bb_title">
					<?php esc_html_e( 'Buy Now', 'bbioon' ); ?>
                </div>
            </a>
        </div>
        <div class="bb_theme_demos_container">
            <div class="bb_theme_demos_close">
                <a href="#">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <span class="bb_title">
                        <?php esc_html_e( 'Close', 'bbioon' ); ?>
                    </span>
                </a>
            </div>

			<?php if ( $title = get_option( 'bb_box_title' ) ) {
				$description = get_option( 'bb_box_desc' );
				?>
                <div class="bb_theme_demos_header">
                    <h3>
						<?php echo wp_specialchars_decode( $title ); ?>
                    </h3>
                    <div><?php echo wp_specialchars_decode( $description ); ?></div>
                </div>
			<?php } ?>

            <div class="bb_theme_demos_block">
                <div class="bb_theme_demos_content">
					<?php while ( $query->have_posts() ): $query->the_post();
						$demo_url = get_post_meta( get_the_ID(), 'showcase_theme_url', true );
						?>
                        <div class="bb_theme_demo_column">
                            <div class="bb_theme_demo">
                                <div class="bb_theme_demo_img bb-ovh">
                                    <a href="<?php echo esc_url( $demo_url ) ?>" target="_blank">
										<?php the_post_thumbnail( 'bb_demo_showcase_image_size', array( 'class' => 'bb-img-responsive' ) ) ?>
                                        <div class="bb_overlay"></div>
                                        <div class="bb_icon">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="bb_theme_demo_title">
                                    <a href="<?php echo esc_url( $demo_url ) ?>" target="_blank">
										<?php the_title(); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
					<?php endwhile; ?>
                </div>
            </div>
        </div>
		<?php
	}
	wp_reset_postdata();
	//Kindly Don't remove this line
	?>
    <!-- Created by BbioonThemes: https://themeforest.net/user/bbioonthemes/portfolio -->
    <!-- Plugin URL: https://github.com/DevWael/WP-Plugin-Themes-Showcase -->
	<?php
}
