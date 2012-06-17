<?php
/*
Plugin Name: Trust Form For Flamingo
Plugin URI: http://www.kakunin-pl.us/
Description: Trust Form and <a href="http://flamingo-eggs.com/" target="_blank">Flamingo</a> work together.
Author: Horike Takahiro
Text Domain: trust-form-for-flamingo
Domain Path: /languages/
Version: 1.0
*/

add_action( 'flamingo_init', 'trfm_flamingo_init' );

function trfm_flamingo_init() {
	if ( ! class_exists( 'Flamingo_Inbound_Message' ) )
		return;

	if ( ! term_exists( 'trust-form', Flamingo_Inbound_Message::channel_taxonomy ) ) {
		wp_insert_term( __( 'Trust Form', 'trust-form' ), 
							Flamingo_Inbound_Message::channel_taxonomy,
							array( 'slug' => 'trust-form' )
		);
	}
}


add_filter( 'tr_new_responce', 'tr_flamingo_new_responce', 10, 2 );

function tr_flamingo_new_responce( $post, $type ) {
	if ( ! ( class_exists( 'Flamingo_Contact' ) && class_exists( 'Flamingo_Inbound_Message' ) ) )
		return;

	$args = array(
		'channel' => 'trust-form',
		'fields' => $post['data'],
		'email' => '',
		'name' => '',
		'from' => '',
		'subject' => apply_filters( 'trfla_save_subject', __( 'Trust Form', 'trust-form' ) )
	);

	if( false !== $email = array_search('e-mail', $type) )
		$args['from_email'] = $args['email'] = trim( $post['data'][$email] );

	$args['from_name'] = $args['name'] = trim( reset($post['data']) );

	$args['from'] = trim( sprintf( '%s <%s>', $args['from_name'], $args['from_email'] ) );

	Flamingo_Contact::add( $args );
	Flamingo_Inbound_Message::add( $args );
	
	return $post;
}
