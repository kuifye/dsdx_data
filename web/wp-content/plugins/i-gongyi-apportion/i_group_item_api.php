<?php

// 如果用户需要对货物开支等数据库进行操作，可以写在这里

//------------------------------------------------------------------------------------------
// 查询某个货物的id归属

function get_user_id_by_item_id($item_id){
	global $wpdb;
	$table_name = get_table_name();
	$query = "SELECT user_id FROM $table_name WHERE id = $item_id LIMIT 0, 1";
	$res = $wpdb->get_var($query);
	return $res;
}

//------------------------------------------------------------------------------------------
// 通过item_ids查询某批货物的价格

function look_up_price_by_item_ids($wpdb, $table_name, $item_ids) {
	$query = "SELECT * FROM $table_name WHERE";
	$init_flag = false;
	foreach ($item_ids as $value)
	{
		if(!$init_flag){
			$query = $query. " id = ".$value;
			$init_flag = true;
		}else{
			$query = $query. " OR id = ".$value;
		}
	}
	return $wpdb->get_results($query);
}

//------------------------------------------------------------------------------------------
// api链接：url: 'localhost/?rest_route=/gongyi/i_look_up_price_by_item_ids',使用方法是get
// 通过item_ids查询某批货物的价格

function i_look_up_price_by_item_ids(){
	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$table_name = get_table_name();
	$item_ids = stripslashes(i_GET(null , 'item_ids'));
	if($item_ids == null){
		return update_response_msg('get_422_error','错误：没有获取到item_ids参数。');
	}
	$item_ids= json_decode($item_ids,true);

	$sql = look_up_price_by_item_ids($wpdb, $table_name, $item_ids);

	if ($sql) {
		$msg_update = '查询完成';
		$res = update_response_msg('get_200_response',$msg_update);
		$res['data'] = $sql;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_look_up_price_by_item_ids', 'i_look_up_price_by_item_ids');


//------------------------------------------------------------------------------------------
// 查询某货物id是否归属某用户，是则返回true，否则返回false
// 未经debug，需要测试！！！

function get_user_id_to_which_item_id_belongs($item_id, $user_id){
	global $wpdb;
	$table_name = get_table_name();
	$query = "SELECT user_id FROM $table_name WHERE id = $item_id LIMIT 0, 1";
	$res = $wpdb->get_row($query, ARRAY_A);
	if ( $res['user_id'] == $user_id ){
		return true;
	}else{
		return false;
	}
}

//------------------------------------------------------------------------------------------
// 以货物id为条件格式，增加归属群id

function add_item_costs_to_group_by_id($item_id, $group_id){
	global $wpdb;
	$table_name = get_table_name();
	function update_item_costs_to_group_by_id($wpdb, $table_name, $item_id, $group_id) {
		return $wpdb->update("$table_name", array( 'group_id' => $group_id), array('id' => $item_id), array( '%d'), array( '%d'));
	}
	$sql = update_item_costs_to_group_by_id($wpdb, $table_name, $item_id, $group_id);
	return $sql;
}

//------------------------------------------------------------------------------------------
// 通过群id及货物id，增加群的开销、支出
// api链接：url: 'localhost/?rest_route=/gongyi/i_add_item_costs_to_group_by_id',使用方法是post

function i_add_item_costs_to_group_by_id(){
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$user_id = get_current_user_id();
	$item_id = $_POST['item_id'];
	$group_id = $_POST['group_id'];
	//查询该群组是否被删除
	if(get_is_delete_by_group_id($group_id)){
		return get_4014_error();
	}
	if (!get_user_id_to_which_item_id_belongs($item_id, $user_id)){
		return get_4020_error();
	}
	$sql = add_item_costs_to_group_by_id($item_id, $group_id);

	if ($sql) {
		return update_response_msg('get_200_response','更改成功');
	}else{
		return update_response_msg('get_404_error','更改失败');
	}
}
rest_route('i_add_item_costs_to_group_by_id', 'i_add_item_costs_to_group_by_id', 'post');

//------------------------------------------------------------------------------------------
// 查询某个群的所有货物总条数

function get_count_item_by_group_id($group_id){
	global $wpdb;
	$table_name = get_table_name();
	$query = "SELECT COUNT(*) FROM $table_name WHERE group_id = $group_id";
	$sql = $wpdb->get_var($query);
	return $sql;
}

//------------------------------------------------------------------------------------------
// 查询某个群的所有货物

function get_all_info_item_by_group_id($group_id, $page_number, $page_size) {
	global $wpdb;
	$table_name = get_table_name();
	$table_req_name	= get_group_req_table_name();
	$table_users_name = get_users_table_name();
	$query = <<<STR
	SELECT $table_req_name.group_id, $table_req_name.user_id, $table_req_name.user_lever, $table_req_name.expenses_weight, $table_name.*, $table_users_name.user_login, $table_users_name.user_nicename, $table_users_name.display_name
	FROM $table_req_name
	INNER JOIN $table_name ON $table_name.user_id = $table_req_name.user_id
	INNER JOIN $table_users_name ON $table_name.user_id = $table_users_name.id
	WHERE $table_name.group_id = $group_id AND $table_req_name.group_id = $group_id
	LIMIT $page_number, $page_size;
	STR;
	return $wpdb->get_results($query);
}

//------------------------------------------------------------------------------------------
// 查询某个群的所有货物
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_all_info_item_by_group_id',使用方法是get

function i_get_all_info_item_by_group_id(){
	global $wpdb;
	$group_id = get_group_id();
	$page_number = get_page_number();
	$page_size = get_page_size();
	$count = get_count_item_by_group_id($group_id);
	if($page_size == null){
		$page_size = $count;
	}

	$sql = get_all_info_item_by_group_id($group_id, $page_number, $page_size);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		$res['count'] = $count;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_all_info_item_by_group_id', 'i_get_all_info_item_by_group_id');

//------------------------------------------------------------------------------------------
// 查询某个群货物的所有开支、对应的人、同其分摊权重
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_all_info_item_by_group_id_without_page',使用方法是get

function i_get_all_info_item_by_group_id_without_page(){
	global $wpdb;
	$group_id = get_group_id();
	$page_number = get_page_number();
	$page_size = get_page_size();
	$count = 0;
	$page_size = get_count_item_by_group_id($group_id);

	$sql = get_all_info_item_by_group_id($group_id, $page_number, $page_size);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		$res['count'] = $count;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_all_info_item_by_group_id_without_page', 'i_get_all_info_item_by_group_id_without_page');

//------------------------------------------------------------------------------------------
// 通过开支编号，也就是货物id，删除一条开销或者说货物的记录
// url: 'localhost/?rest_route=/gongyi/catering/i_delete_item_by_id',

function i_delete_item_by_id(){
	global $wpdb;
	$table_name = get_table_name();
	if (isset($_GET['item_id']) && !empty($_GET['item_id'])) {
		$item_id = $_GET['item_id'];
	}else{
		return get_422_error();
	}
	$user_id = get_current_user_id();
	$group_id = get_group_id();
	if(!get_user_id_in_group_is_admin($user_id ,$group_id)){
		if (!get_user_id_to_which_item_id_belongs($item_id, $user_id)) {
			return update_response_msg('get_4024_error','错误代码4024：你无权删除该记录。');
		}
	}

	function delete_item_by_id($wpdb, $table_name, $item_id, $user_id) {
		return $wpdb->delete($table_name, array('id' => $item_id, 'user_id' => $user_id), array( '%d', '%d'));
	}
	$sql = delete_item_by_id($wpdb, $table_name, $item_id, $user_id);

	if ($sql) {
		return update_response_msg('get_200_response','删除成功');
	}else{
		return update_response_msg('get_404_error','删除失败');
	}
}

//绑定路由
function i_rest_route_delete_item_by_id(){
	register_rest_route( 'gongyi/catering', '/i_delete_item_by_id', [
		'methods' => 'get',
		'callback' => i_delete_item_by_id
	]);
}
add_action("rest_api_init",'i_rest_route_delete_item_by_id');

//------------------------------------------------------------------------------------------
// 查询某条记录的父记录
// api: 'localhost/?rest_route=/gongyi/i_get_father_item_id_by_item_id'

rest_route_get_value_by_field_in_group('father_item_id', i_GET(0,'id'), 'id', get_table_name(),'i_get_father_item_id_by_item_id');

//------------------------------------------------------------------------------------------
// 更改开支项的父开支：预备用于开发开支模组，譬如：番茄炒蛋是番茄和蛋的父开支，方便模块化导入开支项

function set_father_item_id_by_item_id($table_name, $item_id, $father_item_id) {
	global $wpdb;
	return $wpdb->update("$table_name", array( 'father_item_id' => $father_item_id), array('id' => $item_id), array( '%d'), array( '%d'));
}

//------------------------------------------------------------------------------------------
// 更改父群组、需要先进行群组管理员验证、或者网站管理验证
// api链接：url: 'localhost/?rest_route=/gongyi/i_set_father_item_id_by_item_id',使用方法是Post

function i_set_father_item_id_by_item_id(){

	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$user_id = get_current_user_id();
	$table_name = get_group_table_name();
	$item_id = i_POST(null , 'item_id');
	$is_admin = get_user_id_in_item_is_admin($user_id, $group_id,4);

	if(!$is_admin){
		return get_4024_error();
	}

	$sql = set_father_item_id_by_item_id($table_name, $item_id, $father_item_id);

	if ($sql) {
		return update_response_msg('get_200_response','设置成功');
	}else{
		return update_response_msg('get_404_error','设置失败');
	}
}
rest_route('i_set_father_item_id_by_item_id', 'i_set_father_item_id_by_item_id','post');

//------------------------------------------------------------------------------------------
// 递归查询某条记录的子记录及其记录树
// api: 'localhost/?rest_route=/gongyi/i_get_child_item_id_by_item_id'

function i_get_child_item_id_by_item_id(){
	global $wpdb;
	$table_name = get_table_name();
	$item_id = i_GET(0,'id');
	$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE FIND_IN_SET(id,get_child_list('$item_id'))");
	if ($sql) {
		$res = get_200_response();
		$res['data'] = $wpdb->get_results( $sql );
		return $res;
	}else{
		return get_400_error();
	}
}
rest_route('i_get_child_item_id_by_item_id', 'i_get_child_item_id_by_item_id');

//------------------------------------------------------------------------------------------
// 递归查询某个群的货物记录、子记录及其记录树

function get_all_info_item_by_group_id_with_child_list($group_id) {
	global $wpdb;
	$table_name = get_table_name();
	$table_req_name	= get_group_req_table_name();
	$table_users_name = get_users_table_name();
	$query = <<<STR
	SELECT DISTINCT 
		$table_req_name.user_id,
		$table_req_name.user_lever,
		$table_req_name.expenses_weight,
		$table_name.*, 
		$table_users_name.user_login, 
		$table_users_name.user_nicename, 
		$table_users_name.display_name
	FROM $table_req_name
	LEFT JOIN $table_name ON $table_name.user_id = $table_req_name.user_id
	LEFT JOIN $table_users_name ON $table_req_name.user_id = $table_users_name.id
	WHERE FIND_IN_SET($table_name.id,get_all_item_info_by_group_id_with_child_list('$group_id')) AND $table_req_name.group_id = $group_id;
	STR;
	return $wpdb->get_results($query);
}

//------------------------------------------------------------------------------------------
// 递归查询某个群的货物记录、子记录及其记录树
// api: 'localhost/?rest_route=/gongyi/i_get_all_info_item_by_group_id_with_child_list'

function i_get_all_info_item_by_group_id_with_child_list(){
	global $wpdb;
	$table_name = get_table_name();
	$group_id = get_group_id();

	$sql = get_all_info_item_by_group_id_with_child_list($group_id);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		$res['count'] = $count;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_all_info_item_by_group_id_with_child_list', 'i_get_all_info_item_by_group_id_with_child_list');