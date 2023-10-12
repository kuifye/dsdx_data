<?php

//如果用户需要对群本身进行操作，可以写在这里

//------------------------------------------------------------------------------------------
// 创建一个群组，方法：get
// api链接：url: 'localhost/?rest_route=/gongyi/i_group',

function i_group(){

	global $wpdb;

	$user_id = get_current_user_id();
	if (is_user_logged_in() == false){
		return get_401_error();
	}

	//若未设置身份，返回4011错误
	$identity = get_user_option( 'identity', $user_id );
	$expenses_weight = get_identity_weight($identity);
	if(!$expenses_weight){
		return get_4011_error();
	}
	$name= i_GET('平摊表', 'name');
	$description= i_GET('日常饮食平摊', 'description');
	$class_type= i_GET('日常饮食', 'class_type');
	$state = i_GET(0 , 'state');
	$father_group_id = i_GET(null , 'father_group_id');
	$table_name = get_group_table_name();
	$rd_pwd = rd_pwd();

	function create_group_by_name($table_name, $wpdb, $name, $description, $user_id, $class_type, $rd_pwd, $state, $father_group_id){
		return $wpdb->insert($table_name, array('name' => $name,'description' => $description, 'create_user_id' => $user_id,'class_type' => $class_type,'rd_pwd' => $rd_pwd ,'state' => $state, 'father_group_id' => $father_group_id), array( '%s', '%s', '%d', '%s', '%s', '%d', '%d'));
	}
	$sql = create_group_by_name($table_name, $wpdb, $name, $description, $user_id, $class_type, $rd_pwd, $state, $father_group_id);

	if ($sql) {
		// dbDelta($sql);
		$code = '200';
		$msg = '建群成功';
		//查询刚刚建立的群id，然后让该用户加入群组，使其成为超级管理------------------------------------------
		$group_id = $wpdb->insert_id; // 群号，即主键
		$user_lever = 5; //超级管理员
		$table_req_name = get_group_req_table_name();
		function insert_join_group_data($wpdb, $table_req_name ,$group_id ,$user_id ,$user_lever ,$expenses_weight){
			return $wpdb->insert($table_req_name, array('group_id' => $group_id, 'user_id' => $user_id, 'user_lever' => $user_lever, 'expenses_weight' => $expenses_weight), array( '%d', '%d', '%d', '%f'));
		}
		$sql = insert_join_group_data($wpdb, $table_req_name ,$group_id ,$user_id ,$user_lever ,$expenses_weight);
		//------------------------------------------------------------------------------------------------
	} else {
		return update_response_msg('get_404_error','错误：建群失败。');
	}
	return [
		'code' => $code,
		'msg' => $msg,
		'data' => $group_id,
	];
}
rest_route('i_group', 'i_group');

//------------------------------------------------------------------------------------------
// 查询某个id所建立的群
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_created_group_by_user_id',

