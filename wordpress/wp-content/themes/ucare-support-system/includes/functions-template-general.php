<?php
/**
 * General purpose template functions and utilities.
 *
 * @since 1.6.0
 * @package ucare
 */
namespace ucare;

// Pull in assets with theme header
add_action( 'ucare_head', 'ucare\call_header' );

// Pull in assets with theme footer
add_action( 'ucare_footer', 'ucare\call_footer' );

/**
 * Retrieve a template from the templates/ directory. Arguments passed will be globally available in the template file.
 * Template files are included by default if the template is found. This function also optionally returns a configured
 * closure that can be executed at a later point.
 *
 * @param string $name     The name of the template (without the file extension).
 * @param array  $args     (Optional) Arguments to be passed to the template.
 * @param bool   $include  Whether or not the template file should be included.
 * @param bool   $once     Whether to include or include_once.
 * @param bool   $execute  Execute the include right away. When set to false, the internal closure will be returned to
 *                         so that the include can be executed at a later point.
 * @param object $bind    (Optional) The object of which the internal closure should bind $this to.
 *
 * @since 1.6.0
 * @return bool|string
 */
function get_template( $name, $args = array(), $include = true, $once = true, $execute = true, $bind = null ) {

    $template = false;
    $name = str_replace( '.php', '', $name ) . '.php';

    // Check root templates and partials path.
    if ( file_exists( UCARE_TEMPLATES_PATH . $name ) ) {
        $template = UCARE_TEMPLATES_PATH . $name;
    } else if ( file_exists( UCARE_PARTIALS_PATH . $name ) ) {
        $template = UCARE_PARTIALS_PATH . $name;
    }

    // If the template path is found
    if ( $template ) {

        // If we are to execute an include of the template file
        if ( $include ) {

            // Create a new closure
            $exec = function ( $args ) use ( $template, $once ) {

                // Extract args in scope of closure
                if ( is_array( $args ) ) {
                    extract( $args );
                }

                if ( $once ) {
                    include_once $template;
                } else {
                    include $template;
                }

            };

            // Bind new $this to the closure
            if ( is_object( $bind ) ) {
                $exec = \Closure::bind( $exec, $bind, $bind );
            }

            if ( $execute ) {
                $exec( $args );
            } else {
                return $exec;
            }

        }

        return $template;

    }

    return false;

}

/**
 * Execute and return the output of a template file as a string.
 *
 * @param string $name
 * @param array  $args
 * @param bool   $once
 *
 * @since 1.4.2
 * @return string
 */
function buffer_template( $name, $args = array(), $once = true ) {
    ob_start();
    get_template( $name, $args, true, $once );
    return apply_filters( "ucare_template-$name-html", ob_get_clean() );
}

/**
 * Output underscore.js templates.
 *
 * @since 1.4.2
 * @return void
 */
function print_underscore_templates() {
    get_template( 'underscore/tmpl-confirm-modal' );
    get_template( 'underscore/tmpl-notice-inline' );
    get_template( 'underscore/tmpl-ajax-loader-mask' );
}

/**
 * Print copyright text with branding.
 *
 * @since 1.4.2
 * @return void
 */
function print_footer_copyright() {
    $text  = get_option( Options::FOOTER_TEXT );
    $brand = apply_filters( 'ucare_footer_branding', true );

    if ( $text ) {
        echo $text . ( $brand ? ' | ' : '' );
    }

    if ( $brand ) { ?>
        <a href="http://ucaresupport.com" target="_blank">
            <?php _e( 'Powered by uCare Support', 'ucare' ); ?>
        </a>
    <?php }
}

/**
 * Pull in header scripts and styles on public pages.
 *
 * @action ucare_head
 *
 * @since 1.6.0
 * @return void
 */
function call_header() {
    if ( is_a_support_page() && is_page_public() && get_option( Options::LOAD_THEME_ASSETS ) ) wp_head();
}

/**
 * Pull in footer scripts on public pages.
 *
 * @action ucare_footer
 *
 * @since 1.6.0
 * @return void
 */
function call_footer() {
    if ( is_a_support_page() && is_page_public() && get_option( Options::LOAD_THEME_ASSETS ) ) wp_footer();
}
