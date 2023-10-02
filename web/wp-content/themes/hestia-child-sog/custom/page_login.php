<?php
/*
Template Name: 登陆页面
*/
//获取参数
global $wpdb,$user_ID;
//判断用户是否登录
if (!$user_ID) { 

	//如果没有登陆，则提交数据
	if($_POST){ 
		//We shall SQL escape all inputs
		$username = $wpdb->escape($_REQUEST['username']);
		$password = $wpdb->escape($_REQUEST['password']);
		$remember = $wpdb->escape($_REQUEST['rememberme']);
		
		if($remember){
			$remember = "true";
		}else{
			$remember = "false";
		}

		$login_data = array();
		$login_data['user_login'] = $username;
		$login_data['user_password'] = $password;
		$login_data['remember'] = $remember;
		//wp_signon 是wordpress自带的函数，通过用户信息来授权用户(登陆)，可记住用户名
		$user_verify = wp_signon( $login_data, false );
	
		if ( is_wp_error($user_verify) ) {
			//不管啥错误都输出这个信息
			echo var_dump($_REQUEST);
			echo "<span class='error'>用户名或密码错误，请重试!</span>";
			exit();
		}else{ 
			//登陆成功则跳转到首页(ajax提交所以需要用js来跳转)
			echo "<script type='text/javascript'>window.location='". get_bloginfo('url') ."'</script>";
			exit();
		}

	}else{
		
		//这里添加登录表单代码
		get_header();//载入头部文件
		?>

		<div id="container">
			<div id="content">
				<h3>地上的星-登陆页面</h3>

				<div id="result"></div> <!-- 输出结果 -->

					<form id="wp_login_form" action="" method="post">
						<label>用户名</label><br />
						<input type="text" name="username" class="text" value="" /><br />
						<label>密码</label><br />
						<input type="password" name="password" class="text" value="" /> <br />
						<label>
						<input name="rememberme" type="checkbox" value="forever" />记住我</label>
						<br /><br />
						<input type="submit" id="submitbtn" name="submit" value="Login" />
					</form>

					<h1><?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?><h1>

				<!-- ajax提交数据 -->
				<script type="text/javascript">
					$("#submitbtn").click(function() {
					
						$('#result').html('<img src="<?php bloginfo('template_url'); ?>/images/loader.gif" class="loader" />').fadeIn();
						var input_data = $('#wp_login_form').serialize();
						console.log(input_data);

						jQuery.ajax({
							type: "POST",
							url: "<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>",
							data: input_data,

							success: function(msg){
								$('.loader').remove();
								$('<div>').html(msg).appendTo('div#result').hide().fadeIn('slow');
							}
						});
						return false;

					});
				</script>

			</div>
		</div>

		<?php
		get_footer(); //载入底部文件
		
	}
}else { 
	//如果已经登陆了，则直接跳转到首页
	echo "<script type='text/javascript'>window.location='". get_bloginfo('url') ."'</script>";
}