function i_get_created_group_by_user_id(){

	global $wpdb;
	//参数获取
	$table_name = get_group_table_name();
	$user_id = get_user_id();
	$page_number = get_page_number();
	$page_size = get_page_size();

	//目前数据量较小，如果数据量大的话，可以考虑使用分页
	function get_created_group_by_user_id($wpdb, $table_name, $user_id, $page_number, $page_size) {
		return $wpdb->get_results( "SELECT * FROM $table_name WHERE create_user_id = $user_id AND is_delete = 0 ORDER BY id LIMIT $page_number, $page_size" );
	}
	$sql = get_created_group_by_user_id($wpdb, $table_name, $user_id, $page_number, $page_size);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_created_group_by_user_id', 'i_get_created_group_by_user_id');

//------------------------------------------------------------------------------------------
// 通过群id，查询某一个群的信息
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_group_info_by_id',使用方法是get

function i_get_group_info_by_id(){
	global $wpdb;
	$table_name = get_group_table_name();
	if (isset($_GET['group_id'])) {
		$group_id = $_GET['group_id'];
	}else{
		return get_422_error();
	}
	$table_column_fields = get_group_table_column_fields();
	function get_group_info_by_id($wpdb, $table_column_fields, $table_name, $group_id) {
		$query = "SELECT $table_column_fields FROM $table_name WHERE id = $group_id";
		return $wpdb->get_row($query, ARRAY_A);
	}
	$sql = get_group_info_by_id($wpdb, $table_column_fields, $table_name, $group_id);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_group_info_by_id', 'i_get_group_info_by_id');

//------------------------------------------------------------------------------------------
// 更改父群组

function set_father_group_id_by_group_id($table_name, $group_id, $father_group_id) {
	global $wpdb;
	return $wpdb->update("$table_name", array( 'father_group_id' => $father_group_id), array('id' => $group_id), array( '%d'), array( '%d'));
}

//------------------------------------------------------------------------------------------
// 更改父群组、需要先进行群组超级管理员验证、或者网站管理验证
// api链接：url: 'localhost/?rest_route=/gongyi/i_set_father_group_id_by_group_id',使用方法是Post

function i_set_father_group_id_by_group_id(){

	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$user_id = get_current_user_id();
	$table_name = get_group_table_name();
	$group_id = i_POST(null , 'group_id');
	$is_super_admin = get_user_id_in_group_is_admin($user_id, $group_id,5);

	if(!$is_super_admin){
		return get_4025_error();
	}

	$sql = set_father_group_id_by_group_id($table_name, $group_id, $father_group_id);

	if ($sql) {
		return update_response_msg('get_200_response','设置成功');
	}else{
		return update_response_msg('get_404_error','设置失败');
	}
}
rest_route('i_set_father_group_id_by_group_id', 'i_set_father_group_id_by_group_id','post');

//------------------------------------------------------------------------------------------
// 删群、需要先进行群组超级管理员验证
// api链接：url: 'localhost/?rest_route=/gongyi/i_delete_group_by_id',使用方法是Post

function i_delete_group_by_id(){

	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$user_id = get_current_user_id();
	$table_name = get_group_table_name();
	$group_id = $_POST['group_id'];
	$is_super_admin = get_user_id_in_group_is_admin($user_id, $group_id,5);

	if(!$is_super_admin){
		return get_4025_error();
	}

	function delete_group_by_id($wpdb, $table_name, $group_id) {
		return $wpdb->update("$table_name", array( 'is_delete' => 1), array('id' => $group_id), array( '%d'), array( '%d'));
	}
	$sql = delete_group_by_id($wpdb, $table_name, $group_id);

	if ($sql) {
		return update_response_msg('get_200_response','删群成功');
	}else{
		return update_response_msg('get_404_error','删群失败');
	}
}
rest_route('i_delete_group_by_id', 'i_delete_group_by_id','post');

//------------------------------------------------------------------------------------------
// 查询是否存在该群，如果不存在，则返回bool值

function get_group_exist($group_id){
	global $wpdb;
	$table_name = get_group_table_name();

	$existing_group = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE id = $group_id");
	if ($existing_group == 0) {
		return false;
	}else{
		return true;
	};
};

//------------------------------------------------------------------------------------------
// 查询是否存在该群
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_group_exist',使用方法是get

function i_get_group_exist(){
	if (isset($_GET['group_id'])) {
		$group_id = $_GET['group_id'];
	}else{
		return get_422_error();
	}
	$group_exist = get_group_exist($group_id);
	if ($group_exist) {
		return update_response_msg('get_200_response','该群存在。');
	}else{
		return update_response_msg('get_404_error','错误：该群组不存在。');
	}
}
rest_route('i_get_group_exist', 'i_get_group_exist');

//------------------------------------------------------------------------------------------
// 查询某个群是否被删除

function get_is_delete_by_group_id($group_id){
	global $wpdb;
	$table_name = get_group_table_name();

	$is_delete = $wpdb->get_var("SELECT is_delete FROM $table_name WHERE id = $group_id");
	if ($is_delete == 1) {
		return true;
	}else{
		return false;
	};
};

//------------------------------------------------------------------------------------------
// 查询某个群状态值

function get_state_by_group_id($group_id){
	global $wpdb;
	$table_name = get_group_table_name();

	$state = $wpdb->get_var("SELECT state FROM $table_name WHERE id = $group_id");
	return $state;
};

//------------------------------------------------------------------------------------------
// 查询某个群的父群id

function get_father_group_id_by_group_id($group_id){
	global $wpdb;
	$table_name = get_group_table_name();

	$father_group_id = $wpdb->get_var("SELECT father_group_id FROM $table_name WHERE id = $group_id");
	return $father_group_id;
};

//------------------------------------------------------------------------------------------
// 同时查询wp_i_group和wp_i_group_req两张表，并获取某个用户id所有加入的群聊的信息的总条数

function get_count_all_group_info_by_user_id($user_id, $is_delete, $state){
	global $wpdb;
	$table_name = get_group_table_name();
	$table_req_name = get_group_req_table_name();
	$query = <<<STR
	SELECT COUNT(*) 
	FROM $table_req_name 
	INNER JOIN $table_name ON $table_req_name.group_id = $table_name.id 
	WHERE $table_req_name.user_id = $user_id AND $table_name.is_delete = $is_delete AND $table_name.state = $state
	STR;
	$sql = $wpdb->get_var($query);
	return $sql;
}

//------------------------------------------------------------------------------------------
// 同时查询wp_i_group和wp_i_group_req两张表，并获取某个用户id所有加入的群聊的信息
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_all_info_by_user_id',使用方法是get

function i_get_all_group_info_by_user_id(){
	global $wpdb;
	$table_name = get_group_table_name();
	$table_req_name = get_group_req_table_name();
	$user_id =get_user_id();
	$page_number = get_page_number();
	$page_size = get_page_size();
	$is_delete = i_GET(0 , 'is_delete');
	$state = i_GET(0 , 'state');
	$table_column_fields = get_group_table_column_fields();
	$count = get_count_all_group_info_by_user_id($user_id, $is_delete, $state);
	if($page_size == null){
		$page_size = $count;
	}

	function get_all_info_by_user_id($wpdb, $table_column_fields, $table_name, $table_req_name, $user_id, $page_number, $page_size, $is_delete, $state) {
		$query = <<<STR
		SELECT $table_req_name.*, $table_column_fields
		FROM $table_req_name
		INNER JOIN $table_name ON $table_req_name.group_id = $table_name.id
		WHERE $table_req_name.user_id = $user_id AND $table_name.is_delete = $is_delete AND $table_name.state = $state
		LIMIT $page_number, $page_size;
		STR;
		return $wpdb->get_results($query);
	}
	$sql = get_all_info_by_user_id($wpdb, $table_column_fields, $table_name, $table_req_name, $user_id, $page_number, $page_size, $is_delete, $state);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		$res['count'] = $count;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_all_info_by_user_id', 'i_get_all_group_info_by_user_id');

//------------------------------------------------------------------------------------------
// 查询群随机密码
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_rd_pwd_by_group_id',使用方法是get

function i_get_rd_pwd_by_group_id(){
	global $wpdb;
	$table_name = get_group_table_name();
	$group_id = get_group_id();

	if(!get_user_id_in_group_is_admin(get_current_user_id() ,$group_id ,4)){
		return get_4025_error();
	}

	function get_rd_pwd_by_group_id($wpdb, $table_name, $group_id) {
		$query = "SELECT rd_pwd FROM $table_name WHERE id = $group_id";
		return $wpdb->get_var($query);
	}
	$sql = get_rd_pwd_by_group_id($wpdb, $table_name, $group_id);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_rd_pwd_by_group_id', 'i_get_rd_pwd_by_group_id');

//------------------------------------------------------------------------------------------
// 核验群随机密码

function check_rd_pwd_by_group_id($group_id, $rd_pwd_need_check){
	global $wpdb;
	$table_name = get_group_table_name();

	$rd_pwd = $wpdb->get_var("SELECT rd_pwd FROM $table_name WHERE id = $group_id");
	if ($rd_pwd_need_check == $rd_pwd) {
		return true;
	}else{
		return false;
	};
};

//------------------------------------------------------------------------------------------
// 查询群随机密码、接口暂时不开放
// api链接：url: 'localhost/?rest_route=/gongyi/i_check_rd_pwd_by_group_id',使用方法是get

// function i_check_rd_pwd_by_group_id(){
// 	$table_name = get_group_table_name();
// 	$group_id = get_group_id();
// 	$rd_pwd = i_GET(null, 'rd_pwd');

// 	if(check_rd_pwd_by_group_id($group_id, $rd_pwd)){
// 		return update_response_msg('get_200_response','密码正确');
// 	}else{
// 		return update_response_msg('get_404_error','密码错误');
// 	}
// }

// function i_rest_route_check_rd_pwd_by_group_id(){
// 	register_rest_route( 'gongyi/', 'i_check_rd_pwd_by_group_id', [
// 		'methods' => 'get',
// 		'callback' => i_check_rd_pwd_by_group_id
// 	]);
// }
// add_action("rest_api_init",i_rest_route_check_rd_pwd_by_group_id);

//------------------------------------------------------------------------------------------
// 增改群员分摊权重


//------------------------------------------------------------------------------------------
// 更改群简介


//------------------------------------------------------------------------------------------
// 更改群名


//------------------------------------------------------------------------------------------
// 进行数学计算

