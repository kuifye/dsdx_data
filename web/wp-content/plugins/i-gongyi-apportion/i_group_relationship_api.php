<?php

//------------------------------------------------------------------------------------------
// 捆绑一组item到cost

function bind_items_to_costs($wpdb,$item_price,$data,$table_name){
	//原材料价格
	$item_price = array_column($item_price, null, 'id');
	//定义一个数组用于存放包含了所有需要更新字段的成品数据
	$res = array();
	foreach ($data as $value){
		//转换类型，防止出现obj的类型报错的情况
		if(gettype($value) == "object"){
			$value = get_object_vars($value);
		}
		//获取参数
		$id = $value['item_id'];
		$costs_id = $value['costs_id'];
		$quantity = intval ($value['quantity']);
		
		//计算价格，检查是否存在该字段
		if(array_key_exists($costs_id,$res)){
			//如果已经存在，直接累加
			$res[$costs_id]['reimbursement'] += intval (get_object_vars($item_price[$id])['reimbursement']) * $quantity;
			$res[$costs_id]['price'] += intval (get_object_vars($item_price[$id])['price']) * $quantity;
			$res[$costs_id]['expenses_weight'] += intval (get_object_vars($item_price[$id])['expenses_weight']) * $quantity;
		}else{
			//如果不存在，则转换类型后建立该目录
			$res[$costs_id] = get_object_vars($item_price[$id]);
			$res[$costs_id]['id'] = $costs_id;
			$res[$costs_id]['reimbursement'] = intval ($res[$costs_id]['reimbursement']) * $quantity;
			$res[$costs_id]['price'] = intval ($res[$costs_id]['price']) * $quantity;
			$res[$costs_id]['expenses_weight'] = intval ($res[$costs_id]['expenses_weight']) * $quantity;
		}
	}
	

	//获得一个索引为成品id，而字典内容含有所有价格之和的整合字典
	$res = array_values($res);
	$sql = '';
	//更新所有的结果
	pury_custom_transaction_begin();
	foreach ($res as $value) {
		$id = $value["id"];
    $reimbursement = $value["reimbursement"];
    $price = $value["price"];
    $expenses_weight = $value["expenses_weight"];

		// 构建 SQL 更新语句
    $sql = "UPDATE $table_name SET price = $price, reimbursement = $reimbursement, expenses_weight = $expenses_weight WHERE id = $id;";
		$wpdb->query($sql);
	}

	return pury_custom_transaction_commit();
}

//------------------------------------------------------------------------------------------
// 增加一组item之间关系，一般用来设定原材料和成品之间的关系，并通过这些关系，重新计算成品价格
// 如果某一个条目的引用id (reference_id) 不是null，则被建立的关系中的costs_id不是条目id，而是条目所具有的引用id (reference_id)
// api链接：url: 'localhost/?rest_route=/gongyi/i_add_item_ids_to_costs_ids',使用方法是get
// &data=[{"item_id":36,"quantity":1,"costs_id":52,"description":""},{"item_id":41,"quantity":2,"costs_id":52,"description":"test"}]

