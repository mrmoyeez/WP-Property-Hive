<?php
/**
 * Load assets.
 *
 * @author      PropertyHive
 * @category    Admin
 * @package     PropertyHive/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PH_Admin_Assets' ) ) :

/**
 * PH_Admin_Assets Class
 */
class PH_Admin_Assets {

    /**
     * Hook in tabs.
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    /**
     * Enqueue styles
     */
    public function admin_styles() {
        global $wp_scripts;

        // Sitewide menu CSS
        //wp_enqueue_style( 'propertyhive_admin_menu_styles', PH()->plugin_url() . '/assets/css/menu.css', array(), PH_VERSION );

        $screen = get_current_screen();
        
        if ( in_array( $screen->id, ph_get_screen_ids() ) ) {

            $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

            // Admin styles for PH pages only
            wp_enqueue_style( 'propertyhive_admin_styles', PH()->plugin_url() . '/assets/css/admin.css', array(), PH_VERSION );
            
            wp_enqueue_style( 'font_awesome', PH()->plugin_url() . '/assets/css/font-awesome.min.css', array(), PH_VERSION );
            
            wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), PH_VERSION );
            wp_enqueue_style( 'wp-color-picker' );
        }
        
        if ( in_array( $screen->id, array( 'property', 'contact', 'viewing' ) ) )
        {
            wp_enqueue_style( 'chosen', PH()->plugin_url() . '/assets/css/chosen.css', array(), PH_VERSION );
        }

        /*if ( in_array( $screen->id, array( 'dashboard' ) ) ) {
            wp_enqueue_style( 'propertyhive_admin_dashboard_styles', PH()->plugin_url() . '/assets/css/dashboard.css', array(), PH_VERSION );
        }*/

        do_action( 'propertyhive_admin_css' );
    }


    /**
     * Enqueue scripts
     */
    public function admin_scripts() {
        global $wp_query, $post;

        $screen       = get_current_screen();
        $ph_screen_id = sanitize_title( __( 'PropertyHive', 'propertyhive' ) );
        $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Register scripts
        wp_register_script( 'propertyhive_dashboard', PH()->plugin_url() . '/assets/js/admin/dashboard' . /*$suffix .*/ '.js', array( 'jquery' ), PH_VERSION );

        wp_register_script( 'propertyhive_admin', PH()->plugin_url() . '/assets/js/admin/admin' . /*$suffix .*/ '.js', array( 'jquery', 'jquery-tiptip' ), PH_VERSION );

        wp_register_script( 'jquery-tiptip', PH()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . /*$suffix .*/ '.js', array( 'jquery' ), PH_VERSION, true );

        wp_register_script( 'propertyhive_admin_meta_boxes', PH()->plugin_url() . '/assets/js/admin/meta-boxes' . /*$suffix .*/ '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ), PH_VERSION );

        wp_register_script( 'propertyhive_admin_settings', PH()->plugin_url() . '/assets/js/admin/settings' . /*$suffix .*/ '.js', array( 'jquery', 'wp-color-picker' ), PH_VERSION );

        wp_register_script( 'ajax-chosen', PH()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . /*$suffix .*/ '.js', array('jquery', 'chosen'), PH_VERSION );

        wp_register_script( 'chosen', PH()->plugin_url() . '/assets/js/chosen/chosen.jquery' . /*$suffix .*/ '.js', array('jquery'), PH_VERSION );

        wp_register_script( 'flot', PH()->plugin_url() . '/assets/js/jquery-flot/jquery.flot' . $suffix . '.js', array( 'jquery' ), PH_VERSION );
        wp_register_script( 'flot-resize', PH()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.resize' . $suffix . '.js', array( 'jquery', 'flot' ), PH_VERSION );
        //wp_register_script( 'flot-time', PH()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), PH_VERSION );
        //wp_register_script( 'flot-pie', PH()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), PH_VERSION );
        //wp_register_script( 'flot-stack', PH()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), PH_VERSION );

        wp_enqueue_script( 'propertyhive_admin' );

        // PropertyHive admin pages
        if ( in_array( $screen->id, array( 'dashboard' ) ) )
        {
            wp_enqueue_script( 'propertyhive_dashboard' );

            $params = array(
                'ajax_url'                      => admin_url('admin-ajax.php'),
            );
            wp_localize_script( 'propertyhive_dashboard', 'propertyhive_dashboard', $params );
        }

        if ( in_array( $screen->id, ph_get_screen_ids() ) ) 
        {
            wp_enqueue_script( 'ajax-chosen' );
            wp_enqueue_script( 'chosen' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            
            $api_key = get_option('propertyhive_google_maps_api_key');
            wp_register_script('googlemaps', '//maps.googleapis.com/maps/api/js?' . ( ( $api_key != '' && $api_key !== FALSE ) ? 'key=' . $api_key : '' ), false, '3');
            wp_enqueue_script('googlemaps');
        }
        
        if ( in_array( $screen->id, ph_get_screen_ids() ) )
        {
            wp_enqueue_media();
            wp_enqueue_script( 'propertyhive_admin_meta_boxes' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'ajax-chosen' );
            wp_enqueue_script( 'chosen' );
            
            $params = array(
                'plugin_url'                    => PH()->plugin_url(),
                'ajax_url'                      => admin_url('admin-ajax.php'),
                'post_id'                       => isset( $post->ID ) ? $post->ID : '',
                'add_note_nonce'                => wp_create_nonce("add-note"),
                'delete_note_nonce'             => wp_create_nonce("delete-note"),
            );
            wp_localize_script( 'propertyhive_admin_meta_boxes', 'propertyhive_admin_meta_boxes', $params );

            if ( is_rtl() ) 
            {
                wp_enqueue_script( 'chosen-rtl', PH()->plugin_url() . '/assets/js/chosen/chosen-rtl' . /*$suffix .*/ '.js', array( 'jquery' ), PH_VERSION, true );
            }
        }
        
        if ( in_array( $screen->id, array( 'property-hive_page_ph-settings' ) ) )
        {
            wp_enqueue_script( 'propertyhive_admin_settings' );
        
            $params = array(
                'confirm_not_selected_warning'              => __( 'Please check the box to confirm that you are happy to remove this term', 'propertyhive' ),
                'no_departments_selected_warning'           => __( 'Please select at least one active department', 'propertyhive' ),
                'primary_department_not_active_warning'     => __( 'The chosen primary department has not been selected as active', 'propertyhive' ),
                'no_countries_selected'                     => __( 'Please select which countries you operate in', 'propertyhive' ),
                'default_country_not_in_selected'           => __( 'The default country hasn\'t been selected as a country you operate in', 'propertyhive' ),
                'admin_url'                                 => admin_url(),
                'taxonomy_section'                          => ( ( isset($_GET['section']) ) ? $_GET['section'] : '' ),
            );
            wp_localize_script( 'propertyhive_admin_settings', 'propertyhive_admin_settings', $params );
        }
        
        // Reports Pages
        if ( in_array( $screen->id, array( 'property-hive_page_ph-reports' ) ) )
        {
           //wp_register_script( 'ph-reports', PH()->plugin_url() . '/assets/js/admin/reports' . /*$suffix .*/ '.js', array( 'jquery', 'jquery-ui-datepicker' ), PH_VERSION );

            //wp_enqueue_script( 'ph-reports' );
            wp_enqueue_script( 'flot' );
            wp_enqueue_script( 'flot-resize' );
            //wp_enqueue_script( 'flot-time' );
            //wp_enqueue_script( 'flot-pie' );
            //wp_enqueue_script( 'flot-stack' );
        }
    }

}

endif;

return new PH_Admin_Assets();