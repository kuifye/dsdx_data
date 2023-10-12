<?php

//如果用户需要对req数据库进行操作，可以写在这里

//------------------------------------------------------------------------------------------
// 鉴权方法

function join_group_authentication($group_id){
	$state = get_state_by_group_id($group_id);
	if(get_is_delete_by_group_id($group_id)){
		return get_4014_error();
	}elseif($state == 1){
		return get_4031_error();
	}else{
		return true;
	}
}

//------------------------------------------------------------------------------------------
// 申请入群、如果没有身份先设置身份（目前设置成了普通群员）。
// 目前还有一个bug:需要对是否存在该群进行数据验证。
// 其中user_lever指的是用户的权限：0等待验证状态（没消息，不能发言），1可以收到消息不能发言，2普通群员
// user_lever：3暂时处于空余状态，4管理员，5可以进行群注销等超级管理员
// api链接：url: 'localhost/?rest_route=/gongyi/i_join_group',使用方法是get

function i_join_group(){
	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$user_id = get_current_user_id();
	$table_name = get_group_req_table_name();
	$group_id = get_group_id();
	$rd_pwd = i_GET(null , 'rd_pwd');

	//进行随即密码核验，如果密码正确，直接赋予用户2权限（普通群员），如果密码错误，用户权限为0（等待验证状态）
	if (check_rd_pwd_by_group_id($group_id, $rd_pwd)){
		$user_lever = 2;
		$msg_update = '进群成功';
	}else{
		$user_lever = 0;
		$msg_update = '你已经提交了申请，等待管理员审核中……';
	}
	//get_identity_weight方法获取权重，未设置身份的话，返回4011错误
	$identity = get_user_option( 'identity', $user_id );
	$expenses_weight = get_identity_weight($identity);
	if(!$expenses_weight){
		return get_4011_error();
	}
	//查询是否存在该群组
	if(!get_group_exist($group_id)){
		return update_response_msg('get_422_error','错误：该群组不存在。');
	}
	$join_group_authentication_return = join_group_authentication($group_id);
	if($join_group_authentication_return != true){
		return $join_group_authentication_return;
	}

	//插入入群申请的数据
	function insert_join_group_data($wpdb, $table_name ,$group_id ,$user_id ,$user_lever ,$expenses_weight){
		return $wpdb->insert($table_name, array('group_id' => $group_id, 'user_id' => $user_id, 'user_lever' => $user_lever, 'expenses_weight' => $expenses_weight), array( '%d', '%d', '%d', '%f'));
	}
	$sql = insert_join_group_data($wpdb, $table_name ,$group_id ,$user_id ,$user_lever ,$expenses_weight);

	if ($sql) {
		return update_response_msg('get_200_response',$msg_update);
	}else{
		if($wpdb->last_error == "Duplicate entry '$group_id-$user_id' for key 'wp_i_uc_group_user_unique'"){
			return update_response_msg('get_409_error','错误：你重复提交了申请，或已在账本中。');
		}else{
			return get_404_error();
		}
	}
	
}
rest_route('i_join_group', 'i_join_group');

//------------------------------------------------------------------------------------------
//查询某id在某个群组内是否大于某个值，例如大于4是管理员（如有其他需要，请填第三参数），如果大于该值，返回true，否则返回false

function get_user_id_in_group_is_admin($user_id ,$group_id ,$user_lever = 4){
	$res = get_user_lever_by_group_id($user_id,$group_id);
	if ( $res >= $user_lever ){
		return true;
	}else{
		return false;
	}
}

//------------------------------------------------------------------------------------------
// 查询某个群里面user_lever用户等级

function get_user_lever_by_group_id($user_id,$group_id){
	global $wpdb;
	$req_table_name = get_group_req_table_name();
	$query = "SELECT user_lever FROM $req_table_name WHERE user_id = $user_id AND group_id = $group_id LIMIT 0, 1";
	$res = $wpdb->get_row($query, ARRAY_A);        //默认返回对象
	return $res['user_lever'];
}

//------------------------------------------------------------------------------------------
//查询当前id在某个群组内是否为管理员
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_user_id_in_group_is_admin',使用方法是Get

function i_get_user_id_in_group_is_admin(){
	$group_id = 0;
	if (isset($_GET['group_id']) && !empty($_GET['group_id'])) {
		$group_id = $_GET['group_id'];
	}
	return get_user_id_in_group_is_admin(get_current_user_id(),$group_id);
}
rest_route('i_get_user_id_in_group_is_admin', 'i_get_user_id_in_group_is_admin');

