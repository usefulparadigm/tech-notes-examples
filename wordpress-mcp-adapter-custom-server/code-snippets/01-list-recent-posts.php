// Code Snippets: "MCP 테스트 1 - 최근 글 목록"
// 실행 범위: Run everywhere
// 기본 서버(discover)에 공개되는 읽기 전용 어빌리티 하나

add_action( 'wp_abilities_api_categories_init', function () {
	wp_register_ability_category( 'usefulparadigm', array(
		'label'       => 'Usefulparadigm',
		'description' => '유스풀패러다임 테스트용 어빌리티',
	) );
} );

add_action( 'wp_abilities_api_init', function () {
	wp_register_ability( 'usefulparadigm/list-recent-posts', array(
		'label'        => '최근 글 목록',
		'description'  => '최근 발행된 글의 제목, URL, 날짜를 반환합니다.',
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
				'title' => get_the_title( $p ),
				'url'   => get_permalink( $p ),
				'date'  => get_the_date( 'c', $p ),
			), $posts );
		},
		'permission_callback' => fn() => current_user_can( 'edit_posts' ),
		'meta'                => array(
			'annotations' => array( 'readonly' => true ),
			'mcp'         => array( 'public' => true ),
		),
	) );
} );
