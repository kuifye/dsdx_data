<?php

// function enqueue_scripts_styles_init() {
// 	wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri().'/js/script.js', array('jquery'), 1.0 );
// 	wp_localize_script( 'ajax-script', 'WP_API_Settings', array( 
//             'root' => esc_url_raw( rest_url() ), 
//             'nonce' => wp_create_nonce( 'wp_rest' ) 
//         )
//     );
// }
// add_action('init', 'enqueue_scripts_styles_init');

//-------------------------------------------------------------------------------------------------
//废弃，暂时没用，可以用来参考

// // 后台菜单初始化时创建自定义菜单
// add_action('admin_menu', 'add_diy_menu');
// // 设置主菜单和子菜单的函数
// function add_diy_menu() {
//     // 顶级菜单标题
//     add_menu_page(__('顶级菜单'),__('简码菜单'),8,__FILE__,'my_function_menu');
//     // 子菜单标题
//     add_submenu_page(__FILE__,'子菜单1','vue管理界面',8,'your-admin-sub-menu1','my_function_submenu1');
//     // 子菜单标题
//     add_submenu_page(__FILE__,'子菜单2','其他测试界面',8,'your-admin-sub-menu2','my_function_submenu2');
// }
// // 主菜单界面
// function my_function_menu() {
//     // 输出div
//     echo "<div style='width:100%;height:1000px;background-color:#eee'>测试菜单</div>";
// }
// // 子菜单界面-演示为vue项目
// function my_function_submenu1() {
//     // 引入插件dist目录下的html
//     require_once("dist/index.html");
// }
// // 子菜单界面
// function my_function_submenu2() {  
//   // 获取url方式的html
//   $html = str_get_html('https://unpkg.com/vue@3/dist/vue.global.js"');
//   echo $html; 
    
//   // 也可以引入php（里面有html的内容）
//   // include_once 'test.php';
// }


//-------------------------------------------------------------------------------------------------
//参考用

// function func_load_vuescripts() {
//   wp_register_script('wpvue_vuejs', plugin_dir_url( __FILE__ ).'vue.js');
//   wp_register_script('vuecode_json', plugin_dir_url( __FILE__ ).'vuecode.js', 'wpvue_vuejs', true );
// }
// add_action('wp_enqueue_scripts', 'func_load_vuescripts');

// //返回短代码
// function func_wp_vue(){
//   wp_enqueue_script('wpvue_vuejs');
//   wp_enqueue_script('vuecode_json');
//   $json_content = file_get_contents(plugin_dir_url( __FILE__ ).'vue_shortcode.json');
//   $config_array = json_decode($json_content, true);
//   return $json_content;
// }
// add_shortcode( 'i_vue_test', 'func_wp_vue' );