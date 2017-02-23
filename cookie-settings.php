<?php
/**
 * Plugin Name:       Groupe 361 Cookie message settings
 * Plugin URI:        www.groupe361.com
 * Description:       Customize cookie notification message and styles
 * Version:           1.0.0
 * Author:            German Pichardo
 * Author URI:        www.german-pichardo.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cookie-textdomain
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if( !class_exists( 'CookieSettings' )){
    class CookieSettings {
        private $cookie_settings_options;
        /**
         * Plugin initialization
         */
        public function __construct() {
            // Add the page to the admin menu
            add_action( 'admin_menu', array( $this, 'cookie_settings_add_plugin_page' ) );
            // Register page options
            add_action( 'admin_init', array( $this, 'cookie_settings_page_init' ) );
            // Enqueue the needed Javascript and CSS in admin panel (Color picker)
            add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            // Add plugin settings link
            add_filter( 'plugin_action_links', array( $this, 'cookie_add_settings_link' ), 10, 2 );
        }

        /**
         * Function that will enqueue the needed Javascript and CSS in admin panel
         */
        public function enqueue_admin_scripts() {

            $screen = get_current_screen();

            // We load the JS only in our settings page
            if ( $screen -> id == 'settings_page_cookie-settings' ) {
                // Css color-picker
                wp_enqueue_style( 'wp-color-picker' );
                // Load external js file with color-picker dependency
                wp_enqueue_script( 'color_picker_custom_js', plugins_url( 'js/color-picker-custom.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
            }
        }

        /**
         * Function that will add the options page under Setting Menu.
         */
        public function cookie_settings_add_plugin_page() {
            // $page_title, $menu_title, $capability, $menu_slug, $callback_function
            add_options_page(
                __('Cookie message', 'cookie-textdomain'), // page_title
                __('Cookie message', 'cookie-textdomain'), // menu_title
                'manage_options', // capability
                'cookie-settings', // menu_slug
                array( $this, 'cookie_settings_create_admin_page' ) // function
            );
        }

        /**
         * Function that will display the options page.
         */
        public function cookie_settings_create_admin_page() {
            $this->cookie_settings_options = get_option( 'cookie_settings_option_name' ); ?>

            <div class="wrap">
                <h2><?php _e( 'Cookie message settings', 'cookie-textdomain' ); ?></h2>
                <p><?php _e( 'Use the fields to overwrite the default banner styles and settings.', 'cookie-textdomain' ); ?></p>
                <?php settings_errors(); ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'cookie_settings_option_group' );
                    do_settings_sections( 'cookie-settings-admin' );
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        public function cookie_settings_page_init() {

            // Register Settings
            register_setting(
                'cookie_settings_option_group', // option_group
                'cookie_settings_option_name', // option_name
                array( $this, 'cookie_settings_sanitize' ) // sanitize_callback
            );

            // Add Section for option fields
            add_settings_section(
                'cookie_settings_setting_section', // id
                '', // title
                array( $this, 'cookie_settings_section_info' ), // callback
                'cookie-settings-admin' // page
            );

            // Add Message Field
            add_settings_field(
                'activate_cookie_message_0', // id
                __('General activation', 'cookie-textdomain'), // title
                array( $this, 'activate_cookie_message_0_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );

            // Add Background Color Field
            add_settings_field(
                'background_color_1', // id
                __('Background color', 'cookie-textdomain'), // title
                array( $this, 'background_color_1_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );

            // Add Text Color Field
            add_settings_field(
                'text_color_2', // id
                __('Text color', 'cookie-textdomain'), // title
                array( $this, 'text_color_2_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );

            // Add Button text Field
            add_settings_field(
                'button_text_3', // id
                __('Accept button text', 'cookie-textdomain'), // title
                array( $this, 'button_text_3_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );

            // Add banner position Field
            add_settings_field(
                'banner_position_4', // id
                __('Position', 'cookie-textdomain'), // title
                array( $this, 'banner_position_4_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );

            // Add banner message Field
            add_settings_field(
                'banner_message_5', // id
                __('Notification message', 'cookie-textdomain'), // title
                array( $this, 'banner_message_5_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );

            // Add banner message font size
            add_settings_field(
                'banner_font_size_6', // id
                __('Font size', 'cookie-textdomain'), // title
                array( $this, 'banner_font_size_6_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );
            // Add banner opacity
            add_settings_field(
                'banner_opacity_7', // id
                __('Opacity', 'cookie-textdomain'), // title
                array( $this, 'banner_opacity_7_callback' ), // callback
                'cookie-settings-admin', // page
                'cookie_settings_setting_section' // section
            );
        }
        /**
         * Functions that registers settings link on plugin description.
         */
        public function cookie_add_settings_link( $links , $file){
            $this_plugin = plugin_basename(__FILE__);

            if ( is_plugin_active($this_plugin) && $file == $this_plugin ) {
                $links[] = '<a href="' . admin_url( 'options-general.php?page=cookie-settings' ) . '">' . __( 'Settings', 'cookie-textdomain' ) . '</a>';
            }

            return $links;

        } // end cookie_add_settings_link
        /**
         * Functions that display the fields.
         */
        public function cookie_settings_sanitize($input) {
            $sanitary_values = array();
            if ( isset( $input['activate_cookie_message_0'] ) ) {
                $sanitary_values['activate_cookie_message_0'] = $input['activate_cookie_message_0'];
            }

            if ( isset( $input['background_color_1'] ) ) {
                $sanitary_values['background_color_1'] = sanitize_text_field( $input['background_color_1'] );
            }

            if ( isset( $input['text_color_2'] ) ) {
                $sanitary_values['text_color_2'] = sanitize_text_field( $input['text_color_2'] );
            }

            if ( isset( $input['button_text_3'] ) ) {
                $sanitary_values['button_text_3'] = sanitize_text_field( $input['button_text_3'] );
            }

            if ( isset( $input['banner_position_4'] ) ) {
                $sanitary_values['banner_position_4'] = $input['banner_position_4'];
            }

            if ( isset( $input['banner_message_5'] ) ) {
                $sanitary_values['banner_message_5'] = esc_textarea( $input['banner_message_5'] );
            }

            if ( isset( $input['banner_font_size_6'] ) ) {
                $sanitary_values['banner_font_size_6'] = sanitize_text_field($input['banner_font_size_6']);
            }

            if ( isset( $input['banner_opacity_7'] ) ) {
                $sanitary_values['banner_opacity_7'] = sanitize_text_field($input['banner_opacity_7']);
            }

            return $sanitary_values;
        }

        public function cookie_settings_section_info() {

        }

        public function activate_cookie_message_0_callback() {
            printf(
                '<input type="checkbox" name="cookie_settings_option_name[activate_cookie_message_0]" id="activate_cookie_message_0" value="activate_cookie_message_0" %s> <label for="activate_cookie_message_0">' .__( 'Unable notification temporary', 'cookie-textdomain' ) . '</label>',
                ( isset( $this->cookie_settings_options['activate_cookie_message_0'] ) && $this->cookie_settings_options['activate_cookie_message_0'] === 'activate_cookie_message_0' ) ? 'checked' : ''
            );
        }

        public function background_color_1_callback() {
            printf(
                '<input class="cookie-color-picker regular-text" type="text" name="cookie_settings_option_name[background_color_1]" id="background_color_1" value="%s"><p class="description"><small>' .__( 'Default background color : <code>#808080</code>', 'cookie-textdomain' ) . '</small></p>',
                isset( $this->cookie_settings_options['background_color_1'] ) ? esc_attr( $this->cookie_settings_options['background_color_1']) : ''
            );
        }

        public function text_color_2_callback() {
            printf(
                '<input class="cookie-color-picker regular-text" type="text" name="cookie_settings_option_name[text_color_2]" id="text_color_2" value="%s"><p class="description"><small>' .__( 'Default text color : <code>#ffffff</code>', 'cookie-textdomain' ) . '</small></p>',
                isset( $this->cookie_settings_options['text_color_2'] ) ? esc_attr( $this->cookie_settings_options['text_color_2']) : ''
            );
        }

        public function button_text_3_callback() {
            printf(
                '<input class="regular-text" type="text" name="cookie_settings_option_name[button_text_3]" id="button_text_3" value="%s" placeholder="ok">',
                isset( $this->cookie_settings_options['button_text_3'] ) ? esc_attr( $this->cookie_settings_options['button_text_3']) : ''
            );
        }

        public function banner_position_4_callback() {
            ?> <fieldset><?php $checked = ( isset( $this->cookie_settings_options['banner_position_4'] ) && $this->cookie_settings_options['banner_position_4'] === 'top' ) ? 'checked' : '' ; ?>
                <label for="banner_position_4-0"><input type="radio" name="cookie_settings_option_name[banner_position_4]" id="banner_position_4-0" value="top" <?php echo $checked; ?>>  <?php _e( 'Top', 'cookie-textdomain' ); ?></label><br>
                <?php $checked = ( isset( $this->cookie_settings_options['banner_position_4'] ) && $this->cookie_settings_options['banner_position_4'] === 'bottom' ) ? 'checked' : '' ; ?>
                <label for="banner_position_4-1"><input type="radio" name="cookie_settings_option_name[banner_position_4]" id="banner_position_4-1" value="bottom" <?php echo $checked; ?>> <?php _e( 'Bottom', 'cookie-textdomain' ); ?></label></fieldset> <p class="description"><small><?php _e( 'Default position : <code>bottom</code>', 'cookie-textdomain' ); ?></small></p><?php
        }

        public function banner_message_5_callback() {
            printf(
                '<textarea placeholder="' .__( 'Les cookies assurent le bon fonctionnement de nos services. En utilisant ces derniers, vous acceptez l&apos;utilisation des cookies.', 'cookie-textdomain' ) . '" class="large-text" rows="5" name="cookie_settings_option_name[banner_message_5]" id="banner_message_5">%s</textarea>',
                isset( $this->cookie_settings_options['banner_message_5'] ) ? esc_attr( $this->cookie_settings_options['banner_message_5']) : ''
            );
        }

        public function banner_font_size_6_callback() {
            printf(
                '<input class="small-text" type="text" name="cookie_settings_option_name[banner_font_size_6]" id="banner_font_size_6" value="%s" placeholder="11"> px',
                isset( $this->cookie_settings_options['banner_font_size_6'] ) ? esc_attr( $this->cookie_settings_options['banner_font_size_6']) : ''
            );
        }

        public function banner_opacity_7_callback() {
            printf(
                '<input class="small-text" type="text" name="cookie_settings_option_name[banner_opacity_7]" id="banner_opacity_7" value="%s" placeholder="80">  <p class="description"><small>1-100</small></p>',
                isset( $this->cookie_settings_options['banner_opacity_7'] ) ? esc_attr( $this->cookie_settings_options['banner_opacity_7']) : ''
            );
        }

    }
} // !class_exists

if ( is_admin() )
    $cookie_settings = new CookieSettings();
//
//   $cookie_settings_options = get_option( 'cookie_settings_option_name' ); // Array of All Options
//   $activate_cookie_message_0 = $cookie_settings_options['activate_cookie_message_0']; // Activate cookie message
//   $background_color_1 = $cookie_settings_options['background_color_1']; // Background color
//   $text_color_2 = $cookie_settings_options['text_color_2']; // Text color
//   $button_text_3 = $cookie_settings_options['button_text_3']; // Button text
//   $banner_position_4 = $cookie_settings_options['banner_position_4']; // Position
//   $banner_message_5 = $cookie_settings_options['banner_message_5']; // Banner message
//   $banner_font_size_6 = $cookie_settings_options['banner_font_size_6']; // Banner font size
//   $banner_opacity_7 = $cookie_settings_options['banner_opacity_7']; // Banner font size

/******************************************************************************
 * FRONT-END
 * If disable notification option is checked
 * && "cookie-enabled" is already injected
 * && we are not in admin area
 *************************************************************************/

if ( !get_option( 'cookie_settings_option_name' )['activate_cookie_message_0'] && !isset( $_COOKIE[ 'cookie-enabled']) && !is_admin() ) {
    // Inline CSS in head
    add_action( 'wp_print_styles', 'cookie_inline_css' );
    // Inline JS in footer
    add_action( 'wp_footer', 'cookie_inline_scripts' );
}

/**
 * Inline CSS to build banner general style
 */
function cookie_inline_css () {
    // Position conditional style
    $bannerPositionStyle ='';
    if (get_option( 'cookie_settings_option_name' )['banner_position_4'] == 'top'  ) {
        $bannerPositionStyle .= 'bottom:auto !important ;top:0;';
    } else { // Default position is bottom
        $bannerPositionStyle .= 'bottom:0;top:auto;';
    }
    echo '<style id="cookie_inline_css" type="text/css">
          #cookieBannerContainer {
              width:100%;
              min-height:36px; 
              font-family:Arial,Helvetica,sans serif;
              text-align: center;
              padding: 5px;z-index: 100000;
              position:fixed;  
              left: 0;
              '.$bannerPositionStyle.'
              
          }
          #cookieBannerContainer .cookie-content {
            display:inline-block;
          }
          #cookieBannerContainer .cookie-text {
            padding: 5px 0;
            text-align: center;
            display: inline-block;
            margin:0;
          }
          #cookieBannerContainer button {
              background:none; 
              height: 26px;
              line-height:25px; 
              outline: 0; 
              font-size:10px; 
              border-radius: 8px;
              padding: 0 6px;
              cursor: pointer;
              position: relative;
              vertical-align: middle;
          }
      </style>
    ';
}

/**
 * Inline JavaScript to build cookie banner
 */
function cookie_inline_scripts() {
    // Variables with PHP fallback
    $cookie_settings_options = get_option( 'cookie_settings_option_name' ); // Array of All Options

    $banner = array (
        'background'    =>	$cookie_settings_options['background_color_1'] ? $cookie_settings_options['background_color_1'] : '#808080',
        'text_color'    =>	$cookie_settings_options['text_color_2'] ? $cookie_settings_options['text_color_2'] : '#ffffff',
        'button_text'   =>	$cookie_settings_options['button_text_3'] ? $cookie_settings_options['button_text_3'] : '' .__( 'ok', 'cookie-textdomain' ) . '',
        'message'       =>	$cookie_settings_options['banner_message_5'] ? $cookie_settings_options['banner_message_5'] : '' .__( 'Les cookies assurent le bon fonctionnement de nos services. En utilisant ces derniers, vous acceptez l&apos;utilisation des cookies.', 'cookie-textdomain' ) . '',
        'font_size'     =>	$cookie_settings_options['banner_font_size_6'] ? $cookie_settings_options['banner_font_size_6'] : '11',
        'opacity'       =>	$cookie_settings_options['banner_opacity_7'] ? $cookie_settings_options['banner_opacity_7'] : '80',
    );
    // Convert to Json array
    $banner_json = json_encode($banner);

    if( wp_script_is( 'jquery', 'done' ) ) { ?>
        <!--START cookie_inline_scripts-->
        <script type="text/javascript" defer >

            /**************************************************
             * Start - Cookie Message
             ***************************************************/

            // We execute and pass the banner settings : backgroundColor,textColor,bannerMessage,buttonText,fontSize,bannerOpacity
            if (window.attachEvent)
                window.attachEvent('onload', createCookieBanner( <?php print $banner_json;?>));
            else
            if (window.addEventListener)
                window.addEventListener('load', createCookieBanner( <?php print $banner_json;?>),false);

            function isIPaddress(ip){
                if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip)) return true;
                return false;
            }

            function getDomain(){
                var domain;

                if(isIPaddress(window.location.hostname))
                    domain = "";
                else{
                    domain = window.location.hostname.split(".");
                    domain = "." + domain[domain.length-2] + "." + domain[domain.length-1];
                }
                return domain;
            }

            function writeCookie(key, value, domain, path){
                var dateExpire = new Date();
                dateExpire.setMonth(dateExpire.getMonth() + 13);
                document.cookie= key + "=" + value + "; expires=" + dateExpire.toUTCString() + "; domain=" + domain + ";" + "path=" + path + ";";
            }

            function readCookie(key){
                var value = new Array();

                var allcookies = document.cookie;
                // Get all the cookies pairs in an array
                cookiearray  = allcookies.split(';');

                // Now take key value pair out of this array
                for(var i=0; i<cookiearray.length; i++){
                    if (i > 0)
                        cookiearray[i] = cookiearray[i].substring(1);

                    value[cookiearray[i].split('=')[0]] = cookiearray[i].split('=')[1];
                }
                return value[key];
            }

            function createDom(htmlStr) {
                var fragment = document.createDocumentFragment(),
                    temp = document.createElement('div');
                temp.innerHTML = htmlStr;
                while (temp.firstChild) {
                    fragment.appendChild(temp.firstChild);
                }
                return fragment;
            }

            // Convert Hex to rgba with opacity capability
            function convertHex(hex,opacity){
                hex = hex.replace('#','');
                r = parseInt(hex.substring(0,2), 16);
                g = parseInt(hex.substring(2,4), 16);
                b = parseInt(hex.substring(4,6), 16);

                resultRgba = 'rgba('+r+','+g+','+b+','+opacity/100+')';
                return resultRgba;
            }

            function createCookieBanner(banner_json) {
                if(!readCookie('cookie-enabled')){
                    var banner_options = banner_json;
                    // Banner settings : background, text_color, button_text, message, font_size, opacity
                    var banner = {
                        "background": banner_options.background || "#808080",
                        "text_color": banner_options.text_color || "#ffffff",
                        "button_text": banner_options.button_text || "ok",
                        "message": banner_options.message || "<?php _e( 'Lorem ipsum', 'cookie-textdomain' ); ?>",
                        "font_size": banner_options.font_size || "11",
                        "opacity": banner_options.opacity || "80"
                    };

                    var bannerWrapper = createDom("<div id='cookieBannerContainer' style='background: "+banner.background+"; background-color: "+convertHex(banner.background,banner.opacity)+"; color: "+banner.text_color+"; font-size: "+banner.font_size+"px;'><span class='cookie-content'><span class='cookie-text'>"+banner.message+"</span> <button type='button' style='color:"+banner.text_color+";border:1px solid "+banner.text_color+";font-size: "+banner.font_size+"px;' id='cookieBannerButton' title='Fermer'>"+banner.button_text+"</button></span></div>");

                    body=document.body;
                    body.insertBefore(bannerWrapper,body.childNodes[0]);

                    setTimeout(function () {document.getElementById("cookieBannerContainer");}, 300);
                    document.getElementById('cookieBannerButton').onclick = function(){
                        var p = document.getElementById("cookieBannerContainer");
                        body.removeChild(p);
                        writeCookie("cookie-enabled", "1", getDomain(), "/");
                    }
                }
            }
            /**************************************************
             * End - Cookie Message
             ***************************************************/
        </script>
        <!--END cookie_inline_scripts-->
        <?php
    }

}
