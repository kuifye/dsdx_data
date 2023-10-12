<?php
//通用接口

//------------------------------------------------------------------------------------------
// 此示例将注册后自动登陆，并重定向到主页（有问题，需要修复）

// function auto_login_new_user( $user_id ) {
// 	// 用户注册后自动登录
// 	wp_set_current_user($user_id);
// 	wp_set_auth_cookie($user_id);
// 	// 这里跳转到 http://域名/about 页面，请根据自己的需要修改
// 	wp_redirect(home_url()); 
// 	exit;
// }
// add_action( 'user_register', 'auto_login_new_user');

//------------------------------------------------------------------------------------------
// 此示例将管理员重定向到仪表板，将其他用户重定向到主页。确保在 is_admin() 之外使用 add_filter，因为调用过滤器时该函数不可用。

// function my_login_redirect( $redirect_to, $request, $user ) {
// 	//is there a user to check?
// 	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
// 		//check for admins
// 		if ( in_array( 'administrator', $user->roles ) ) {
// 			// redirect them to the default place
// 			return home_url('查看账本');
// 		} else {
// 			return home_url('查看账本');
// 		}
// 	} else {
// 		return $redirect_to;
// 	}
// }
// add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

//------------------------------------------------------------------------------------------
// mysql递归方法，这个方法似乎不能直接被php调用，而需要在mysql内部进行定义，因此该方法被弃用
// phpMyAdmin不支持delimiter语法，因此在phpMyAdmin中执行时需要手动更改命令行下面的分隔符，而不是直接复制粘贴
// 使用案例：SELECT * FROM wp_i_costs_apportion WHERE FIND_IN_SET(id,get_child_list('359'))

// function mysql_create_function(){
// 	global $wpdb;
// 	$table_name = get_table_name();
// 	$query_get_child_list = <<<STR
// 	delimiter $$
// 	drop function if exists get_child_list$$
// 	create function get_child_list(in_id varchar(10)) returns varchar(1000) DETERMINISTIC
// 	begin
// 		declare ids varchar(1000) default '';
// 		declare tempids varchar(1000);
// 		set tempids = in_id;
// 		while tempids is not null do
// 			set ids = CONCAT_WS(',',ids,tempids);
// 			select GROUP_CONCAT(id) into tempids from wp_i_costs_apportion where FIND_IN_SET(father_item_id,tempids)>0;
// 		end while;
// 		return ids;
// 	end
// 	$$
// 	delimiter ;
// 	STR;
// 	$sql = $wpdb->query($query_get_child_list);
// }

//获取所有的子记录id及记录
// 使用案例：SELECT * FROM wp_i_costs_apportion WHERE FIND_IN_SET(id,get_all_item_info_by_group_id_with_child_list('0'))

// drop function if exists get_all_item_info_by_group_id_with_child_list$$
// create function get_all_item_info_by_group_id_with_child_list(group_id bigint(20)) returns varchar(1000) DETERMINISTIC
// BEGIN
//     DECLARE groupid BIGINT(20);
//     DECLARE ids VARCHAR(1000) DEFAULT '';
//     DECLARE tempids VARCHAR(1000) DEFAULT '';

// 		SET groupid = group_id;

//     -- 获取符合条件的 wp_i_group_req.id 并拼接到 tempids
//     SELECT GROUP_CONCAT(wp_i_costs_apportion.id) INTO tempids
//     FROM wp_i_costs_apportion
//     WHERE wp_i_costs_apportion.group_id = groupid;

//     -- 循环开始
//     WHILE tempids IS NOT NULL DO
//         SET ids = CONCAT_WS(',', ids, tempids);

//         -- 获取符合条件的 id 并更新 tempids
//         SELECT GROUP_CONCAT(id) INTO tempids
//         FROM wp_i_costs_apportion
//         WHERE FIND_IN_SET(father_item_id, tempids) > 0;
//     END WHILE;

//     RETURN ids;
// END$$