function i_add_item_ids_to_costs_ids(){
	//获取参数并鉴权
	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$table_name = get_table_name();
	$table_relationship_name = get_group_relationship_table_name();
	$data = stripslashes(i_GET(null , 'data'));
	$user_id = get_current_user_id();
	if($data == null){
		return update_response_msg('get_422_error','错误：缺少原材料或成品货物的参数。');
	}
	$data= json_decode($data,true);


	//检查是否存在缺失项，如果有一项成品或原料item不存在，则鉴权失败
	$id_array = [];
	foreach ($data as $value){
		array_push($id_array,$value['item_id']);
		array_push($id_array,$value['costs_id']);
	}
	$id_array_unique = array_unique($id_array);
	$sql = $wpdb->prepare("SELECT id,reference_id FROM $table_name WHERE id IN (" . implode(',', $id_array_unique) . ")");
	$result = $wpdb->get_results($sql);
	if (count($result) != count($id_array_unique)) {
		return update_response_msg('get_404_error','错误：其中某些条目或货品不存在。');
	}

	//获取项目之间的引用关系
	$table_reference_id = array_column($result, 'reference_id', 'id');

	//建立关系表
	function add_item_ids_to_costs_ids($wpdb, $table_name, $table_relationship_name, $data, $user_id, $table_reference_id) {
		$query = "";
		pury_custom_transaction_begin();
		//该数组用来储存所有的子元素
		$item_ids = [];
		//遍历数据，并转换成mysql语句
		foreach ($data as $value){
			$item_id = $value['item_id'];
			$quantity = $value['quantity'];
			$costs_id = $value['costs_id'];
			$reference_id = $table_reference_id[$costs_id];
			if($reference_id!=null){
				$costs_id = $reference_id;
			}
			$description = $value['description'];
			$query = "INSERT INTO $table_relationship_name (`item_id`,`quantity`,`costs_id`,`description`,`user_id`) VALUES (".$item_id.",".$quantity.",".$costs_id.",'".$description."',".$user_id.")";
			$wpdb->query($query);
			array_push($item_ids,$item_id);
		}
		//提交语句，建立关系
		pury_custom_transaction_commit();
		//通过所有的子元素，查询并返回一个包含所有原材料价格的数组
		return look_up_price_by_item_ids($wpdb, $table_name, $item_ids);
	}
	//里面存储了原材料的价格
	$item_price = add_item_ids_to_costs_ids($wpdb, $table_name, $table_relationship_name, $data, $user_id, $table_reference_id);

	//更新货物表的价格
	bind_items_to_costs($wpdb,$item_price,$data,$table_name);

	if ($wpdb->last_error) {
		return get_404_error();
	}else{
		$msg_update = '开支关系建立成功';
		return update_response_msg('get_200_response',$msg_update);
	}
}
rest_route('i_add_item_ids_to_costs_ids', 'i_add_item_ids_to_costs_ids');

//------------------------------------------------------------------------------------------
// 通过costs_id删除一组item，注意！该方法会忽略引用id(reference_id)
// api链接：url: 'localhost/?rest_route=/gongyi/i_delete_items_by_costs_ids',使用方法是get
// &costs_ids=[0,1,2,3,5,99,231]

function i_delete_items_by_costs_ids(){
	global $wpdb;
	// if (is_user_logged_in() == false){
	// 	return get_401_error();
	// }
	$table_name = get_group_relationship_table_name();
	$costs_ids = stripslashes(i_GET(null , 'costs_ids'));
	$user_id = get_current_user_id();
	if($costs_ids == null){
		return update_response_msg('get_422_error','错误：没有获取到costs_ids参数。');
	}
	$costs_ids= json_decode($costs_ids,true);

	function delete_item_by_costs_ids($wpdb, $table_name, $costs_ids, $user_id) {
		$query = "";
		pury_custom_transaction_begin();
		foreach ($costs_ids as $value)
		{
			$query = "DELETE FROM $table_name WHERE costs_id = ".$value;
			$wpdb->query($query);
		}
		return pury_custom_transaction_commit();
	}

	delete_item_by_costs_ids($wpdb, $table_name, $costs_ids, $user_id);

	if ($wpdb->last_error) {
		return get_404_error();
	}else{
		$msg_update = '删除完成';
		return update_response_msg('get_200_response',$msg_update);
	}
}
rest_route('i_delete_items_by_costs_ids', 'i_delete_items_by_costs_ids');

//------------------------------------------------------------------------------------------
// 通过costs_id查找一组包含该id的数据，注意！该方法会忽略引用id(reference_id)
// api链接：url: 'localhost/?rest_route=/gongyi/i_look_up_items_by_costs_ids',使用方法是get
// &costs_ids=[0,1,2,3,5,99,231]

function i_look_up_items_by_costs_ids(){
	global $wpdb;
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$table_name = get_group_relationship_table_name();
	$costs_ids = stripslashes(i_GET(null , 'costs_ids'));
	$user_id = get_current_user_id();
	if($costs_ids == null){
		return update_response_msg('get_422_error','错误：没有获取到costs_ids参数。');
	}
	$costs_ids= json_decode($costs_ids,true);

	function look_up_items_by_costs_ids($wpdb, $table_name, $costs_ids, $user_id) {
		$query = "SELECT * FROM $table_name WHERE";
		$init_flag = false;
		foreach ($costs_ids as $value)
		{
			if(!$init_flag){
				$query = $query. " costs_id = ".$value;
				$init_flag = true;
			}else{
				$query = $query. " OR costs_id = ".$value;
			}
		}
		return $wpdb->get_results($query);
	}

	$sql = look_up_items_by_costs_ids($wpdb, $table_name, $costs_ids, $user_id);

	if ($sql) {
		$msg_update = '查询完成';
		$res = update_response_msg('get_200_response',$msg_update);
		$res['data'] = $sql;
		return $res;
	}else{
		return get_404_error();
	}
}
rest_route('i_look_up_items_by_costs_ids', 'i_look_up_items_by_costs_ids');

