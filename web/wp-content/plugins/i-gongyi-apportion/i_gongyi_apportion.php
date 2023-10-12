<?php

/*
Plugin Name: i_gongyi_apportion
Plugin URI: null
Description: SOG俱乐部插件，用于核算成本，分摊费用。开启后，可访问api接口，进行日常餐饮签到，查询点菜数据等，功能开发中，by 2023年9月30日。
Version: 1.0.1
Author: li_zi/as
Author URI: null
License: null
*/

//该文件为主php文件，用于加载其他文件，以及定义一些常用函数，如错误返回值等。
//------------------------------------------------------------------------------------------

//泛用插件，首页重定向等
include 'common.php';
// 身份数据、工人、学生等
include 'i_profile.php';
//创建、删除、查询等群组接口
include 'i_group_api.php';
//增删开支方面接口
include 'i_group_item_api.php';
//群组和群员相关功能
include 'i_group_req_api.php';
//日常餐饮数据库签到查询
include 'i_sign_up_api.php';
//用来处理原材料和成品之间的关系表api
include 'i_group_relationship_api.php';

//------------------------------------------------------------------------------------------
// 获取日常饮食的价格，如果未设置，则返回0

function get_daily_food_price(){
	return 100;
}

//------------------------------------------------------------------------------------------
// 更改状态码的消息

function update_response_msg($callback,$msg){
	$res = $callback();
	$res['msg'] = $msg;
	return $res;
}

//------------------------------------------------------------------------------------------
// 获取200HTML状态码请求成功

function get_200_response(){
	return [
		'code' => '200',
		'msg' => '请求成功',
		'data' => true,
	];
}

//------------------------------------------------------------------------------------------
//请求错误

