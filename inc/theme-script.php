<?php

/**
 * 脚本函数
 * @theme 50图
 * @author Jin <hongyexs@gmail.com>
 * @version 2026.03.03
 */
 

 /** 批量导入外部脚本和样式文件 */
function wpdocs_enqueue_scripts(){

	/** 注册样式 */
	wp_register_style( 'style-css', get_stylesheet_uri(), [], false, 'all'); //style.css

	wp_register_style( 'fancybox-css', get_template_directory_uri().'/fancybox/jquery.fancybox.css'); // fancybox.CSS
	
	wp_register_style( 'fancybox-buttons-css', get_template_directory_uri().'/fancybox/helpers/jquery.fancybox-buttons.css'); // fancybox-buttons.CSS
	
	/** 注册脚本 */	
	wp_register_script( 'jquery-js', get_template_directory_uri().'/asset/jquery.min.js'); //jquery.min.js
	
	wp_register_script( 'fancybox-js', get_template_directory_uri().'/fancybox/jquery.fancybox.js', ['jquery']);
	
	wp_register_script( 'fancybox-buttons-js', get_template_directory_uri().'/fancybox/helpers/jquery.fancybox-buttons.js', ['jquery']);
	
	wp_register_script( 'myfancybox-js', get_template_directory_uri().'/asset/myfancybox.js'); //myfancybox.js
	
	/** 样式队列 */
	wp_enqueue_style('style-css');
	wp_enqueue_style('fancybox-css');
	wp_enqueue_style('fancybox-buttons-css');
	
	/** 脚本队列 */
	wp_enqueue_script('jquery-js');
	wp_enqueue_script('fancybox-js');
	wp_enqueue_script('fancybox-buttons-js');
	wp_enqueue_script('myfancybox-js');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_enqueue_scripts' );