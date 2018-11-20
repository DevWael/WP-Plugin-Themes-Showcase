<?php
/* Demos Showcase Settings Page */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class bb_demosshowcase_Settings_Page {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wph_create_settings' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_fields' ) );
	}

	public function wph_create_settings() {
		$parent_slug = 'edit.php?post_type=theme_demos';
		$page_title  = 'Demos Showcase Settings';
		$menu_title  = 'Demos Showcase';
		$capability  = 'manage_options';
		$slug        = 'demosshowcase';
		$callback    = array( $this, 'wph_settings_content' );
		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $slug, $callback );
	}

	public function wph_settings_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		?>
        <div class="wrap">
            <h1>Demos Showcase Settings</h1>
			<?php settings_errors(); ?>
            <form method="POST" action="options.php">
				<?php
				settings_fields( 'demosshowcase' );
				do_settings_sections( 'demosshowcase' );
				submit_button();
				?>
            </form>
        </div> <?php
	}

	public function wph_setup_sections() {
		add_settings_section( 'demosshowcase_section', '', array(), 'demosshowcase' );
	}

	public function wph_setup_fields() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$fields = array(
			array(
				'label'   => 'Activate Demo Box',
				'id'      => 'bb_activate_demo',
				'type'    => 'radio',
				'desc'    => '',
				'default' => 'off',
				'options' => array(
					'on'  => 'On',
					'off' => 'Off'
				),
				'section' => 'demosshowcase_section',
			),
			array(
				'label'       => 'Demos Box Title',
				'id'          => 'bb_box_title',
				'type'        => 'text',
				'desc'        => '',
				'placeholder' => '',
				'section'     => 'demosshowcase_section',
			),
			array(
				'label'       => 'Buy Now URL',
				'id'          => 'bb_buy_url',
				'type'        => 'text',
				'desc'        => '',
				'placeholder' => '',
				'section'     => 'demosshowcase_section',
			),
			array(
				'label'   => 'Demos Box Description',
				'id'      => 'bb_box_desc',
				'desc'    => '',
				'type'    => 'wysiwyg',
				'section' => 'demosshowcase_section',
			),
		);
		foreach ( $fields as $field ) {
			add_settings_field( $field['id'], $field['label'], array(
				$this,
				'wph_field_callback'
			), 'demosshowcase', $field['section'], $field );
			register_setting( 'demosshowcase', $field['id'] );
		}
	}

	public function wph_field_callback( $field ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
			case 'wysiwyg':
				wp_editor( $value, $field['id'] );
				break;
			case 'radio':
			case 'checkbox':
				if ( ! $value ) {
					$value = array( $field['default'] );
				}
				if ( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
					$options_markup = '';
					$iterator       = 0;
					foreach ( $field['options'] as $key => $label ) {
						$iterator ++;
						$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
							$field['id'],
							$field['type'],
							$key,
							checked( $value[ array_search( $key, $value, true ) ], $key, false ),
							$label,
							$iterator
						);
					}
					printf( '<fieldset>%s</fieldset>',
						$options_markup
					);
				}
				break;
			default:
				printf( '<input name="%1$s" style="width: 300px;" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if ( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}
}

new bb_demosshowcase_Settings_Page();