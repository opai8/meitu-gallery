<?php

/* ========== 基础设置 basic ========== */

/*
 * 特殊模式 theme-special
 */

// ========================================
// 哀悼模式
// ========================================
function mourning_style() {
	
    // 条件检查：仅首页且启用哀悼模式
    if (!is_home()) return;
    
    // 获取主开关选项
    $rip_value = my_option('opt_rip');
    
    // 验证主开关有效性
    if (empty($rip_value)) {
        return;
    }
	
	// 获取日期范围配置
    $rip_date = my_option('opt_rip_date');
    
	// 注册哀悼样式
	wp_register_style('mourning-style', false, [], '1.0', 'all');
	
	// 添加动态CSS
	wp_add_inline_style('mourning-style', '
		html {
			filter: grayscale(100%);
			-webkit-filter: grayscale(100%);
			transition: filter 0.5s ease;
		}
		@media print {
			html { filter: none; }
		}
	');
	
	// 无日期配置时直接启用
	if ( !isset($rip_date['from']) || !isset($rip_date['to']) ) {
		// 优先加载样式
        wp_enqueue_style('mourning-style');
        return;
    }

	// 解析时间范围
	$from = $rip_date['from'] ? DateTime::createFromFormat('Y-m-d H:i', $rip_date['from']) : null;
	$to   = $rip_date['to']   ? DateTime::createFromFormat('Y-m-d H:i', $rip_date['to'])   : null;

	// 获取当前时间
	$current = current_time('Y-m-d H:i');

	// 时间范围检查（精确到分钟）
    if ( $from < $current || $current > $to) {
        return;
    }
	wp_enqueue_style('mourning-style');
}
add_action('wp_enqueue_scripts', 'mourning_style', 11); // 高优先级确保覆盖其他样式

/**
 * 哀悼模式管理（带缓存穿透）
 */
function mourning_cache_buster() {
    if (mourning_style_enabled()) {
        // 强制穿透缓存
        define('MOURNING_MODE', true);
        header('Cache-Control: no-cache, no-store, must-revalidate');
    }
}
// add_action('init', 'mourning_cache_buster');

/**
 * 条件判断辅助函数
 */
function mourning_style_enabled(): bool {
    static $cache;
    if (null === $cache) {
        $cache = is_home() && my_option('opt_rip');
    }
    return $cache;
}




// ========================================
// 维护模式
// ========================================
add_action('get_header', 'custom_maintenance');

function custom_maintenance() {

    if ( empty( my_option('opt_maintenance') ) ) return;         // 1. 维护模式未开启直接退出
    if (current_user_can('edit_themes')) return;        // 3. 管理员直接放行
    
    // 维护模式核心响应(非管理员用户)
    wp_die(
        '<h1>网站维护中</h1>' . 
        '<p>预计2小时后恢复，请稍后访问。<br>' . 
        date('H:i') . '开始维护</p>',                  // 动态维护开始时间
        '维护公告', 
        [
            'response' => 503,
            'back_link' => false,
            'code' => '404'       // 自定义错误代码
        ]
    );
}
