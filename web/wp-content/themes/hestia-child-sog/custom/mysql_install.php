<?php
global $wpdb;
if($wpdb){
	
  echo '<br>成功连接数据库...<br>';

	$i_costs_apportion="CREATE TABLE IF NOT EXISTS $table_prefix.`i_costs_apportion` (
		`id` int(11) unsigned NOT NULL COMMENT '主键',
		`created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
		`item_name` varchar(30) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT '品名',
		`price` int(11) DEFAULT NULL COMMENT '价格',
		`user_id` bigint(20) unsigned NOT NULL COMMENT '用户名',
		`description` text COLLATE utf8mb4_unicode_520_ci COMMENT '创建人对该项目的描述',
		`group_id` int(11) unsigned DEFAULT NULL,
		`is_reimbursement` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为报销，如果不是则为认缴',
		`is_expenses_weight` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为固定认缴，如果是，则只计算认缴开支，而不参与超出部分的平摊',
		`father_item_id` bigint(20) unsigned DEFAULT NULL COMMENT '父条目，一个父条目可以具有多个子条目，形成了模组的概念'
	) ENGINE=InnoDB AUTO_INCREMENT=383 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci COMMENT='费用分摊表';
	";
	$costs_apportion = $wpdb -> prepare($i_costs_apportion);

	$i_group="CREATE TABLE IF NOT EXISTS $table_prefix.`i_group` (
		`id` bigint(20) unsigned NOT NULL COMMENT '主键',
		`name` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '群名',
		`description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '群描述',
		`created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
		`create_user_id` int(11) DEFAULT NULL COMMENT '创建人',
		`class_type` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '群组、账本类型',
		`is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否被删除，如果是则为1',
		`rd_pwd` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '自动生成的随机密码，用于鉴权',
		`state` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '0表示没有异常状态',
		`father_group_id` bigint(20) unsigned DEFAULT NULL COMMENT '父条目，一个父条目可以具有多个子条目，形成了模组的概念'
	) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci COMMENT='分摊记账群租';
	";
	$group = $wpdb -> prepare($i_group);

	$i_group_req="CREATE TABLE IF NOT EXISTS $table_prefix.`i_group_req` (
		`id` int(11) unsigned NOT NULL COMMENT '主键',
		`group_id` int(11) unsigned NOT NULL COMMENT '群id',
		`user_id` bigint(20) unsigned NOT NULL COMMENT '加入群的用户id',
		`user_lever` int(2) unsigned NOT NULL COMMENT '用户的群权限',
		`created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发起访问的时间',
		`expenses_weight` float NOT NULL DEFAULT '1' COMMENT '用户需要支付的核算开支',
		`fixed_credit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为固定认缴，如果是，则只计算认缴开支，而不参与超出部分的平摊'
	) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci COMMENT='群组和用户之间的关系请求访问数据。';
	";
	$group_req = $wpdb -> prepare($i_group_req);

	// $i_group_insert="INSERT INTO $table_prefix.`i_group` (`id`, `name`, `description`, `created_time`, `create_user_id`, `class_type`, `is_delete`, `rd_pwd`, `state`, `father_group_id`) VALUES
	// (0, '日常饮食平摊', '中兴和园日常饮食平摊', '2023-08-21 09:04:56', 3, '日常饮食', 0, '7M0YvsMFpsN', 0, NULL);"
	// $group_insert = $wpdb -> prepare($i_group_insert);

	if ( $costs_apportion || $group || $group_insert || $group_req ){
		echo "<script>alert('欢迎您使用SOG_club.theme，数据库己经安装成功,请删除install文件!',top.location='../index.php')</script>";
	}else{
		echo 'try again,something fail.';
	}
}
?>