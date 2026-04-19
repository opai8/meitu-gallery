<?php

/* ========== 基础设置 basic ========== */

/*
 * 加速优化 theme-speed
 */


// ========================================
// 记录开始时间并输出生成时间
// ========================================
function record_page_execution_time() {
	
	//条件判断
	if( empty( my_option('opt_execution_time') ) ) {return;}
	
    // 统一记录开始时间
    $GLOBALS['page_start_time'] = microtime(true);
    
    // 根据页面类型添加显示钩子
    if (is_admin()) {
        add_action('admin_footer', 'display_page_execution_time', 100);
    } else {
        add_action('wp_footer', 'display_page_execution_time', 100);
    }
}
add_action('init', 'record_page_execution_time');

function display_page_execution_time() {
    if (!isset($GLOBALS['page_start_time'])) return;
    
    $execution_time = microtime(true) - $GLOBALS['page_start_time'];
    $position = is_admin() ? 'admin-execution-time' : 'page-execution-time';
    
    // 统一显示样式（右下角）
    $output = '<div id="' . $position . '" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        font-size: 12px;
        z-index: 10000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    ">';
    
    if (is_admin()) {
        $output .= '<strong>后台执行时间</strong>: ';
    } else {
        $output .= '<strong>页面执行时间</strong>: ';
    }
    
    $output .= round($execution_time, 4) . ' 秒
        <br>
        <span style="font-size: 10px;">数据库查询: ' . get_num_queries() . ' 次</span>
    </div>';
    
    echo $output;
}



// ========================================
// . 前台移除jQuery Migrate .
// ========================================
function remove_jquery_migrate($scripts) {
	// 获取选项值（默认false）
    $migrate_value = my_option('opt_jquery_migrate');
    
    // 如果选项未启用则直接返回
	
    if (empty($migrate_value)) {
        return;
    }

	// 仅在非后台页面操作
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        // 确保存在依赖项数组
        if (is_array($script->deps) && !empty($script->deps)) {
            // 移除jquery-migrate依赖
            $script->deps = array_diff($script->deps, ['jquery-migrate']);
        }
    }
}
// 添加到wp_default_scripts钩子，优先级11确保在脚本注册后执行
add_action('wp_default_scripts', 'remove_jquery_migrate', 11);



// ========================================
// . 禁用后台 Google Fonts .
// ========================================
function disable_google_font() {
	
	// 条件判断
	if( empty( my_option('opt_google_font') ) ) {return;}

    // 1. 移除 WordPress 后台核心注册的 Google Fonts
    remove_action('admin_enqueue_scripts', 'wp_enqueue_google_fonts');
	// 禁用前端 Google Fonts(可选)
	remove_action('wp_enqueue_scripts', 'wp_enqueue_google_fonts');
    
    // 2. 注销已注册的样式
    wp_deregister_style('open-sans');
	wp_deregister_style('wp-block-library-theme');
    
    // 3. 过滤所有样式加载
    add_filter('style_loader_src', function ($href) {
        if (strpos($href, 'fonts.googleapis.com') !== false) {
            return false;
        }
        return $href;
    }, 999);
    
    // 4. 清理内联样式
    add_action('wp_head', function() {
        ob_start(function($html) {
            return preg_replace('/<link[^>]*fonts\.googleapis\.com[^>]*>/i', '', $html);
        });
    }, 1);
	
	// 5. 插件兼容性扩展
    if (function_exists('elementor_enqueue_google_fonts')) {
        remove_action('elementor/frontend/after_enqueue_styles', 'elementor_enqueue_google_fonts');
    }
}
add_action('init', 'disable_google_font');




// ========================================
// 替换 gravatar 源
// ========================================
if( empty( my_option('opt_gravatar') ) ){
	return;
} else{
	if ( ! function_exists( 'get_cravatar_url' ) ) {
		/**
		 * 替换 Gravatar 头像为 Cravatar 头像
		 *
		 * Cravatar 是 Gravatar 在中国的完美替代方案，您可以在 https://cravatar.com 更新您的头像
		 */
		function get_cravatar_url( $url ) {
			$sources = array(
				'www.gravatar.com',
				'0.gravatar.com',
				'1.gravatar.com',
				'2.gravatar.com',
				'secure.gravatar.com',
				'cn.gravatar.com',
				'gravatar.com',
			);
			return str_replace( $sources, 'cravatar.cn', $url );
		}
		add_filter( 'um_user_avatar_url_filter', 'get_cravatar_url', 1 );
		add_filter( 'bp_gravatar_url', 'get_cravatar_url', 1 );
		add_filter( 'get_avatar_url', 'get_cravatar_url', 1 );
	}
	if ( ! function_exists( 'set_defaults_for_cravatar' ) ) {
		/**
		 * 替换 WordPress 讨论设置中的默认头像
		 */
		function set_defaults_for_cravatar( $avatar_defaults ) {
			$avatar_defaults['gravatar_default'] = 'Cravatar 标志';
			return $avatar_defaults;
		}
		add_filter( 'avatar_defaults', 'set_defaults_for_cravatar', 1 );
	}
	if ( ! function_exists( 'set_user_profile_picture_for_cravatar' ) ) {
		/**
		 * 替换个人资料卡中的头像上传地址
		 */
		function set_user_profile_picture_for_cravatar() {
			return '<a href="https://cravatar.com" target="_blank"> 您可以在 Cravatar 修改您的资料图片</a>';
		}
		add_filter( 'user_profile_picture_description', 'set_user_profile_picture_for_cravatar', 1 );
	}

}