//------------------------------------------------------------------------------------------
// 更新一组数据
// api链接：url: 'localhost/?rest_route=/gongyi/i_update_items_by_costs_ids',使用方法是get
// &costs_ids=[52,41]

function i_update_items_by_costs_ids(){
	//初始化，获取参数，并进行类型转换
	global $wpdb;
	// if (is_user_logged_in() == false){
	// 	return get_401_error();
	// }
	$table_name = get_table_name();
	$table_relationship_name = get_group_relationship_table_name();
	$costs_ids = stripslashes(i_GET(null , 'costs_ids'));
	$user_id = get_current_user_id();
	if($costs_ids == null){
		return update_response_msg('get_422_error','错误：没有获取到costs_ids参数。');
	}
	$costs_ids= json_decode($costs_ids,true);

	//查找引用关系
	$id_array_unique = array_unique($costs_ids);
	$sql = $wpdb->prepare("SELECT id,reference_id FROM $table_name WHERE id IN (" . implode(',', $id_array_unique) . ")");
	$result = $wpdb->get_results($sql);
	$table_reference_id = array_column($result, 'reference_id', 'id');
	$table_reverse_reference_id = array_column($result, 'id', 'reference_id');

	//查找所有带有成品的关系表
	function look_up_items_by_costs_ids($wpdb, $table_relationship_name, $costs_ids, $user_id, $table_reference_id) {
		$query = "SELECT * FROM $table_relationship_name WHERE";
		$init_flag = false;
		foreach ($costs_ids as $value)
		{
			$id = $value;
			if($table_reference_id[$id]!= null){
				$id = $table_reference_id[$id];
			}
			if(!$init_flag){
				$query = $query. " costs_id = ".$id;
				$init_flag = true;
			}else{
				$query = $query. " OR costs_id = ".$id;
			}
		}
		return $wpdb->get_results($query);
	}
	//获取到所有成品的关系键值对
	$data = look_up_items_by_costs_ids($wpdb, $table_relationship_name, $costs_ids, $user_id, $table_reference_id);

	//检查是否存在缺失项，如果有一项成品或原料item不存在，则鉴权失败
	$id_array = $costs_ids;
	foreach ($data as $value){
		$value = get_object_vars($value);
		array_push($id_array,$value['item_id']);
		array_push($id_array,$value['costs_id']);
	}
	$id_array_unique = array_unique($id_array);
	$sql = $wpdb->prepare("SELECT id FROM $table_name WHERE id IN (" . implode(',', $id_array_unique) . ")");
	$result = $wpdb->get_results($sql);
	if (count($result) != count($id_array_unique)) {
		return update_response_msg('get_404_error','错误：其中某些条目或货品不存在。');
	}

	//把所有的原料id转换成一个数组
	$item_ids = [];
	foreach ($data as $value){
		$value = get_object_vars($value);
		array_push($item_ids,$value['item_id']);
	}
	//查找所有的原料的价格
	$item_price = look_up_price_by_item_ids($wpdb, $table_name, $item_ids);

	//把查找到的数据中被引用的costs_id转换成原id，以便正确更新价格，而不会错误地更新被引用的reference_id的价格。
	$data_res = [];
	foreach ($data as $value){
		$value = get_object_vars($value);
		$tempt_costs_id = $value['costs_id'];
		if(array_key_exists($tempt_costs_id,$table_reverse_reference_id)){
			$value['costs_id'] = $table_reverse_reference_id[$tempt_costs_id];
		}
		array_push($data_res,$value);
	}
	
	//重新计算成品的价格并更新
	bind_items_to_costs($wpdb,$item_price,$data_res,$table_name);
	

	if ($wpdb->last_error) {
		return get_404_error();
	}else{
		$msg_update = '更新完成';
		return update_response_msg('get_200_response',$msg_update);
	}
}
rest_route('i_update_items_by_costs_ids', 'i_update_items_by_costs_ids');

//------------------------------------------------------------------------------------------
// 通过某个item_id来更新所有costs_id在i_apportion表内的账单价格
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_all_info_item_by_group_id',使用方法是get

