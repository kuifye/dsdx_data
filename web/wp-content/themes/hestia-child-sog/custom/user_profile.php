<?php
/**
* Template Name: User Profile
*
* Allow users to update their profiles from Frontend.
*
*/

/* Get user info. */

global $current_user, $wp_roles;
//get_currentuserinfo(); //deprecated since 3.1

/* Load the registration file. */
//require_once( ABSPATH . WPINC . '/registration.php' ); //deprecated since 3.1
$error = array();    
/* 如果保存了用户记录，则更新. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

	/* 更新用户密码 */
	if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
		if ( $_POST['pass1'] == $_POST['pass2'] )
			wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
		else
			$error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
	}

	/* 更新用户信息 */
	if ( !empty( $_POST['url'] ) )
		wp_update_user( array( 'ID' => $current_user->ID, 'user_url' => esc_url( $_POST['url'] ) ) );
	if ( !empty( $_POST['email'] ) ){
		if (!is_email(esc_attr( $_POST['email'] )))
			$error[] = __('该邮箱格式不合法，请再次尝试。', 'profile');
		elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
			$error[] = __('这个邮箱已经被注册了，请尝试别的邮箱。', 'profile');
		else{
			wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
		}
	}

	if ( !empty( $_POST['display_name'] ) )
		wp_update_user( array( 'ID' => $current_user->ID, 'display_name' => esc_attr( $_POST['display_name'] ) ) );

	if ( !empty( $_POST['identity'] ) )
		update_user_meta( $current_user->ID, 'identity', esc_attr( $_POST['identity'] ) );
	if ( !empty( $_POST['phone'] ) )
		update_user_meta( $current_user->ID, 'phone', esc_attr( $_POST['phone'] ) );

	if ( !empty( $_POST['first-name'] ) )
		update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
	if ( !empty( $_POST['last-name'] ) )
		update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
	if ( !empty( $_POST['description'] ) )
		update_user_meta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );

	/* 重定向页面会更新用户信息 */
	/* 我不是代码的作者，我也不知道为什么，如果改了下面这一行之后( count($error) == 0 ){，代码确实能跑起来了 */
	if ( count($error) == 0 ) {
		//action hook 插件到一些被保存了的额外字段
		do_action('edit_user_profile_update', $current_user->ID);
		wp_redirect( get_permalink() );
		exit;
	}
}
?>

<?php
/**
 * The template for displaying all single posts and attachments.
 *
 * @package Hestia
 * @since Hestia 1.0
 */

get_header();

do_action( 'hestia_before_single_page_wrapper' );

?>
<div class="<?php echo hestia_layout(); ?>">
	<?php
	$class_to_add = '';
	if ( class_exists( 'WooCommerce', false ) && ! is_cart() ) {
		$class_to_add = 'blog-post-wrapper';
	}
	?>

	<div class="div_margin_top"></div>
	<div class="div_margin_top"></div>

	<div class="blog-post <?php esc_attr( $class_to_add ); ?>">
		<div class="container">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>">
						<div class="entry-content entry">
							<?php the_content(); ?>
							<?php if ( !is_user_logged_in() ) : ?>
								<p class="warning">
										<?php _e('你必须登陆才能编辑你的账户.', 'profile'); ?>
								</p><!-- .warning -->
							<?php else : ?>
								<?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
								<form method="post" id="adduser" action="<?php the_permalink(); ?>">

									<!-- <p class="form-username">
										<label for="first-name"><?php _e('First Name', 'profile'); ?></label>
										<input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
									</p>

									<p class="form-username">
										<label for="last-name"><?php _e('Last Name', 'profile'); ?></label>
										<input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
									</p> -->

									<p class="form-display_name">
										<label for="display_name"><?php _e('昵称 *', 'profile'); ?></label>
										<input class="text-input" name="display_name" type="text" id="display_name" value="<?php the_author_meta( 'display_name', $current_user->ID ); ?>" />
									</p><!-- .form-email -->

									<p class="form-identity">
										<label for="identity"><?php _e('身份 *', 'profile'); ?></label>
										<input class="text-input" name="identity" type="text" id="identity" value="<?php the_author_meta( 'identity', $current_user->ID ); ?>" />
									</p><!-- .form-email -->

									<p class="form-email">
										<label for="email"><?php _e('电子邮箱 *', 'profile'); ?></label>
										<input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
									</p>

									<p class="form-phone">
										<label for="phone"><?php _e('手机号', 'profile'); ?></label>
										<input class="text-input" name="phone" type="text" id="phone" value="<?php the_author_meta( 'phone', $current_user->ID ); ?>" />
									</p>

									<!-- <p class="form-password">
										<label for="pass1"><?php _e('密码 *', 'profile'); ?> </label>
										<input class="text-input" name="pass1" type="password" id="pass1" />
									</p>

									<p class="form-password">
										<label for="pass2"><?php _e('再次输入你的密码 *', 'profile'); ?></label>
										<input class="text-input" name="pass2" type="password" id="pass2" />
									</p> -->

									<p class="form-textarea">
										<label for="description"><?php _e('个人介绍', 'profile') ?></label>
										<textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
									</p><!-- .form-textarea -->

									<?php 
										//action hook for plugin and extra fields
										do_action('edit_user_profile',$current_user); 
									?>

									<p class="form-submit">
										<?php echo $referer; ?>
										<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('更新资料', 'profile'); ?>" />
										<?php wp_nonce_field( 'update-user' ) ?>
										<input name="action" type="hidden" id="action" value="update-user" />
									</p><!-- .form-submit -->

								</form><!-- #adduser -->
							<?php endif; ?>
						</div><!-- .entry-content -->
				</div><!-- .hentry .post -->
				<?php endwhile; ?>
			<?php else: ?>
					<p class="no-data">
							<?php _e('对不起，没有找到匹配的数据。', 'profile'); ?>
					</p><!-- .no-data -->
			<?php endif; ?>



		</div>
	</div>
</div>
	<?php get_footer(); ?>