function get_html_error($code){
	return [
		'code' => $code,
		'msg' => '错误代码'+$code,
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//400错误，请求错误，服务器不理解请求的语法

function get_400_error(){
	return [
		'code' => '400',
		'msg' => '错误代码400',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//401错误，未登录

function get_401_error(){
	return [
		'code' => '401',
		'count' => 0,
		'msg' => '未登录',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//404错误，服务器找不到请求的网页资源

function get_404_error(){
	return [
		'code' => '404',
		'msg' => '错误代码404',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//409错误，请求冲突，由于和被请求的资源的当前状态之间存在冲突，请求无法完成

function get_409_error(){
	return [
		'code' => '409',
		'msg' => '错误代码409',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//422错误，参数错误或缺乏相应的参数

function get_422_error(){
	return [
		'code' => '422',
		'msg' => '参数错误，不可处理访问',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//4011错误，未设置学生或工人身份

function get_4011_error(){
	return [
		'code' => '4011',
		'count' => 0,
		'msg' => '请先设置身份',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//4014错误，对象被删除，操作权限不足

function get_4014_error(){
	return [
		'code' => '4014',
		'msg' => '对象被删除，无法进行操作',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//4020错误，非对象的归属者，操作权限不足

function get_4020_error(){
	return [
		'code' => '4020',
		'msg' => '权限不足，无法进行操作',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//4024错误，非群组普通管理员，操作权限不足

function get_4024_error(){
	return [
		'code' => '4024',
		'msg' => '您不是管理员，无法进行操作',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//4025错误，非群组超级管理员，操作权限不足

function get_4025_error(){
	return [
		'code' => '4025',
		'msg' => '您不是超级管理员，无法进行操作',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//4031错误，该群为引用模板，无法进行操作

function get_4031_error(){
	return [
		'code' => '4031',
		'msg' => '该群为引用模板，无法进行操作',
		'data' => false,
	];
}

//------------------------------------------------------------------------------------------
//获取类目表名称wp_i_costs_apportion

function get_table_name(){
	global $table_prefix; 
	$table_name = $table_prefix . 'i_costs_apportion';
	return $table_name;
}

//------------------------------------------------------------------------------------------
//获取群组表名称wp_i_group

function get_group_table_name(){
	global $table_prefix; 
	$table_name = $table_prefix . 'i_group';
	return $table_name;
}

//------------------------------------------------------------------------------------------
//获取类目表名称wp_i_group_req

function get_group_req_table_name(){
	global $table_prefix; 
	$table_name = $table_prefix . 'i_group_req';
	return $table_name;
}

//------------------------------------------------------------------------------------------
//获取类目表名称wp_i_costs_relationship

function get_group_relationship_table_name(){
	global $table_prefix; 
	$table_name = $table_prefix . 'i_costs_relationship';
	return $table_name;
}

//------------------------------------------------------------------------------------------
//获取用户表名称wp_usermeta

function get_usermeta_table_name(){
	global $table_prefix; 
	$table_name = $table_prefix . 'usermeta';
	return $table_name;
}

//------------------------------------------------------------------------------------------
//获取用户表名称wp_users

function get_users_table_name(){
	global $table_prefix; 
	$table_name = $table_prefix . 'users';
	return $table_name;
}

//------------------------------------------------------------------------------------------
//通过GET方法获取页码条数

function get_page_number(){
	$page_number = 0;
	if (isset($_GET['page_number']) && !empty($_GET['page_number']) && $_GET['page_number']!= null && $_GET['page_number']!= 'null') {
		$page_number = $_GET['page_number'];
	}
	return $page_number;
}

//------------------------------------------------------------------------------------------
//通过GET方法获取分页尺寸

function get_page_size(){
	$page_size = null;
	if (isset($_GET['page_size']) && !empty($_GET['page_size']) && $_GET['page_size']!= null && $_GET['page_size']!= 'null') {
		$page_size = $_GET['page_size'];
	}
	return $page_size;
}

//------------------------------------------------------------------------------------------
//通过方法获取GET值

function i_GET($res , $get_value){
	if (isset($_GET[$get_value]) && !empty($_GET[$get_value]) && $_GET[$get_value]!= null && $_GET[$get_value]!= 'null') {
		$res = $_GET[$get_value];
	}
	return $res;
}

//------------------------------------------------------------------------------------------
//通过方法获取POST值

function i_POST($res , $get_value){
	if (isset($_POST[$get_value]) && !empty($_POST[$get_value]) && $_POST[$get_value]!= null && $_POST[$get_value]!= 'null') {
		$res = $_POST[$get_value];
	}
	return $res;
}

//------------------------------------------------------------------------------------------
//通过方法获取用户id，警告！如果需要鉴权不应当使用该函数，该函数会直接从get中获取id，这意味着可以任意更改id值。

function get_user_id(){
	$user_id = get_current_user_id();
	if (isset($_GET['user_id']) && !empty($_GET['user_id']) && $_GET['user_id']!= null && $_GET['user_id']!= 'null') {
		$user_id = $_GET['user_id'];
	}
	return $user_id;
}

//------------------------------------------------------------------------------------------
// 通过方法获取群组id

function get_group_id(){
	$group_id = 0;
	if (isset($_GET['group_id']) && !empty($_GET['group_id']) && $_GET['group_id']!=null) {
		$group_id = $_GET['group_id'];
	}
	return $group_id;
}

//------------------------------------------------------------------------------------------
// 获取群组的非敏感字段，屏蔽群组的敏感字段例如随机密码等

function get_group_table_column_fields(){
	$table_name = get_group_table_name();
	$fields = "{$table_name}.id, {$table_name}.name, {$table_name}.description, {$table_name}.created_time, {$table_name}.create_user_id, {$table_name}.class_type, {$table_name}.is_delete, {$table_name}.state, {$table_name}.father_group_id";
	return $fields;
}

//------------------------------------------------------------------------------------------
// 用于生成长度为11的随机密码

function rd_pwd($length = 11){
	$randomText = '';
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	for ($i = 0; $i < $length; $i++) {
			$randomText .= $chars[random_int(0, strlen($chars) - 1)];
	}
	return $randomText;
}

//------------------------------------------------------------------------------------------
// 用于nonce验证消息，如果删掉会导致用户安全验证失败，从而失去返回值，显示未登录，需要配合nonce请求使用。

function enqueue_scripts_styles_init() {
	wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri().'/js/script.js', array('jquery'), 1.0 );
	wp_localize_script( 'ajax-script', 'WP_API_Settings', array( 
            'root' => esc_url_raw( rest_url() ), 
            'nonce' => wp_create_nonce( 'wp_rest' ) 
        )
    );
}
add_action('init', 'enqueue_scripts_styles_init');

//------------------------------------------------------------------------------------------
// 用该方法注册查询某个字段的路由api

function get_value_by_field_in_group($value, $id, $field, $table_name){
	global $wpdb;
	$res = $wpdb->get_var("SELECT $value FROM $table_name WHERE $field = $id");
	return $res;
};

function rest_route_get_value_by_field_in_group($value, $id, $field, $table_name,$path = null){
	function i_get_value_by_field_in_group($value, $id, $field, $table_name){
		$sql = get_value_by_field_in_group($value, $id, $field, $table_name);
		if($sql){
			$res = get_200_response();
			$res['data'] = $sql;
			return $res;
		}else{
			return get_404_error();
		}
	}
	
	function i_rest_route_get_value_by_field_in_group($value, $id, $field, $table_name,$path=null){
		if($path == null){
			$path = 'i_get_'+ $value +'_by_'+ $field +'_in_'+$table_name;
		}
		register_rest_route( 'gongyi/', $path, [
			'methods' => 'get',
			'callback' => function() use ($value, $id, $field, $table_name) {
        return i_get_value_by_field_in_group($value, $id, $field, $table_name);
			}
		]);
	}
	add_action("rest_api_init",function() use ($value, $id, $field, $table_name, $path){
		return i_rest_route_get_value_by_field_in_group($value, $id, $field, $table_name, $path);
	});
}

//------------------------------------------------------------------------------------------
// 绑定路由，i_rest_route必须放在rest_route作用域外面，每次调用都会创建一个新的闭包函数，否则重复调用会报错

function i_rest_route($path, $methods, $callback) {
	register_rest_route('gongyi/', $path, [
			'methods' => $methods,
			'callback' => $callback
	]);
}
function rest_route($path, $callback, $methods = 'get'){
	add_action("rest_api_init",function() use($path, $methods, $callback){
		return i_rest_route($path, $methods, $callback);
	});
}

//------------------------------------------------------------------------------------------
// 事务 - 开始

function pury_custom_transaction_begin(){
	global $wpdb;
	return $wpdb->query("START TRANSACTION");
}

//------------------------------------------------------------------------------------------
// 事务 - 提交

function pury_custom_transaction_commit(){
	global $wpdb;
	return $wpdb->query("COMMIT");
}

//------------------------------------------------------------------------------------------
// 事务 - 回滚

function pury_custom_transaction_rollback(){
	global $wpdb;
	return $wpdb->query("ROLLBACK");
}