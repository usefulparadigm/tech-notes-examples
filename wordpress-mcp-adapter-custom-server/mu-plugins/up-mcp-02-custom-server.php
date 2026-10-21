<?php
/**
 * Plugin Name: UP MCP Example - Custom Server
 * Description: 어빌리티 3개(최근 글 목록, 초안 만들기, 내 초안 휴지통)를 등록하고 MCP Adapter 커스텀 서버로 노출한다.
 * Version: 0.1.0
 * Author: Usefulparadigm
 * Requires at least: 6.9
 * License: MIT
 *
 * wp-content/mu-plugins/ 에 넣어 사용한다. 01 파일과 같은 이름을 등록하므로 둘 중 하나만 둔다.
 * 엔드포인트: https://사이트주소/wp-json/usefulparadigm/mcp
 */

// 기본 서버(mcp-adapter-default-server)를 끄려면 아래 줄의 주석을 푼다.
// add_filter( 'mcp_adapter_create_default_server', '__return_false' );

add_action( 'wp_abilities_api_categories_init', function () {
	wp_register_ability_category( 'usefulparadigm', array(
		'label'       => 'Usefulparadigm',
		'description' => '유스풀패러다임 테스트용 어빌리티',
	) );
} );

add_action( 'wp_abilities_api_init', function () {

	// (1) 읽기 전용: 최근 글 목록
	wp_register_ability( 'usefulparadigm/list-recent-posts', array(
		'label'        => '최근 글 목록',
		'description'  => '최근 발행된 글의 ID, 제목, URL, 날짜를 반환합니다.',
		'category'     => 'usefulparadigm',
		'input_schema' => array(
			'type'       => 'object',
			'properties' => array(
				'count' => array( 'type' => 'integer', 'default' => 5, 'minimum' => 1, 'maximum' => 20 ),
			),
		),
		'output_schema'       => array( 'type' => 'array' ),
		'execute_callback'    => function ( $input ) {
			$posts = get_posts( array( 'numberposts' => $input['count'] ?? 5 ) );
			return array_map( fn( $p ) => array(
				'id'    => $p->ID,
				'title' => get_the_title( $p ),
				'url'   => get_permalink( $p ),
				'date'  => get_the_date( 'c', $p ),
			), $posts );
		},
		'permission_callback' => fn() => current_user_can( 'edit_posts' ),
		'meta'                => array(
			'annotations' => array( 'readonly' => true, 'destructive' => false, 'idempotent' => true ),
		),
	) );

	// (2) 쓰기(비파괴): 초안 만들기. 상태는 항상 draft로 고정한다.
	wp_register_ability( 'usefulparadigm/create-draft', array(
		'label'        => '초안 만들기',
		'description'  => '제목과 본문으로 새 글 초안을 만듭니다. 발행하지 않고 항상 초안(draft)으로 저장합니다.',
		'category'     => 'usefulparadigm',
		'input_schema' => array(
			'type'       => 'object',
			'properties' => array(
				'title'   => array( 'type' => 'string', 'minLength' => 1 ),
				'content' => array( 'type' => 'string', 'default' => '' ),
			),
			'required'   => array( 'title' ),
		),
		'output_schema'       => array( 'type' => 'object' ),
		'execute_callback'    => function ( $input ) {
			$id = wp_insert_post( array(
				'post_title'   => sanitize_text_field( $input['title'] ),
				'post_content' => wp_kses_post( $input['content'] ?? '' ),
				'post_status'  => 'draft',
				'post_author'  => get_current_user_id(),
			), true );
			if ( is_wp_error( $id ) ) {
				return $id;
			}
			return array(
				'id'        => $id,
				'status'    => get_post_status( $id ),
				'edit_link' => get_edit_post_link( $id, 'raw' ),
			);
		},
		'permission_callback' => fn() => current_user_can( 'edit_posts' ),
		'meta'                => array(
			'annotations' => array( 'readonly' => false, 'destructive' => false, 'idempotent' => false ),
		),
	) );

	// (3) 파괴적: 내가 만든 초안만 휴지통으로 (발행된 글, 남의 글은 거부)
	wp_register_ability( 'usefulparadigm/trash-own-draft', array(
		'label'        => '내 초안 휴지통으로 보내기',
		'description'  => '현재 사용자가 직접 만든 초안(draft)만 휴지통으로 옮깁니다. 발행된 글이나 다른 사람의 글은 거부합니다. 영구 삭제는 하지 않습니다.',
		'category'     => 'usefulparadigm',
		'input_schema' => array(
			'type'       => 'object',
			'properties' => array(
				'id' => array( 'type' => 'integer', 'minimum' => 1 ),
			),
			'required'   => array( 'id' ),
		),
		'output_schema'       => array( 'type' => 'object' ),
		'execute_callback'    => function ( $input ) {
			$post = wp_trash_post( (int) $input['id'] );
			return array(
				'id'      => (int) $input['id'],
				'trashed' => (bool) $post,
			);
		},
		'permission_callback' => function ( $input ) {
			$post = get_post( (int) ( $input['id'] ?? 0 ) );
			return $post
				&& 'draft' === $post->post_status
				&& (int) $post->post_author === get_current_user_id()
				&& current_user_can( 'delete_post', $post->ID );
		},
		'meta'                => array(
			'annotations' => array( 'readonly' => false, 'destructive' => true, 'idempotent' => true ),
		),
	) );
} );

// 커스텀 서버: https://사이트주소/wp-json/usefulparadigm/mcp
add_action( 'mcp_adapter_init', function ( $adapter ) {
	$adapter->create_server(
		'usefulparadigm-content',                        // 서버 ID
		'usefulparadigm',                                // REST 네임스페이스
		'mcp',                                           // REST 경로
		'Usefulparadigm Content Server',                 // 서버 이름
		'글 목록 조회, 초안 작성, 내 초안 휴지통 이동만 노출', // 설명
		'v0.1.0',                                        // 버전
		array( \WP\MCP\Transport\HttpTransport::class ),
		\WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class,
		\WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
		array(                                           // 개별 도구로 노출할 어빌리티
			'usefulparadigm/list-recent-posts',
			'usefulparadigm/create-draft',
			'usefulparadigm/trash-own-draft',
		),
		array(),                                         // 리소스
		array()                                          // 프롬프트
	);
} );
