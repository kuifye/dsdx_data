<?php

//------------------------------------------------------------------------------------------
//增加身份选项

function i_my_profile( $contactmethods ) {
	$contactmethods['identity'] = '身份';
	return $contactmethods;
}
add_filter('user_contactmethods','i_my_profile');

//------------------------------------------------------------------------------------------
//获取身份权重，工人是1，学生0.5，其他情况返回false

function get_identity_weight($identity = '工作'){
	if ($identity == '工人') {
		return 100;
	} else if ($identity == '学生') {
		return 50;
	} else if ($identity == '失业') {
		return 50;
	} else if ($identity == '工作') {
		return 100;
	} else {
		return false;
	}
}

//------------------------------------------------------------------------------------------
// 设置身份选项
// api链接：url: 'localhost/?rest_route=/gongyi/i_set_identity_is_labor_or_student',

function i_set_identity_is_labor_or_student($identity = '工人'){

	$user_id = get_current_user_id();
	if (is_user_logged_in() == false){
		return get_401_error();
	}
	$option_name = 'identity';
	if (isset($_GET['identity']) && !empty($_GET['identity'])) {
		$identity = $_GET['identity'];
	}
	update_user_option( $user_id, $option_name, $identity, true );
	return update_response_msg('get_200_response','设置成功');
}

function i_rest_route_set_identity_is_labor_or_student(){	//签到并通过get请求返回签到数据
	register_rest_route( 'gongyi/', 'i_set_identity_is_labor_or_student', [
		'methods' => 'get',
		'callback' => i_set_identity_is_labor_or_student
	]);
}
add_action("rest_api_init",'i_rest_route_set_identity_is_labor_or_student');

//------------------------------------------------------------------------------------------
// 返回身份信息function,被api所调用

function get_info_by_user_id($user_id){
	$the_user = get_user_by( 'id', $user_id );
	$user_name = $the_user->display_name;
	$identity = get_user_option( 'identity', $user_id );
	$user_nicename = $the_user->user_nicename;
	$user_status = $the_user->user_status;
	$user_registered = $the_user->user_registered;

	return [
		'id' => $user_id,
		'user_name' => $user_name,
		'identity' => $identity,
		'user_nicename' => $user_nicename,
		'user_status' => $user_status,
		'user_registered' => $user_registered,
	];
}

//------------------------------------------------------------------------------------------
// 返回身份信息
// api链接：url: 'localhost/?rest_route=/gongyi/i_get_info_by_user_id'
// 可以用GET方法查询指定用户'&user_id=3'

function i_get_info_by_user_id(){
	if (isset($_GET['user_id']) && !empty($_GET['user_id']) && $_GET['user_id'] != null) {
		$user_id = $_GET['user_id'];
	}else{
		$user_id = get_current_user_id();
	}
	return get_info_by_user_id($user_id);
}

function i_rest_route_get_info_by_user_id(){	//签到并通过get请求返回签到数据
	register_rest_route( 'gongyi/', 'i_get_info_by_user_id', [
		'methods' => 'get',
		'callback' => i_get_info_by_user_id
	]);
}
add_action("rest_api_init",'i_rest_route_get_info_by_user_id');

//------------------------------------------------------------------------------------------
// 登陆api
// api链接：url: 'localhost/?rest_route=/gongyi/i_login',方法是post
// 向该api发送请求以登陆该站点

function i_login(){
	global $wpdb,$user_ID;
	if (is_user_logged_in() == true){
		return update_response_msg('get_401_error','已经登陆了');
		// echo "<script type='text/javascript'>window.location='". get_bloginfo('url') ."'</script>";
	}else{
		//从post中获取用户信息
		$username = $wpdb->escape(i_POST(null , 'username'));
		$password = $wpdb->escape(i_POST(null , 'password'));
		$remember = $wpdb->escape(i_POST(null , 'rememberme'));

		//通过检查手机号码返回用户的id信息
		function get_username_by_phone_number($wpdb, $username){
			$table_usermeta_name = get_usermeta_table_name();
			$query = "SELECT user_id FROM $table_usermeta_name WHERE meta_value = $username AND meta_key = 'phone'";
			$res = $wpdb->get_var($query);
			return $res;
		}

		//判断是不是手机号
		if(preg_match("/^1[3456789]{1}\d{9}$/",$username)){
			$user_id = get_username_by_phone_number($wpdb, $username);
			if($user_id!=null){
				$username = get_userdata($user_id)->user_login;
			}else{
				return update_response_msg('get_404_error','不存在该手机号码!');
			}
		}

		//是否记住密码
		if($remember){
			$remember = "true";
		}else{
			$remember = "false";
		}

		//整理成数组的形式
		$login_data = array();
		$login_data['user_login'] = $username;
		$login_data['user_password'] = $password;
		$login_data['remember'] = $remember;
		//wp_signon 是wordpress自带的函数，通过用户信息来授权用户(登陆)，可记住用户名
		$user_verify = wp_signon( $login_data, false );
	
		//检查是否登陆了
		if ( is_wp_error($user_verify) ) {
			//不管啥错误都输出这个信息
			return update_response_msg('get_404_error','用户名或密码错误，请重试!');
		}else{ 
			//登陆成功则跳转到首页(ajax提交所以需要用js来跳转)
			return update_response_msg('get_200_response','登录成功');
		}
	}
}
rest_route('i_login', 'i_login', 'post');

//------------------------------------------------------------------------------------------
// 更改手机号
// api链接：url: 'localhost/?rest_route=/gongyi/i_change_phone',方法是post
// 向该api发送请求以更改手机号

function i_change_phone(){
	global $wpdb;
	$user_id = get_current_user_id();
	$user_id = 3;
	
	if (is_user_logged_in() == false){
		return update_response_msg('get_401_error','未登陆');
	}

	$phone = i_GET(null , 'phone');
	if(!preg_match("/^1[3456789]{1}\d{9}$/",$phone)){
		return update_response_msg('get_422_error','手机格式有误，请检查后重新输入。');
	};

	//进行鉴权如果成功
	if(true){
		update_user_meta( $user_id, 'phone', $phone );
		return update_response_msg('get_200_response','更改成功');
	}else{
		return update_response_msg('get_404_error','更改失败!');
	};
}
rest_route('i_change_phone', 'i_change_phone');