<?php

//------------------------------------------------------------------------------------------
//无法删除父主题的钩子，会直接报错
//很奇怪……怀疑和$this传递的类的参数有关系

// public function i_bottom_footer_content() {
// 	$hestia_general_credits = sprintf(
// 		/* translators: %1$s is Theme Name, %2$s is WordPress */
// 		esc_html__( '%1$s | Learn more： %2$s', 'sog' ),
// 		esc_html__( 'Sog', 'sog' ),
// 		/* translators: %1$s is URL, %2$s is WordPress */
// 		sprintf(
// 			'<a href="%1$s" rel="nofollow">%2$s</a>',
// 			esc_url( __( 'https://space.bilibili.com/3493258344795060', 'sog' ) ),
// 			'地上的星'
// 		)
// 	);
// }

// add_action('wp_logout','auto_redirect_after_logout');   
// function auto_redirect_after_logout(){   
//   wp_redirect( home_url() );   
//   exit();   
// }

// add_action( 'hestia_do_bottom_footer_content', 'i_bottom_footer_content' );


//------------------------------------------------------------------------------------------
// 确保 main.js 作为 ES6 模块加载
// 它将调用此函数，将类型“module”附加到脚本标记。

// add_filter( 'script_loader_tag', 'load_as_ES6',10,3 );

// public function load_as_ES6($tag, $handle, $source){
// 	if('main'===$handle){
// 			$tag ='<script src="' . $source . '" type="module" ></script>';
// 	}
// 	return $tag;
// }

//------------------------------------------------------------------------------------------
// 启用短代码

add_filter( 'widget_text', 'do_shortcode' );

//------------------------------------------------------------------------------------------
// 启用路由：写法有问题需要优化

function loadCustomTemplate($template) {
	global $wp_query;
	if(!file_exists($template))return;
	$wp_query->is_page = true;
	$wp_query->is_single = false;
	$wp_query->is_home = false;
	$wp_query->comments = false;
	// if we have a 404 status
	if ($wp_query->is_404) {
	// set status of 404 to false
		unset($wp_query->query["error"]);
		$wp_query->query_vars["error"]="";
		$wp_query->is_404=false;
	}
	// change the header to 200 OK
	header("HTTP/1.1 200 OK");
	//load our template
	include($template);
	exit;
}

function templateRedirect() {
	$basename = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
	loadCustomTemplate(get_stylesheet_directory().'/custom/'."/$basename.php");
}

add_action('template_redirect', 'templateRedirect');

//------------------------------------------------------------------------------------------
// 修改用户角色和权限的名称
//WordPress 修改用户角色

add_action('init', 'fanly_change_role_name');
function fanly_change_role_name() {
	global $wp_roles;
	if ( ! isset( $wp_roles ) )$wp_roles = new WP_Roles();
	$wp_roles->roles['administrator']['name'] = '网站管理';
	$wp_roles->role_names['administrator'] = '网站管理';

	$wp_roles->roles['editor']['name'] = '编辑';
	$wp_roles->role_names['editor'] = '编辑';

	//订阅者
	// $wp_roles->roles['subscriber']['name'] = '订阅者';
	// $wp_roles->role_names['subscriber'] = '订阅者';
	remove_role( 'subscriber' );

	$wp_roles->roles['author']['name'] = '信息管理';
	$wp_roles->role_names['author'] = '信息管理';

	$wp_roles->roles['contributor']['name'] = '用户';
	$wp_roles->role_names['contributor'] = '用户';

	add_role('custom_role', '联络员', array(
    'read' => true, //阅读权限，true 表示允许
    'edit_posts' => true,//编辑文章的权限，true 为允许
    'delete_posts' => true, //删除文章的权限，false 表示不允许删除
		'switch_themes' => true,
		'edit_themes' => true,
		'activate_plugins' => true,
		'edit_plugins' => true,
		'edit_users' => true,
		'edit_files' => true,
		'manage_options' => true,
		'moderate_comments' => true,
		'manage_categories' => true,
		'manage_links' => true,
		'upload_files' => true,
		'import' => true,
		'unfiltered_html' => true,
		'edit_others_posts' => true,
		'edit_published_posts' => true,
		'publish_posts' => true,
		'edit_pages' => true,
		'level_10' => true,
		'level_9' => true,
		'level_8' => true,
		'level_7' => true,
		'level_6' => true,
		'level_5' => true,
		'level_4' => true,
		'level_3' => true,
		'level_2' => true,
		'level_1' => true,
		'level_0' => true,));
}

//------------------------------------------------------------------------------------------

// add_filter('get_header', 'fanly_ssl');
// function fanly_ssl(){
// 	if( is_ssl() ){
// 		function fanly_ssl_main ($content){
// 			$siteurl = get_option('siteurl');
// 			$upload_dir = wp_upload_dir();
// 			$content = str_replace( 'https:'.strstr($siteurl, '//'), 'http:'.strstr($siteurl, '//'), $content);
// 			$content = str_replace( 'https:'.strstr($upload_dir['baseurl'], '//'), 'http:'.strstr($upload_dir['baseurl'], '//'), $content);
// 			return $content;
// 		}
// 		ob_start("fanly_ssl_main");
// 	}
// }

//------------------------------------------------------------------------------------------
// 延长登陆时间

add_filter('auth_cookie_expiration', 'custom_cookie_expiration', 99, 3);
function custom_cookie_expiration($expiration, $user_id = 0, $remember = true) {
    if($remember) {
        $expiration = 31536000;
    }
    return $expiration;
}
