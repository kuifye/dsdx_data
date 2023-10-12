<?php

//登记用接口，可用于增加item货物记录

//------------------------------------------------------------------------------------------
// 登记表，用于登记相关的信息
// url: 'localhost/?rest_route=/gongyi/catering/i_sign_up',

function i_sign_up(){
	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$group_id = i_GET(0 , 'group_id');
	//如果群组已经被删除，返回4014错误
	if(get_is_delete_by_group_id($group_id)){
		return get_4014_error();
	}
	//不能使用自定义的方法get_user_id()，因为只能自己给自己添加开支记录或签到，否则会有风险。
	$user_id = get_current_user_id();
	//如果用户未加入该群组，返回4020错误
	if(!is_in_group_by_user_id($user_id,$group_id)){
		$rd_pwd = i_GET(null , 'rd_pwd');
		if(check_rd_pwd_by_group_id($group_id, $rd_pwd)){
			$sql = $wpdb->insert(get_group_req_table_name(), array('group_id' => $group_id, 'user_id' => $user_id, 'user_lever' => 2, 'expenses_weight' => 100), array( '%d', '%d', '%d', '%d'));
			if (!$sql) {
				return get_404_error();
			}
		}else{
			return update_response_msg('get_4020_error','未加入该群组');
		}
	}
	$table_name = get_table_name();
	//获取其他参数
	$item_name = i_GET('日常饮食' , 'item_name');
	$description = i_GET('……' , 'description');
	$reimbursement = i_GET(0 , 'reimbursement');
	$price = i_GET(0 , 'price');
	$expenses_weight = i_GET(0 , 'expenses_weight');
	$father_item_id = i_GET(null , 'father_item_id');
	$reference_id = i_GET(null , 'reference_id');
	// $occurrence_time = i_GET(date('Y-m-d H:i:s') , 'occurrence_time');
	$occurrence_time = i_GET(null , 'occurrence_time');
	
	function insert_sign_up_by_item_name($wpdb, $table_name, $item_name, $price, $user_id, $description, $reimbursement, $expenses_weight, $group_id, $father_item_id, $reference_id, $occurrence_time){
		if($occurrence_time == null){
			return $wpdb->insert($table_name, array('user_id' => $user_id, 'item_name' => $item_name, 'group_id' => $group_id, 'price' => $price, 'description' =>  $description, 'reimbursement' => $reimbursement, 'expenses_weight' => $expenses_weight, 'father_item_id' => $father_item_id, 'reference_id' =>$reference_id));
		}else{
			return $wpdb->insert($table_name, array('user_id' => $user_id, 'item_name' => $item_name, 'group_id' => $group_id, 'price' => $price, 'description' =>  $description, 'reimbursement' => $reimbursement, 'expenses_weight' => $expenses_weight, 'father_item_id' => $father_item_id, 'reference_id' =>$reference_id, 'occurrence_time' => $occurrence_time));
		}
	}
	$sql = insert_sign_up_by_item_name($wpdb, $table_name, $item_name, $price, $user_id, $description, $reimbursement, $expenses_weight, $group_id, $father_item_id, $reference_id, $occurrence_time);

	if ($sql) {
		// dbDelta($sql);
		$order_id = $wpdb->insert_id;
		$res = update_response_msg('get_200_response','签到成功');
		$res['data'] = $order_id;
		return $res;
	} else {
		return get_400_error();
	}
}
function i_rest_route_sign_up(){	//签到并通过get请求返回签到数据
	register_rest_route( 'gongyi/catering', '/i_sign_up', [
		'methods' => 'get',
		'callback' => i_sign_up
	]);
}
add_action("rest_api_init",'i_rest_route_sign_up');

//------------------------------------------------------------------------------------------
// 引用一条记录
// api链接：url: 'localhost/?rest_route=/gongyi/i_sign_up_by_reference_id',使用方法是get

