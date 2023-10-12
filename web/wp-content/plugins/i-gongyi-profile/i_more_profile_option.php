<?php
/*
Plugin Name: i_more_profile_option
Plugin URI: null
Description: 更多的个人资料界面选项。
Version: 1.0.0
Author: li_zi
Author URI: http://47.120.34.164/
License: null
*/

//------------------------------------------------------------------------------------------
//移除顶部工具条

// show_admin_bar( false );

//------------------------------------------------------------------------------------------
//个人界面相关的选项

function i_more_profile( $contactmethods ) {
	$contactmethods['qq'] = 'QQ';
   $contactmethods['weixin'] = '微信号';
   $contactmethods['phone'] = '手机号';
	return $contactmethods;
}
add_filter('user_contactmethods','i_more_profile');

//------------------------------------------------------------------------------------------
//禁止用户修改相关选项

// add_action( 'admin_init', 'stop_access_profile' );
// function stop_access_profile() {
//     remove_menu_page( 'profile.php' );
//     remove_submenu_page( 'users.php', 'profile.php' );
//     if(IS_PROFILE_PAGE === true) {
//         wp_die( 'You are not permitted to change your own profile information. Please contact a member of HR to have your profile information changed.' );
//     }
// }

//------------------------------------------------------------------------------------------
//下面的例子中，我们将在用户资料编辑界面添加一个生日字段，并在用户资料更新时将其保存到数据库中。

function wporg_usermeta_form_field_birthday($user)
{
   ?>
   <h3>你的生日：</h3>
   <table class=form-table>
      <tr>
         <th>
            <label for=birthday>Birthday</label>
         </th>
         <td>
            <input type=date
               class=regular-text ltr
               id=birthday
               name=birthday
               value=<?= esc_attr(get_user_meta($user->ID, 'birthday', true)); ?>
               title=Please use YYYY-MM-DD as the date format.
               pattern=(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])
               required>
            <p class=description>
               请输入你的生日。
            </p>
         </td>
      </tr>
   </table>
   <?php
}
 
/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function wporg_usermeta_form_field_birthday_update($user_id)
{
   // check that the current user have the capability to edit the $user_id
   if (!current_user_can('edit_user', $user_id)) {
      return false;
   }

   // create/update user meta for the $user_id
   return update_user_meta(
      $user_id,
      'birthday',
      $_POST['birthday']
   );
}
 
// add the field to user's own profile editing screen
add_action(
   'edit_user_profile',
   'wporg_usermeta_form_field_birthday'
);
 
// add the field to user profile editing screen
add_action(
   'show_user_profile',
   'wporg_usermeta_form_field_birthday'
);
 
// add the save action to user's own profile editing screen update
add_action(
   'personal_options_update',
   'wporg_usermeta_form_field_birthday_update'
);
 
// add the save action to user profile editing screen update
add_action(
   'edit_user_profile_update',
   'wporg_usermeta_form_field_birthday_update'
);