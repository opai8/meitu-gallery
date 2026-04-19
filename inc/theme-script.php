<?php

/**
 * 脚本函数
 * @theme 50图
 * @author Jin <hongyexs@gmail.com>
 * @version 2026.04.19
 */


/**
 * 注册并加载主题的脚本和样式
 *
 * 遵循 WordPress 最佳实践：先注册 (register)，再加载 (enqueue)。
 * 这种方式提供了更高的灵活性，允许其他插件或子主题覆盖资源。
 * ( 句柄 (Handle), 路径URL , 依赖项必须在 jquery 之后加载 , 版本号 , 在页脚 (footer) 加载 )
 */
function wpdocs_enqueue_script() {
	
	// 获取主题版本号
    // $theme_version = wp_get_theme()->get('Version') ?: '1.0.0';

	// 注册主样式表
    wp_register_style('meitu-style', get_stylesheet_uri(), [], '3.0.1');
    wp_enqueue_style('meitu-style'); // 加载它

    // 注册并加载 jQuery（CDN）
    wp_deregister_script('jquery');
    wp_register_script('jquery', get_template_directory_uri() . '/asset/jquery-3.7.1.min.js', [], '3.7.1', true);
    wp_enqueue_script('jquery');
	
	// 注册并加载主题脚本
    wp_register_script('meitu-script', get_template_directory_uri() . '/asset/script.js', ['jquery'], '1.0.0', true);
    wp_enqueue_script('meitu-script');

    // 4. 传递 AJAX URL 到前端
    // 这个函数最好在 enqueue 之后调用，确保脚本已被加载
	wp_localize_script('meitu-script', 'meituAjax', 
		array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'meitu-nonce' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_enqueue_script' );