function i_sign_up_by_reference_id(){
	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$group_id = i_GET(0 , 'group_id');
	//如果群组已经被删除，返回4014错误
	if(get_is_delete_by_group_id($group_id)){
		return get_4014_error();
	}
	//不能使用自定义的方法get_user_id()，因为只能自己给自己添加开支记录或签到，否则会有风险。
	$user_id = get_current_user_id();
	//如果用户未加入该群组，返回4020错误
	if(!is_in_group_by_user_id($user_id,$group_id)){
		$rd_pwd = i_GET(null , 'rd_pwd');
		if(check_rd_pwd_by_group_id($group_id, $rd_pwd)){
			$sql = $wpdb->insert(get_group_req_table_name(), array('group_id' => $group_id, 'user_id' => $user_id, 'user_lever' => 2, 'expenses_weight' => 100), array( '%d', '%d', '%d', '%d'));
			if (!$sql) {
				return get_404_error();
			}
		}else{
			return update_response_msg('get_4020_error','未加入该群组');
		}
	}
	$table_name = get_table_name();
	//获取其他参数
	$reference_id = i_GET(null , 'reference_id');
	//查询被引用的记录
	$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = ".$reference_id);
	$result_table = $wpdb->get_results($sql);
	$result_table = get_object_vars($result_table[0]);
	//获取被引用的记录的数据
	$item_name = $result_table['item_name'];
	$description = $result_table['description'];
	$reimbursement = $result_table['reimbursement'];
	$price = $result_table['price'];
	$expenses_weight = $result_table['expenses_weight'];
	$father_item_id = $result_table['father_item_id'];
	
	//插入该条目
	function insert_sign_up_by_item_name($wpdb, $table_name, $item_name, $price, $user_id, $description, $reimbursement, $expenses_weight, $group_id, $father_item_id, $reference_id){
		return $wpdb->insert($table_name, array('user_id' => $user_id, 'item_name' => $item_name, 'group_id' => $group_id, 'price' => $price, 'description' =>  $description, 'reimbursement' => $reimbursement, 'expenses_weight' => $expenses_weight, 'father_item_id' => $father_item_id, 'reference_id' =>$reference_id), array( '%d', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d'));
	}
	$sql = insert_sign_up_by_item_name($wpdb, $table_name, $item_name, $price, $user_id, $description, $reimbursement, $expenses_weight, $group_id, $father_item_id, $reference_id);

	if ($sql) {
		// dbDelta($sql);
		$order_id = $wpdb->insert_id;
		$res = update_response_msg('get_200_response','成功新建条目');
		$result_table['id'] = $order_id;
		$res['data'] = $result_table;
		return $res;
	} else {
		return get_400_error();
	}
}
rest_route('i_sign_up_by_reference_id', 'i_sign_up_by_reference_id');

//------------------------------------------------------------------------------------------
// 查询登记的次数，待优化项：可以先存在cookie里面，除非出现网络问题再再次查询，否则会吃很多性能。
// url: 'localhost/?rest_route=/gongyi/catering/i_count',

function i_count(){
	global $wpdb;

	$user_id = get_user_id();

	$table_name = get_table_name();
	function get_sign_up_count_by_item_name($wpdb, $table_name, $user_id, $item_name) {
		return $wpdb -> prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = $user_id AND item_name = '$item_name'");
	}
	
	$item_name = '日常饮食';
	if (isset($_GET['item_name']) && !empty($_GET['item_name'])) {
		$item_name = $_GET['item_name'];
	}

	$sql = get_sign_up_count_by_item_name($wpdb, $table_name, $user_id, $item_name);

	if ($sql) {
		$res = get_200_response();
		$res['data'] = $wpdb->get_var( $sql );
		return $res;
	}else{
		return get_400_error();
	}
}
function i_rest_route_count(){
	//统计当前用户累计吃了多少次饭
	register_rest_route( 'gongyi/catering', '/i_count', [
		'methods' => 'get',
		'callback' => i_count
	]);
}
add_action("rest_api_init",'i_rest_route_count');
