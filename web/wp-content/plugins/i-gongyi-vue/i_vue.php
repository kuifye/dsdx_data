<?php
/*
Plugin Name: i_vue
Plugin URI: null
Description: 通过短代码导入vue插件、qrcode插件，2023年8月29日。
Version: 1.0.1
Author: li_zi
Author URI: http://47.120.34.164/
License: null
*/

//----------------------------------------------------------------------------------------
// 泛用插件
include 'common.php';

//----------------------------------------------------------------------------------------
// 获取 JSON 文件的内容

$json_content = file_get_contents(plugin_dir_url( __FILE__ ).'vue_shortcode.json');
$config_array = json_decode($json_content, true);
//获取前缀
$prefix = $config_array['prefix'];

for($i_code=0; $i_code<count($config_array['shortcode_profile']); $i_code++){
  //依次获取每一个vue的app
  $_add_shortcode = $config_array['shortcode_profile'][$i_code];
  //获取组件的地址
  if($_add_shortcode['plugin_dir_url'] === "__FILE__"){
    $path = plugin_dir_url( __FILE__ ).$_add_shortcode['path'].$_add_shortcode['file_name'];
  }else{
    $path = $_add_shortcode['path'].$_add_shortcode['file_name'];
  }
  //获取vm组件的名称
  $vm_name = $_add_shortcode['vm_name'];
  //组件处传递的消息，可以是html文本
  $msg = $_add_shortcode['msg'];
  //注册组件钩子，使用匿名函数传参
  add_action('wp_enqueue_scripts', function() use ($vm_name, $path) {
    return func_load_vuescripts($vm_name, $path);
  });
  add_shortcode($prefix . $vm_name, function() use ($vm_name, $msg) {
    return func_wp_vue($vm_name, $msg);
  });
}

//----------------------------------------------------------------------------------------
// 注册短代码[i_include /path]以使用html、php

function include_any_file($atts) {
  extract(shortcode_atts(array(
      'file' => ''
  ), $atts));
  if (!$file) return '';
  ob_start();
  include($file);
  return ob_get_clean();
}

add_shortcode('i_include', 'include_any_file');

//----------------------------------------------------------------------------------------
// 注册短代码[i_vue_$vm_name]以使用vue.js

function func_load_vuescripts($vm_name, $path) {
  wp_register_script('wpvue_vuejs', plugin_dir_url( __FILE__ ).'vue.js');
  wp_register_script('vuecode_json'. $vm_name, $path, 'wpvue_vuejs', true );
}

//返回短代码
function func_wp_vue($vm_name, $msg = '') {
  wp_enqueue_script('wpvue_vuejs');
  wp_enqueue_script('vuecode_json'. $vm_name);
  return $msg;
}

//----------------------------------------------------------------------------------------
// 注册短代码[i_vue_qrcode]以使用qrcode.js

function func_load_qrcodejs() {
  wp_register_script('qrcodejs', plugin_dir_url( __FILE__ ).'qrcode.min.js');
}

function func_wp_qrcode() {
  wp_enqueue_script('qrcodejs');
  return '';
}

add_action('wp_enqueue_scripts','func_load_qrcodejs');
add_shortcode($prefix . 'qrcode', 'func_wp_qrcode');