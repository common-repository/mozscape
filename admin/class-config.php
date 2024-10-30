<?php 

class Mozscape_Admin_Pages {
	
	var $currentoption = 'moz';
	var $adminpages = array( 'mozscape_dashboard' );

	/**
	 * Class constructor, which basically only hooks the init function on the init hook
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ), 20 );
	}

	function init() {
		$this->adminpages = apply_filters( 'mozscape_admin_pages', $this->adminpages );
	}
	
	/**
	 * Generates the header for admin pages
	 *
	 * @param string $title          The title to show in the main heading.
	 * @param bool   $form           Whether or not the form should be included.
	 * @param string $option         The long name of the option to use for the current page.
	 * @param string $optionshort    The short name of the option to use for the current page.
	 */
	function mozscape_header( $title, $form = true, $option = 'mozscape_options', $optionshort = 'moz') {
		?>
		<div class="wrap">
			<?php
		if ( ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' ) || ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) ) {
			$msg = __( 'Settings updated', 'wp-mozscape' );

			echo '<div id="message" style="width:94%;" class="message updated"><p><strong>' . esc_html( $msg ) . '.</strong></p></div>';
		}
		?>
		<h2><?php _e( "Mozscape: ", 'wp-mozscape' ); echo $title; ?></h2>
            <div id="wpseo_content_top" class="postbox-container" style="min-width:400px; max-width:600px; padding: 0 20px 0 0;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">
		<?php
		if ( $form ) {
			echo '<form action="' . admin_url( 'options.php' ) . '" method="post" id="mozscape-conf">';
			settings_fields( $option );
			$this->currentoption = $optionshort;
		}

	}
	
	function mozscape_footer($submit = false) {
		if ( $submit ) {
			?>
			<div class="submit"><input type="submit" class="button-primary" name="submit"
									   value="<?php _e( "Save Settings", 'wp-mozscape' ); ?>"/></div>
			<?php } 
			echo '</form>';
			?>
					</div>
				</div>
			</div>
		</div>				
		<?php
	}

	function mozscape_dashboard() {
		echo '<img src="'.MOZ_URL.'images/mozscape-powered.gif" alt="Powered by Mozscape" /><h2>' . __( 'Moz Tools', 'wp-mozscape') . '</h2>';
		echo '<p>' . __('Provide your Mozscape API key to reveal SEO related information as you edit posts and pages.', 'wp-mozscape') . '</p>';
		echo $this->textinput('moz_accessid', 'Enter your Access ID');
		echo $this->textinput('moz_secretkey', 'Enter your Secret Key');
		echo '<br />';
		echo $this->checkbox('moz_url_metrics', __('Enable URL Metrics', 'wp-mozscape'));
		echo $this->checkbox('moz_link_metrics', __('Enable Link Metrics', 'wp-mozscape'));
		echo $this->checkbox('moz_anchor_text_metrics', __('Enable Anchor Text Metrics', 'wp-mozscape'));
		echo '<p><a target="_blank" href="http://www.moz.com/api">' . __('What is the Moz API? ', 'wp-mozscape') . '</a></p>';
		echo '<p><a target="_blank" href="http://www.moz.com/api/keys">' . __('Where can I find my API Credentials? ', 'wp-mozscape') . '</a></p>';
		echo '<p class="desc">&nbsp;</p>';
	}
	
	function checkbox( $var, $label, $label_left = false, $option = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;
	
		$options = get_option( $option );
	
		if ( !isset( $options[$var] ) )
			$options[$var] = false;
	
		if ( $options[$var] === true )
			$options[$var] = 'on';
	
		if ( $label_left !== false ) {
			if ( !empty( $label_left ) )
				$label_left .= ':';
			$output_label = '<label class="checkbox" for="' . esc_attr( $var ) . '">' . $label_left . '</label>';
			$class        = 'checkbox';
		} else {
			$output_label = '<label for="' . esc_attr( $var ) . '">' . $label . '</label>';
			$class        = 'checkbox double';
		}
	
		$output_input = "<input class='$class' type='checkbox' id='".esc_attr( $var )."' name='" . esc_attr( $option ) . "[" . esc_attr( $var ) ."]' " . checked( $options[$var], 'on', false ) . '/>';
	
		if ( $label_left !== false ) {
			$output = $output_label . $output_input . '<label class="checkbox" for="' . esc_attr( $var ) . '">' . $label . '</label>';
		} else {
			$output = $output_input . $output_label;
		}
		return $output . '<br class="clear" />';
	}
	
	function textinput( $var, $label, $option = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;
	
		$options = get_option( $option );
	
		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );
	
		return '<label class="textinput" for="' . esc_attr( $var ) . '">' . $label . ':</label><input class="textinput" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>' . '<br class="clear" />';
	}
	
}

global $mozscape_admin_pages;
$mozscape_admin_pages = new Mozscape_Admin_Pages();
?>