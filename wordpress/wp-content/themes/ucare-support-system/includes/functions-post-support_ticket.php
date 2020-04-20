<?php
/**
 * Functions for managing the support ticket post type.
 *
 * @since 1.4.2
 * @package ucare
 */
namespace ucare;


// Register the custom post type
add_action( 'init', 'ucare\register_ticket_post_type' );

add_action( 'init', 'ucare\register_ticket_custom_status' );

add_action( 'wp_insert_post', 'ucare\set_default_ticket_meta', 10, 3 );

add_action( 'update_post_metadata', 'ucare\ticket_properties_updated', 10, 4 );


/**
 * Register the support ticket post type.
 *
 * @since 1.0.0
 * @return void
 */
function register_ticket_post_type() {
    $labels = array(
        'name'                  => _x( 'Support Tickets', 'Post Type General Name', 'ucare' ),
        'singular_name'         => _x( 'Support Ticket', 'Post Type Singular Name', 'ucare' ),
        'menu_name'             => __( 'uCare Support', 'ucare' ),
        'name_admin_bar'        => __( 'uCare Support', 'ucare' ),
        'archives'              => __( 'Item Archives', 'ucare' ),
        'parent_item_colon'     => __( 'Parent Item:', 'ucare' ),
        'all_items'             => __( 'Ticket List', 'ucare' ),
        'add_new_item'          => __( 'Create Ticket', 'ucare' ),
        'add_new'               => __( 'Create Ticket', 'ucare' ),
        'new_item'              => __( 'Create Ticket', 'ucare' ),
        'edit_item'             => __( 'Edit Ticket', 'ucare' ),
        'update_item'           => __( 'Update Ticket', 'ucare' ),
        'view_item'             => __( 'View Ticket', 'ucare' ),
        'search_items'          => __( 'Search Ticket', 'ucare' ),
        'not_found'             => __( 'Ticket Not found', 'ucare' ),
        'not_found_in_trash'    => __( 'Ticket Not found in Trash', 'ucare' ),
        'featured_image'        => __( 'Featured Image', 'ucare' ),
        'set_featured_image'    => __( 'Set featured image', 'ucare' ),
        'remove_featured_image' => __( 'Remove featured image', 'ucare' ),
        'use_featured_image'    => __( 'Use as featured image', 'ucare' ),
        'insert_into_item'      => __( 'Insert into ticket', 'ucare' ),
        'uploaded_to_this_item' => __( 'Uploaded to this ticket', 'ucare' ),
        'items_list'            => __( 'Tickets list', 'ucare' ),
        'items_list_navigation' => __( 'Tickets list navigation', 'ucare' ),
        'filter_items_list'     => __( 'Filter tickets list', 'ucare' )
    );

    $args = array(
        /**
         *
         * @since 1.0.0
         */
        'label'                => __( 'Support Ticket', 'ucare' ),
        'description'          => __( 'Tickets for support requests', 'ucare' ),
        'labels'               => $labels,
        'supports'             => array( 'editor', 'comments', 'title', 'custom-fields' ),
        'hierarchical'         => false,
        'public'               => false,
        'show_ui'              => true,
        'show_in_menu'         => false,
        'menu_position'        => 10,
        'menu_icon'            => 'dashicons-sos',
        'show_in_admin_bar'    => false,
        'show_in_nav_menus'    => false,
        'can_export'           => true,
        'has_archive'          => false,
        'exclude_from_search'  => true,
        'publicly_queryable'   => false,
        'capability_type'      => array( 'support_ticket', 'support_tickets' ),
        'feeds'                => null,
        'map_meta_cap'         => true,

        /**
         *
         * @since 1.6.0
         */
        'show_in_rest'         => current_user_can( 'use_support' ),
        'rest_base'            => 'support-tickets'
    );

    if ( ucare_is_support_agent() ) {
        array_push( $args['supports'], 'author' );
    }

    register_post_type( 'support_ticket', $args );
}


/**
 * See if a post is a support ticket.
 *
 * @param null|int|\WP_Post $ticket
 *
 * @since 1.6.0
 * @return bool
 */
function is_support_ticket( $ticket = null ) {
    $ticket = get_post( $ticket );

    if ( !$ticket || $ticket->post_type !== 'support_ticket' ) {
        return false;
    }

    return true;
}


/**
 * Get a list of the ticket statuses.
 *
 * @since 1.4.2
 * @return array
 */
function get_ticket_statuses() {
    $statuses = array(
        'new'               => __( 'New', 'ucare' ),
        'waiting'           => __( 'Waiting', 'ucare' ),
        'opened'            => __( 'Opened', 'ucare' ),
        'responded'         => __( 'Responded', 'ucare' ),
        'needs_attention'   => __( 'Needs Attention', 'ucare' ),
        'closed'            => __( 'Closed', 'ucare' )
    );

    return apply_filters( 'ucare_ticket_statuses', $statuses );
}


/**
 * Get a list of the ticket priorities.
 *
 * @since 1.6.0
 * @return array
 */
function ticket_priorities() {
    $priorities = array(
        0 => __( 'Low', 'ucare' ),
        1 => __( 'Medium', 'ucare' ),
        2 => __( 'High', 'ucare' )
    );

    return apply_filters( 'ucare_ticket_priorities', $priorities );
}


/**
 * Register a custom status for auto draft tickets.
 *
 * @action init
 *
 * @since 1.6.0
 * @return void
 */
function register_ticket_custom_status() {
    $args = array(
        'private'                   => true,
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => false,
        'show_in_admin_status_list' => false
    );

    register_post_status( 'ucare-auto-draft', $args );
}


/**
 * Update ticket meta when the status is changed.
 *
 * @global $wbdb
 *
 * @action update_post_metadata
 *
 * @param $null
 * @param $id
 * @param $key
 * @param $value
 *
 * @since 1.0.0
 * @return void
 */
function ticket_properties_updated( $null, $id, $key, $value ) {
    global $wpdb;

    if ( get_post_type( $id ) == 'support_ticket' && $key == 'status' ) {
        $q = "UPDATE $wpdb->posts
              SET post_modified = %s, post_modified_gmt = %s
              WHERE ID = %d ";

        $q = $wpdb->prepare( $q, array( current_time( 'mysql' ), current_time( 'mysql', 1 ), $id ) );
        $wpdb->query( $q );

        delete_post_meta( $id, 'stale' );

        if ( $value == 'closed' ) {
            update_post_meta( $id, 'closed_date', current_time( 'mysql' ) );
            update_post_meta( $id, 'closed_by', wp_get_current_user()->ID );
        }
    }
}


/**
 * Set default ticket meta when a ticket is created.
 *
 * @action wp_insert_post
 *
 * @param $post_id
 * @param $post
 * @param $update
 *
 * @since 1.0.0
 * @return void
 */
function set_default_ticket_meta( $post_id, $post, $update ) {
    $defaults = array(
        'priority' => 0,
        'status'   => 'new'
    );

    if ( !$update ) {

        foreach ( $defaults as $key => $value ) {
            add_post_meta( $post_id, $key, $value, true );
        }
    }
}
