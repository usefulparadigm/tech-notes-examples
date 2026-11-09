<?php
/**
 * 골목커피 theme functions.
 *
 * @package golmokcoffee
 */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'golmokcoffee-style',
		get_parent_theme_file_uri( 'style.css' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);
} );

add_action( 'after_setup_theme', function () {
	add_editor_style( 'style.css' );
} );

add_action( 'init', function () {
	register_block_style( 'core/group', array( 'name' => 'shop-panel', 'label' => __( '방문 안내', 'golmokcoffee' ) ) );
	register_block_style( 'core/group', array( 'name' => 'menu-sheet', 'label' => __( '메뉴판', 'golmokcoffee' ) ) );
} );
