<?php
/**
 * Theme 50图.
 *
 * @package 50tu
 */


// 首页瀑布流布局(50图)
require get_template_directory() .'/inc/theme-core.php';


/** 主题后台选项配置  */ 
require get_template_directory() .'/inc/theme-options.php';


// 脚本 Javascript and CSS
require get_template_directory() .'/inc/theme-script.php';


/* ====== 基础配置 Basic ====== */
	
	require get_template_directory() .'/inc/basic/theme-basic-special.php'; // 特殊模式
	
	require get_template_directory() .'/inc/basic/theme-basic-block.php';   // 功能屏蔽
	
	require get_template_directory() .'/inc/basic/theme-basic-speed.php';   // 加速优化

	require get_template_directory() .'/inc/basic/theme-basic-extend.php';	// 扩展增强



/* ====== 页面布局 Page ====== */

	require get_template_directory() .'/inc/page/theme-page-seo.php'; // SEO


/* ====== 文章附件 Post ====== */

	require get_template_directory() .'/inc/post/theme-post-attachment.php';// 附件管理


/* ====== 广告中心 Ad ====== */

/* ====== 移动端设置 App ====== */
	require get_template_directory() .'/inc/app/theme-app-set.php';   // 移动端设置