//------------------------------------------------------------------------------------------
// 查询某个id加入群组的条数

function get_count_group_by_group_id($group_id){
	global $wpdb;
	$table_name = get_group_req_table_name();
	$query = "SELECT COUNT(*) FROM $table_name WHERE group_id = $group_id";
	$sql = $wpdb->get_var($query);
	return $sql;
}

//------------------------------------------------------------------------------------------
// 查询某个id加入的群组
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_group_by_user_id',使用方法是Get

function i_get_group_by_user_id(){

	global $wpdb;
	$table_name = get_group_req_table_name();
	$user_id =get_user_id();
	$page_number = get_page_number();
	$page_size = get_page_size();
	$count = get_count_group_by_group_id($group_id);
	if($page_size == null){
		$page_size = $count;
	}

	//目前数据量较小，如果数据量大的话，可以考虑使用分页
	function get_group_by_user_id($wpdb, $table_name, $user_id, $page_number, $page_size) {
		return $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id = $user_id AND user_lever > 0 ORDER BY id LIMIT $page_number, $page_size" );
	}
	$sql = get_group_by_user_id($wpdb, $table_name, $user_id, $page_number, $page_size);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		$res['count'] = $count;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_group_by_user_id', 'i_get_group_by_user_id');

//------------------------------------------------------------------------------------------
// 查询某个群组的所有成员
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_all_user_info_by_group_id',使用方法是Get
// 为了方便前端计算，暂时不使用后端分页，如有需要，在查询语句最后一行加上：LIMIT $page_number, $page_size;

function i_get_all_user_info_by_group_id(){
	global $wpdb;
	$table_name = get_group_req_table_name();
	$table_users_name = get_users_table_name();
	$table_usermeta_name = get_usermeta_table_name();
	$group_id = get_group_id();
	$page_number = get_page_number();
	$page_size = get_page_size();

	function get_all_user_info_by_group_id($wpdb, $table_name, $table_users_name, $table_usermeta_name, $group_id, $page_number, $page_size) {
		$query = <<<STR
		SELECT $table_name.*, $table_users_name.id, $table_users_name.user_login, $table_users_name.user_nicename, $table_users_name.display_name, $table_usermeta_name.meta_value
		FROM $table_name
		INNER JOIN $table_users_name ON $table_name.user_id = $table_users_name.id
		INNER JOIN $table_usermeta_name ON $table_name.user_id = $table_usermeta_name.user_id
		WHERE $table_name.group_id = $group_id AND $table_usermeta_name.meta_key = 'identity'
		STR;
		return $wpdb->get_results($query);
	}
	$sql = get_all_user_info_by_group_id($wpdb, $table_name, $table_users_name, $table_usermeta_name, $group_id, $page_number, $page_size);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $sql;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_get_all_user_info_by_group_id', 'i_get_all_user_info_by_group_id');

//------------------------------------------------------------------------------------------
// 查询某个id是否加入了某个群组

function is_in_group_by_user_id($user_id ,$group_id){
	global $wpdb;
	$req_table_name = get_group_req_table_name();
	$query = "SELECT 1 FROM $req_table_name WHERE user_id = $user_id AND group_id = $group_id LIMIT 0 ,1";
	$res = $wpdb->get_var($query);        //默认返回对象
	if ( $res > 0 ){
		return true;
	}else{
		return false;
	}
}

//------------------------------------------------------------------------------------------
// 查询某个id是否加入了某个群组
// api链接：url: 'localhost/?rest_route=/gongyi/i_is_in_group_by_user_id',使用方法是Get

function i_is_in_group_by_user_id(){
	$group_id = get_group_id();
	$user_id = get_user_id();
	if(is_in_group_by_user_id($user_id,$group_id)){
		return update_response_msg('get_200_response','已加入该群组');
	}else{
		return update_response_msg('get_4020_error','未加入该群组');
	}
}
rest_route('i_is_in_group_by_user_id', 'i_is_in_group_by_user_id');

//------------------------------------------------------------------------------------------
// 设置管理员（user_lever参数默认为4，即管理员）

function set_admin_by_user_id($user_id ,$group_id, $user_lever = 4){
	global $wpdb;
	$req_table_name = get_group_req_table_name();
	$query = "UPDATE $req_table_name SET user_lever = $user_lever WHERE user_id = $user_id AND group_id = $group_id";
	$res = $wpdb->query($query);        //默认返回对象
	if ( $res ){
		return true;
	}else{
		return false;
	}
}

//------------------------------------------------------------------------------------------
// 设置管理员(只有user_lever>5才能给管理)
// api链接：url: 'localhost/?rest_route=/gongyi/i_set_admin_by_user_id',使用方法是Get

function i_set_admin_by_user_id(){
	$group_id = get_group_id();
	$user_id = get_user_id();
	$user_lever = get_user_lever_by_group_id($user_id,$group_id);
	if($user_lever>=4){
		return update_response_msg('get_409_error','错误代码409：对方已经是管理员了。');
	}
	if(!get_user_id_in_group_is_admin(get_current_user_id() ,$group_id ,5)){
		return update_response_msg('get_4025_error','错误代码4025：无权设置管理员。');
	}else{
		if(set_admin_by_user_id($user_id ,$group_id)){
			return update_response_msg('get_200_response','设置成功');
		}else{
			return update_response_msg('get_404_error','设置失败');
		}
	}
}
rest_route('i_set_admin_by_user_id', 'i_set_admin_by_user_id');

//------------------------------------------------------------------------------------------
// 移除管理员(只有user_lever>5才能移除管理)
// api链接：url: 'localhost/?rest_route=/gongyi/i_remove_admin_by_user_id',使用方法是Get

function i_remove_admin_by_user_id(){
	$group_id = get_group_id();
	$user_id = get_user_id();
	if($user_id==get_current_user_id()){
		return update_response_msg('get_409_error','错误代码409：不能移除自己的管理员权限。');
	}
	$user_lever = get_user_lever_by_group_id($user_id,$group_id);
	if($user_lever<4){
		return update_response_msg('get_409_error','错误代码409：对方不是管理员。');
	}
	if(!get_user_id_in_group_is_admin(get_current_user_id() ,$group_id ,5)){
		return update_response_msg('get_4025_error','错误代码4025：无权移除管理员。');
	}else{
		if(set_admin_by_user_id($user_id ,$group_id,2)){
			return update_response_msg('get_200_response','移除成功');
		}else{
			return update_response_msg('get_404_error','移除失败');
		}
	}
}
rest_route('i_remove_admin_by_user_id', 'i_remove_admin_by_user_id');

//------------------------------------------------------------------------------------------
// 同意入群申请
// api链接：url: 'localhost/?rest_route=/gongyi/i_agree_join_by_user_id',使用方法是get

function i_agree_join_by_user_id(){
	$group_id = get_group_id();
	$user_id = get_user_id();
	$user_lever = get_user_lever_by_group_id($user_id,$group_id);
	if($user_lever>0){
		return update_response_msg('get_409_error','错误代码409：对方已经是群员了。');
	}
	if(!get_user_id_in_group_is_admin(get_current_user_id() ,$group_id ,5)){
		return update_response_msg('get_4025_error','错误代码4025：无权同意入群。');
	}else{
		if(set_admin_by_user_id($user_id ,$group_id,2)){
			return update_response_msg('get_200_response','同意成功');
		}else{
			return update_response_msg('get_404_error','同意失败');
		}
	}
}
rest_route('i_agree_join_by_user_id', 'i_agree_join_by_user_id');

//------------------------------------------------------------------------------------------
// 删除入群记录

function delete_req_by_user_id($user_id ,$group_id){
	global $wpdb;
	$req_table_name = get_group_req_table_name();
	$query = "DELETE FROM $req_table_name WHERE user_id = $user_id AND group_id = $group_id";
	$res = $wpdb->query($query);
	if ( $res ){
		return true;
	}else{
		return false;
	}
}

//------------------------------------------------------------------------------------------
// 拒绝入群申请
// api链接：url: 'localhost/?rest_route=/gongyi/i_refuse_join_by_user_id',使用方法是get

function i_refuse_join_by_user_id(){
	$group_id = get_group_id();
	$user_id = get_user_id();
	$user_lever = get_user_lever_by_group_id($user_id,$group_id);
	if($user_lever>0){
		return update_response_msg('get_409_error','错误代码409：对方已经是群员了。');
	}
	if(!get_user_id_in_group_is_admin(get_current_user_id() ,$group_id ,5)){
		return update_response_msg('get_4025_error','错误代码4025：无权拒绝入群。');
	}else{
		if(delete_req_by_user_id($user_id ,$group_id)){
			return update_response_msg('get_200_response','拒绝成功');
		}else{
			return update_response_msg('get_404_error','拒绝失败');
		}
	}
}
rest_route('i_refuse_join_by_user_id', 'i_refuse_join_by_user_id');

//------------------------------------------------------------------------------------------
// 退出群组
