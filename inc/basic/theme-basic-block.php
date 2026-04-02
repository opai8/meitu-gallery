<?php

/* ========== 基础设置 basic ========== */

/*
 * 功能屏蔽 theme-block
 */


// ========================================
// 主控制函数 - 仅在管理后台执行--且限制页面
// ========================================
add_action('admin_init', 'execute_admin_security_function', 20, 1);

function execute_admin_security_function() {

    // 权限控制：仅管理员可执行
    // if (!current_user_can('manage_options')) return;

	// 页面判断：只在特定页面执行（可选，提升性能）
    // $allowed_pages = array(
		// 'index.php',
		// 'settings_page_my-plugin',  // 示例：设置页面
        // 'toplevel_page_my-plugin',  // 示例：顶级菜单页面
        // 'edit-tags.php'            // 示例：标签管理页面
		// );
    // $current_page = basename($_SERVER['PHP_SELF'] ?? '');
    
	// 检查是否在允许的页面
    // if (!in_array($current_page, $allowed_pages)) {
        // return;
    // }

    // 选项映射配置:键名映射到函数名
    $option_function_map = array(
		// 常规功能
        'opt_block_common' => array(
            'opt_revise'      => 'disable_post_revision',
            'opt_draft'       => 'disable_draft_data',
            'opt_trackback'   => 'disable_trackback',
            'opt_xmlrpc'      => 'disable_xmlrpc',
            'opt_auto_update' => 'disable_auto_update',
            'opt_feed'        => 'disable_feed',
            'opt_email_check' => 'disable_email_check',
            'opt_unuse_style' => 'disable_unuse_style'
        ),
		// 函数禁用
        'opt_block_func'	 => array(
            'opt_translation_api'  => 'disable_translations_api',
            'opt_wp_phpv'	   	   => 'disable_wp_phpv',
            'opt_check_browserv'   => 'disable_check_browserv'
        ),
		// 转换功能
        'opt_block_transform' => array(
            'opt_emoji'			   => 'disable_emoji',
            'opt_wptexturize'	   => 'disable_wptexturize',
            'opt_capitalization'   => 'disable_capitalization'
        ),
		// 后台功能
        'opt_block_backend' => array(
			'opt_home_bar'		  => 'remove_home_bar',
			'opt_admin_bar'		  => 'remove_admin_bar',
            'opt_dashboard_tool'  => 'remove_dashboard_tool',
            'opt_screen_option'   => 'remove_screen_option'
        ),
		// 页面功能
        'opt_block_page' => array(		
			'opt_theme_menu'	 => 'remove_theme_menu',
			'opt_site_editor'	 => 'remove_site_editor',
			'opt_customize_manage'   => 'remove_customize_manage',
			'opt_theme_editor'   => 'remove_theme_editor',
			'opt_site_health'	 => 'remove_site_health',
			'opt_gdpr_privacy'   => 'remove_gdpr_privacy'
			
        ),
		// 嵌入功能
        'opt_block_embed' => array(
            'opt_auto_embed' => 'disable_auto_embed',
            'opt_wp_embed'   => 'disable_wp_embed'
        ),
		// 古腾堡编辑器
        'opt_block_editor' => array(
            'opt_gutenberg_editor' => 'disable_gutenberg_editor',
            'opt_widget_editor'    => 'disable_widget_editor'
        ),
    );

	
	// 获取所有选项组的选中值
    $all_selected = [];
    foreach ($option_function_map as $option_name => $mapping) {
        $selected = my_option($option_name); // 替换为实际的选项获取方法
        if (is_array($selected)) {
            $all_selected[$option_name] = $selected;
        }
    }
	
	// 遍历执行对应函数
    foreach ($all_selected as $option_name => $selected_options) {
        foreach ($selected_options as $option_value) {
            if (isset($option_function_map[$option_name][$option_value])) {
                $function_name = $option_function_map[$option_name][$option_value];
                
                // 安全校验函数是否存在
                if (function_exists($function_name)) {
                    call_user_func($function_name);
                }
            }
        }
    }
	
}


// ========================================
// . 屏蔽文章修订功能 .
// ========================================
/**
 * 彻底禁用所有文章类型的修订功能
 * 移除编辑页面中的修订版本元框
 * 禁用自动保存功能
 * 清除所有现有文章的修订版本和自动保存草稿
 * 清理相关的数据库记录
 */
function disable_post_revision() {
    // 禁用修订功能
    add_filter('wp_revisions_to_keep', '__return_zero', 10, 2);
    add_filter('wp_revisions_to_keep', '__return_false', 10);
    
    // 禁用自动保存
    add_action('admin_print_scripts', 'disable_autosave_script');
    
    // 移除修订版本元框
    add_action('admin_head', 'remove_revision_meta_box');
    
    // 禁用修订保存
    add_action('save_post', 'disable_revision_saving', 10, 2);
    
    // 禁用 REST API 修订版本
    add_filter('rest_prepare_revision', '__return_null');

	// 禁用自动保存间隔
	add_filter('autosave_interval', '__return_false');
}

// 禁用自动保存
function disable_autosave_script() {
    wp_deregister_script('autosave');
}

// 移除修订版本元框
function remove_revision_meta_box() {
    remove_meta_box('revisionsdiv', 'post', 'normal');
    remove_meta_box('revisionsdiv', 'page', 'normal');
}

// 禁用修订保存
function disable_revision_saving($post_id, $post) {
	// 自动保存时不处理
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 禁用修订版本
	remove_action('pre_post_update', 'wp_save_post_revision');
}




// ========================================
// . 删除所有自动保存草稿 .
// ========================================
function disable_draft_data() {
    global $wpdb;
    
    // 启动事务确保原子性
    $wpdb->query('START TRANSACTION');
    
    // 1. 删除所有自动保存草稿
    $wpdb->delete(
        $wpdb->posts,
        ['post_status' => 'auto-draft'],
        ['%s']
    );
    if ($wpdb->last_error) {
        $wpdb->query('ROLLBACK');
        error_log('删除自动草稿时出错: ' . $wpdb->last_error);
        return;
    }
    
    // 2. 删除所有自动保存修订版本
    $wpdb->delete(
        $wpdb->posts,
        [
            'post_type' => 'revision',
            'post_status' => 'auto-draft'
        ],
        ['%s', '%s']
    );

    // 3. 删除所有inherit类型数据
    $wpdb->delete(
        $wpdb->posts,
        ['post_status' => 'inherit'],
        ['%s']
    );

    // 清理相关元数据
    $wpdb->query(
        "DELETE meta FROM $wpdb->postmeta meta
         LEFT JOIN $wpdb->posts posts ON meta.post_id = posts.ID
         WHERE posts.ID IS NULL"
    );

    // 提交事务
    $wpdb->query('COMMIT');
    
    // 清除缓存保证数据一致性
    wp_cache_flush();
}





// ========================================
// . 彻底禁用 Trackback 功能 .
// ========================================
function disable_trackback() {
	// 1. 核心层禁用（最高优先级）
	add_filter('pings_open', '__return_false', 100); // 阻止新文章接收ping
	add_filter('trackback_enabled', '__return_false'); // 禁用trackback功能

	// 2. 请求拦截层（防止扫描/利用）
	add_action('init', function() {
		// 拦截所有Trackback特征请求
		if (isset($_GET['tb_id']) || isset($_POST['tb_id']) || 
			preg_match('/trackback\?/i', $_SERVER['REQUEST_URI'])) {
			wp_die(
				'本站已彻底禁用Trackback功能',
				'服务已禁用',
				['response' => 403, 'back_link' => true]
			);
		}
	});

	// 3. 接口层禁用（API/REST）
	add_filter('rest_endpoints', function($routes) {
		// 移除所有与Trackback/Pingback相关的REST端点
		foreach ($routes as $route => $callback) {
			if (preg_match('/(trackback|pingback)/i', $route)) {
				unset($routes[$route]);
			}
		}
		return $routes;
	});

	// 4. UI层移除（前后台）
	add_action('admin_init', function() {
		// 移除后台Trackbacks元框
		remove_meta_box('trackbacksdiv', ['post', 'page'], 'normal');
		
		// 移除讨论设置中的Trackback选项
		add_filter('discussion_settings', function($fields) {
			unset($fields['trackback_enabled']);
			return $fields;
		});
	});

	// 5. 数据库清理（可选，执行前务必备份）
	// global $wpdb;
	// $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'trackback'");
}




// ========================================
// . 彻底禁用并隐藏XML-RPC功能 .
// ========================================
function disable_xmlrpc(){
	// 1. 核心禁用：关闭XML-RPC
	add_filter('xmlrpc_enabled', '__return_false');

	// 2. 移除 XML-RPC 头部信息（防止被扫描发现）
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');

	// 3. 【安全】移除 HTTP 头中的 Pingback 链接
	add_filter('wp_headers', function($headers) {
		unset($headers['X-Pingback']);
		return $headers;
	});

    // 5. (可选但推荐) 【性能】对于非 API 请求，提前终止
    // 如果检测到是 XML-RPC 请求，直接退出，避免加载整个 WordPress 环境
    add_action('init', function() {
        // 检查请求的 URI 是否包含 xmlrpc.php
        if (strpos($_SERVER['REQUEST_URI'], 'xmlrpc.php') !== false) {
            // 返回一个标准的 403 Forbidden 响应
            status_header(403);
            nocache_headers();
			exit('XML-RPC 服务已禁用');
        }
    });
}



// ========================================
// . 完全禁用WordPress的所有自动更新 .
// ========================================
function disable_auto_update(){

	// 禁用核心自动更新
	add_filter('auto_update_core', '__return_false');

	// 禁用插件自动更新
	add_filter('auto_update_plugin', '__return_false');

	// 禁用主题自动更新
	add_filter('auto_update_theme', '__return_false');

	// 禁用翻译文件自动更新
	add_filter('auto_update_translation', '__return_false');

	// 禁用开发版本更新
    add_filter('allow_dev_auto_core_updates', '__return_false');
	
	// 移除更新通知（可选）
	add_filter('pre_site_transient_update_core', '__return_null');
	add_filter('pre_site_transient_update_plugins', '__return_null');
	add_filter('pre_site_transient_update_themes', '__return_null');

	// 移除自动更新相关的定时任务
	add_action('init', 'remove_auto_update_cron_jobs', 10);

	// 移除自动更新相关的管理通知
	add_action('admin_notices', 'remove_auto_update_notices', 10);

}

// 移除自动更新相关的定时任务 
function remove_auto_update_cron_jobs() {
    // 移除核心自动更新检查
    wp_clear_scheduled_hook('wp_version_check');
    
    // 移除插件自动更新检查
    wp_clear_scheduled_hook('wp_update_plugins');
    
    // 移除主题自动更新检查
    wp_clear_scheduled_hook('wp_update_themes');
    
    // 移除翻译自动更新检查
    wp_clear_scheduled_hook('wp_update_translation_strings');
}

// 移除自动更新相关的管理通知
function remove_auto_update_notices() {
    // 移除核心更新通知
    remove_action('admin_notices', 'update_nag', 3);
    
    // 移除插件更新通知（如果需要）
    // 注意：这可能会影响用户体验，请谨慎使用
    // remove_action('admin_notices', 'wp_plugin_update_rows', 20);
}




// ========================================
// . 屏蔽站点 Feed 功能 .
// ========================================
function disable_feed() {
    // 1. 禁用所有 Feed 类型并拦截请求
    add_action('do_feed',      'disable_feed_handler', 1);
    add_action('do_feed_rdf',  'disable_feed_handler', 1);
    add_action('do_feed_rss',  'disable_feed_handler', 1);
    add_action('do_feed_rss2', 'disable_feed_handler', 1);
    add_action('do_feed_atom', 'disable_feed_handler', 1);
    
    // 2. 移除 Feed 链接（防止被扫描发现）
    add_action('init', function() {
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('template_redirect', 'do_feed_redirect', 10);
    }, 10);
    
    // 3. 禁用 REST API 中的 Feed 端点
    add_filter('rest_endpoints', function($endpoints) {
        $feed_routes = [
            '/wp/v2/posts',
            '/wp/v2/comments',
            '/wp/v2/categories',
            '/wp/v2/tags',
            '/wp/v2/users',
            '/wp/v2/media',
            '/wp/v2/pages',
            '/wp/v2/types',
            '/wp/v2/statuses',
            '/wp/v2/taxonomies',
            '/wp/v2/blocks',
            '/wp/v2/search'
        ];
        
        foreach ($feed_routes as $route) {
            if (isset($endpoints[$route])) {
                unset($endpoints[$route]);
            }
        }
        
        return $endpoints;
    }, 9999);
}

// 禁用 Feed 请求处理
function disable_feed_handler() {
    wp_redirect(home_url('/404'), 404); // 重定向到404页面（404状态码）
    exit();
}




// ========================================
// . 禁用站点管理员邮箱定期验证 .
// ========================================
function disable_email_check() {
  
    // 核心禁用
    add_filter('admin_email_verification_required', '__return_false');
    add_filter('admin_email_check_interval', '__return_false');
    
    // 清理数据
    $options = array(
        'admin_email_verification_pending',
        'admin_email_verification_expiration',
        'admin_email_verification_token',
        'admin_email_verification_key',
        'admin_email_last_checked',
        'admin_email_verification_expiration',
        '_admin_email_verification_pending'
    );
    
    foreach ($options as $option) {
        delete_option($option);
    }
    
    // 清理用户元数据
    add_action('init', function() {
        static $done = false;
        if ($done) return;
        
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE '%admin_email_verification%'");
        
        $done = true;
    });
    
    // 移除提示
    remove_action('admin_notices', 'admin_email_verify_notice');
    remove_action('admin_init', 'admin_email_verify_init');
}


// ========================================
// . 优化wp_head,移除未使用样式 .
// ========================================
function disable_unuse_style() {
    // 条件判断：仅在前端页面执行
    if (is_admin()) return;

    // 安全移除：版本号暴露（安全风险）
    if (function_exists('wp_generator')) {
        remove_action('wp_head', 'wp_generator');
    }

    // SEO优化：短链接（保留规范链接）
    if (has_action('wp_head', 'adjacent_posts_rel_link_wp_head')) {
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    }

    // 性能优化：oEmbed发现链接
    $oembed_actions = [
        'rest_output_link_wp_head',
        'wp_oembed_add_discovery_links',
        'wp_oembed_add_host_js_css'
    ];
    foreach ($oembed_actions as $action) {
        if (has_action('wp_head', $action)) {
            remove_action('wp_head', $action);
        }
    }

	// 样式优化：条件移除块样式（测试模式）
	if (wp_style_is('wp-block-library', 'registered')) {
		wp_dequeue_style('wp-block-library');
		wp_deregister_style('wp-block-library');
	}

    // 移除内联样式缓存
	if (wp_style_is('global-styles', 'registered')) {
		wp_dequeue_style('global-styles');
		remove_action('wp_head', 'wp_global_styles_render_svg_filters');
		remove_action('wp_head', 'wp_global_styles_render_keyframes');
	}

}



// ========================================
// . 禁止 translations_api .
// ========================================
function disable_translation_api() {
	add_filter('translations_api', 'disable_translations_api', 999, 2);
}

// ========================================
// . 禁止 wp_check_php_version .
// ========================================
function disable_wp_phpv() {
	// 直接拦截函数输出
	add_filter('wp_check_php_version', '__return_empty_array');
}

// ========================================
// . 禁止 wp_check_browser_version .
// ========================================
function disable_check_browserv() {
	// 直接拦截
	add_filter('wp_check_browser_version', '__return_empty_array');
}


// ========================================
// . 屏蔽emoji .
// ========================================
function disable_emoji() {
    // 1. 移除所有Emoji相关动作和过滤器(含前台和后台的Emoji脚本和样式)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_head', 'print_emoji_styles', 99);
    remove_action('admin_print_scripts', 'print_emoji_detection_script', 7);
    remove_action('admin_print_styles', 'print_emoji_styles', 99);
    remove_action('embed_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('oembed_response_data', 'wp_filter_oembed_result');
    
    // 2. 禁用DNS预取中的Emoji
    remove_action('wp_head', 'wp_resource_hints', 2);
    
    // 3. 禁用SVG Emoji
    add_filter('emoji_svg_url', '__return_false');
    
    // 4. 移除编辑器Emoji
    add_filter('tiny_mce_plugins', function($plugins) {
        if (is_array($plugins) && in_array('wpemoji', $plugins)) {
            return array_diff($plugins, array('wpemoji'));
        }
        return $plugins;
    });
    
    // 5. 移除REST API中的Emoji
    remove_filter('rest_pre_echo_response', 'wp_oembed_add_discovery_links');
    
    // 6. 禁用自动转换（保留wptexturize的其他功能）
    add_filter('wp_staticize_emoji', '__return_false');
    
    // 7. 移除Emoji相关的CSS类
    add_action('wp_head', function() {
        echo '<style>img.emoji { display: none !important; }</style>';
    }, 999);
}



// ========================================
// . 屏蔽字符转换(禁用wptexturize) .
// ========================================
function disable_wptexturize() {
    // 移除内容和评论的格式化
    remove_filter('the_content', 'wptexturize');
    remove_filter('the_excerpt', 'wptexturize');
    remove_filter('comment_text', 'wptexturize');
    
    // 保留标题的格式化（可选）
    // remove_filter('the_title', 'wptexturize');
    
    // 移除RSS中的格式化
    remove_filter('the_content_rss', 'wptexturize');
    remove_filter('the_title_rss', 'wptexturize');
    
    // 移除小工具中的格式化
    remove_filter('widget_text', 'wptexturize');
    remove_filter('widget_title', 'wptexturize');
}




// ========================================
// . 屏蔽 WordPress 大小写 .
// ========================================
function disable_capitalization() {
	// 移除标题大小写修正（P大写修正）
    remove_filter('the_title', 'capital_P_dangit', 11);
    remove_filter('the_content', 'capital_P_dangit', 11);
	// 评论中禁用
    remove_filter('comment_text', 'capital_P_dangit', 31);
	// 移除编辑保存时的标题修正(可选)
    remove_filter('title_save_pre', 'capital_P_dangit');
	// 移除RSS中的修正
    remove_filter('the_title_rss', 'capital_P_dangit');
    remove_filter('wp_title', 'capital_P_dangit');
}




// ========================================
// . 移除前台顶部工具栏 .
// ========================================
function remove_home_bar() {
	// 禁用工具栏
	add_filter('show_admin_bar', '__return_false');

	 // 移除个人资料页面的工具栏选项字段
    remove_action('personal_options_update', '_admin_bar_preferences');
	
	// 移除管理栏相关的CSS/JS
    remove_action('wp_head', '_admin_bar_bump_cb');
    remove_action('wp_footer', 'wp_admin_bar_render', 1000);	
}



// ========================================
// . 移除后台顶部工具栏 .
// ========================================
function remove_admin_bar() {
    // 仅在后台执行
    if (is_admin() && !defined('DOING_AJAX')) {
        // 强制隐藏管理栏
        add_filter('show_admin_bar', '__return_false', 100);
        
        // 移除管理栏渲染钩子
        remove_action('wp_footer', 'wp_admin_bar_render', 1000);
        remove_action('admin_print_scripts', 'wp_admin_bar_enqueue_scripts');
        remove_action('admin_print_styles', 'wp_admin_bar_enqueue_styles');
        
        // 添加CSS强制隐藏
        add_action('admin_head', function() {
            echo '<style>
                #wpadminbar, 
                #wp-toolbar { 
                    display: none !important; 
                    visibility: hidden !important;
                }
                html {
                    margin-top: 0 !important;
                    padding-top: 0 !important;
                }
            </style>';
        });
    }
	
	// 防止通过URL直接访问管理栏相关页面
	if (is_admin() && !current_user_can('manage_options')) {
        wp_redirect(home_url());
        exit;
    }
	
}



// ========================================
// . 移除仪表盘小工具 .
// ========================================
function remove_dashboard_tool() {

    // 移除所有默认小工具
    $widgets = array(
        'dashboard_right_now',     // 概况
        'dashboard_activity',      // 活动
        'dashboard_primary',       // WordPress新闻
        'dashboard_secondary',     // 其他新闻
        'dashboard_quick_press',   // 快速发布
        'dashboard_recent_drafts', // 最近草稿
        'dashboard_recent_comments', // 最近评论
        'dashboard_incoming_links', // 入站链接
        'dashboard_plugins'        // 插件
    );
	// 批量移除小工具(保留核心样式)
    foreach ($widgets as $widget) {
        remove_meta_box($widget, 'dashboard', 'normal');
        remove_meta_box($widget, 'dashboard', 'side');
    }
    
    // 移除欢迎面板
    // remove_action('welcome_panel', 'wp_welcome_panel');
    
    // 清理缓存
    delete_transient('dashboard_primary');
    delete_transient('dashboard_secondary');
    delete_transient('dashboard_activity');
}



// ========================================
// . 移除后台右上角帮助和选项 .
// ========================================
function remove_screen_option() {
	// 「显示选项」
	add_filter('screen_options_show_screen', '__return_false');
	
	add_filter('hidden_columns', '__return_empty_array');
	// 「帮助」
	add_action('in_admin_header', function(){
		global $current_screen;
		$current_screen->remove_help_tabs();
	});
}



// ========================================
// . 移除「外观」菜单项 .
// ========================================
function remove_theme_menu() {
	// 移除主菜单项
    // remove_menu_page('edit.php');         	// 移除文章菜单
    // remove_menu_page('upload.php');      	// 移除媒体库菜单
    // remove_menu_page('link-manager.php'); 	// 移除链接菜单
    // remove_menu_page('edit-comments.php');	// 移除评论菜单
    remove_menu_page('themes.php');			// 移除外观菜单
    // remove_menu_page('plugins.php');			// 移除插件菜单
    // remove_menu_page('users.php');			// 移除用户菜单
    // remove_menu_page('tools.php');       	// 移除工具菜单
    
    // 移除子菜单项（示例）
    remove_submenu_page('options-general.php', 'options-reading.php'); // 移除阅读设置
}



// ========================================
// . 移除「外观-样板」菜单项 .
// ========================================
function remove_site_editor() {
	// 移除样板相关资源（假设资源句柄）
    wp_dequeue_style('template-styles');
    wp_dequeue_script('template-scripts');
	
	// 移除后台菜单项[外观-样板]
	// remove_submenu_page('themes.php', 'site-editor.php');
	// 移除「样板」（动态检测可能存在的标识符）
    global $submenu;
    if (isset($submenu['themes.php'])) {
        foreach ($submenu['themes.php'] as $key => $menu_item) {
            // 检测菜单项是否包含「样板」或「模板」关键词
            if (
                is_array($menu_item) && 
                isset($menu_item[0]) && 
                (
                    strpos($menu_item[0], '样板') !== false || 
                    strpos($menu_item[0], '模板') !== false
                )
            ) {
                remove_submenu_page('themes.php', $menu_item[2]);
                break;
            }
        }
    }
	
	// 防止通过URL直接访问被移除页面
	if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/site-editor.php') !== false) {
        wp_redirect(admin_url(), 301);
        exit;
    }

}



// ========================================
// . 移除「外观-主题文件编辑器」菜单项 .
// ========================================
function remove_theme_editor() {
    // 移除菜单项
    remove_submenu_page('themes.php', 'theme-editor.php');
    
    // 移除相关资源
    wp_dequeue_style('theme-editor');
    wp_dequeue_script('theme-editor');
    
    // 防止通过URL直接访问
    if (isset($_GET['page']) && $_GET['page'] === 'theme-editor.php') {
        wp_redirect(admin_url());
        exit;
    }
}



// ========================================
// . 移除「外观-自定义」菜单项 .
// ========================================
function remove_customize_manage() {
	if (!current_user_can('manage_options')) {
        die('权限错误：无权限执行此操作');
    }
    // 1. 移除自定义器前端资源加载
    remove_action('admin_enqueue_scripts', 'wp_customize_controls_enqueue_scripts');
    remove_action('customize_controls_enqueue_scripts', 'wp_customize_theme_control_enqueue_scripts');
    remove_action('customize_controls_init', 'wp_customize_theme_control_init');
    remove_action('customize_register', 'wp_customize_theme_control_register');

    // 2. 禁用自定义器API接口
    remove_action('wp_ajax_customize_save', 'wp_customize_save');
    remove_action('wp_ajax_nopriv_customize_save', 'wp_customize_save');
    remove_action('wp_ajax_customize_load', 'wp_customize_load');
    remove_action('wp_ajax_nopriv_customize_load', 'wp_customize_load');
    remove_action('wp_ajax_customize_preview', 'wp_customize_preview');
    remove_filter('customize_save_response', 'wp_customize_save_response');

    // 3. 移除后台菜单项
	// remove_submenu_page('themes.php', 'customize.php'); // (推荐,但未生效,使用以下方法)
	global $submenu;
    if (isset($submenu['themes.php'])) {
        foreach ($submenu['themes.php'] as $key => $menu_item) {
            // 检测菜单回调函数是否指向自定义器
            if (
                is_array($menu_item) && 
                isset($menu_item[2]) && 
                (
                    $menu_item[2] === 'customize.php' || 
                    strpos($menu_item[2], 'customize') !== false ||
                    strpos($menu_item[0], '自定义') !== false
                )
            ) {
                unset($submenu['themes.php'][$key]);
                break;
            }
        }
    }

	// 4.防止通过URL直接访问customize.php
	if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/customize.php') !== false) {
        wp_redirect(admin_url(), 301);
        exit;
    }
}


// ========================================
// . 移除[工具-站点健康页面]「工具-个人数据」菜单项 .
// ========================================
function remove_site_health() {
    // 移除菜单项
    remove_menu_page('site-health.php');
    remove_submenu_page('tools.php', 'site-health.php');
    
    // 移除仪表盘面板
    add_action('wp_dashboard_setup', function() {
        remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
        remove_meta_box('dashboard_php_nag', 'dashboard', 'side');
    });
    
    // 阻止访问站点健康页面
    add_action('admin_init', function() {
        if (isset($_GET['page']) && in_array($_GET['page'], ['site-health', 'site-health.php'])) {
            wp_redirect(admin_url());
            exit;
        }
    });
    
    // 禁用邮件通知
    add_filter('site_health_send_email_notification', '__return_false');
    
    // 禁用健康检测
    add_filter('site_health_run_tests', '__return_false');
    add_filter('site_health_collect_data', '__return_false');
    
    // 移除定时任务
    wp_clear_scheduled_hook('site_health_cron_hook');
    
    // 禁用API端点
    add_filter('site_health_enable_api', '__return_false');
    
    // 移除标签页
    add_filter('site_health_navigation_tabs', '__return_empty_array');
    
    // 移除脚本和样式
    add_action('admin_enqueue_scripts', function($hook) {
        if (strpos($hook, 'site-health') !== false) {
            wp_dequeue_script('site-health');
            wp_dequeue_style('site-health');
        }
    });
    
    // 阻止访问测试结果页面
    add_action('current_screen', function() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'site-health') {
            wp_redirect(admin_url(), 301);
            exit;
        }
    });
    
    // 禁用错误日志功能
    add_filter('site_health_error_log', '__return_false');
}




// ========================================
// . 移除[设置-隐私]菜单项 .
// ========================================
function remove_gdpr_privacy() {
    // 1. 权限验证（管理员权限检查）
    if (!current_user_can('manage_options')) {
        die('权限错误：无权限执行此操作 code => 403');
    }

    // 2. 移除隐私政策页面
    $privacy_page = get_posts([
        'post_type'   => 'page',
        'meta_key'    => '_wp_page_template',
        'meta_value'  => 'privacy-policy',
        'fields'      => 'ids',
        'numberposts' => 1
    ]);


	if (!empty($privacy_page)) {
        wp_delete_post($privacy_page[0], true); // 强制删除不放入回收站
        
        // 直接清理关联元数据（无需等待钩子）
        global $wpdb;
        $wpdb->delete(
            $wpdb->postmeta,
            [
                'post_id'  => $privacy_page[0],
                'meta_key' => '_wp_page_template'
            ],
            ['%d', '%s']
        );
    }

    // 3. 移除后台菜单项
    remove_submenu_page('options-general.php', 'options-privacy.php');       // 隐私设置
    remove_submenu_page('tools.php', 'export-personal-data.php');            // 导出个人数据
    remove_submenu_page('tools.php', 'erase-personal-data.php');             // 抹除个人数据

    // 4. 清理全局残留数据（元数据+选项）
    delete_option('wp_page_for_privacy_policy');
    delete_option('_wp_page_for_privacy_policy'); // 兼容旧版WP

    // 5. 禁用隐私相关功能钩子（防止默认内容生成）
    if (class_exists('WP_Privacy_Policy_Content')) {
        $policy_class = 'WP_Privacy_Policy_Content';
        remove_action('admin_init', [$policy_class, 'text_change_check'], 100);
        remove_action('edit_form_after_title', [$policy_class, 'notice']);
        remove_action('admin_init', [$policy_class, 'add_suggested_content'], 1);
    }
}





// ========================================
// . 禁用Auto Embeds功能 .
// ========================================
function disable_auto_embed() {
    remove_filter('the_content', 'wp_auto_embed', 8);
	add_filter('autoembed_enabled', '__return_false');
}



// ========================================
// . 屏蔽嵌入其他WordPress文章功能 .
// ========================================
function disable_wp_embed() {
    remove_action('wp_enqueue_scripts', 'wp_maybe_enqueue_embeds');
    add_filter('embed_oembed_html', '__return_false');
}




// ========================================
// . 屏蔽Gutenberg编辑器 .
// ========================================
function disable_gutenberg_editor() {

	// 禁用块编辑器（Gutenberg）的核心开关
	add_filter('use_block_editor_for_post', '__return_false', 10, 2);
	
	// 启用经典编辑器插件功能
	add_filter('classic_editor_enabled', '__return_true');
}



// ========================================
// . 屏蔽小工具区块编辑器模式 .
// ========================================
function disable_widget_editor() {

	// 禁用小工具的区块编辑器
	add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
}



// ========================================
// . 自定义后台工具栏 .
// ========================================
function custom_toolbar_link($wp_admin_bar) {
	
	// 条件判断
	if( empty( my_option('opt_custom_toolbar') ) ) {return;}

	// 定义需要移除的节点ID数组
    $target_node = [
        'wp-logo',        // WordPress LOGO
        // 'site-name',      // 网站名称
			'view-site',      // 查看站点
        'updates',        // 更新提醒
        'comments',       // 评论提醒
        'new-content',    // 新建文件
        'top-secondary',  // 用户信息区域
    ];
	
	// 批量移除节点
    foreach ($target_node as $args) {
        $wp_admin_bar->remove_node($args);
    }


	// 主菜单项配置
    $main_menu_item = [
        [
            'id'    => 'custom_home_link',
            'title' => '前台首页',
            'group' => null,
            'href'  => home_url(),
            'meta'  => [
                'class'  => 'custom_home_link',
                'title'  => '回到首页',
                'target' => '_blank'
            ]
        ],
        [
            'id'    => 'custom_new_title',
            'title' => '新建文章',
            'group' => null,
            'href'  => admin_url('edit.php'),
            'meta'  => [
                'class'  => 'custom_new_title',
                'target' => '_self'
            ]
        ]
		// 菜单栏(只显示图标)
		// [
            // 'id'    => 'custom_new_img',
            // 'title' => __( '<img src="https://bpic.51yuansu.com/pic3/cover/04/03/36/659237d7f2717_260.jpg" width="15" height="10" />' ),
            // 'href'  => 'https://baidu.com',
            // 'meta'  => ['target' => '_blank']
        // ]
    ];
	
	// 子菜单项配置
    $submenu_item = [
        [
            'id'     => 'custom_new_submenu',
            'parent' => 'custom_home_link',
            'title'  => '子菜单项1',
            'href'   => admin_url()
        ],
        [
            'id'     => 'custom_new_submenu2',
            'parent' => 'custom_home_link',
            'title'  => '子菜单项2',
            'href'   => admin_url()
        ]
    ];

	// （位于用户账户右侧）
    $node_item = [
        [
            'id'     => 'custom-home-link',
			'parent' => 'user-actions',  // 附加到用户账户子菜单
			'title'  => '前台首页1',
			'href'   => home_url(),
			'meta'   => [
				'target' => '_blank',
				'class'  => 'custom-home-link'
			]
        ],
		[
            'id'     => 'custom-home-link2',
			'parent' => 'user-actions',
			'title'  => '前台首页2',
			'href'   => home_url(),
			'meta'   => [
				'target' => '_blank',
				'class'  => 'custom-home-link'
			]
        ],
    ];

	// 批量添加主菜单项
    foreach ($main_menu_item as $args) {
        $wp_admin_bar->add_menu($args);
    }
    
    // 批量添加子菜单项
    foreach ($submenu_item as $args) {
        $wp_admin_bar->add_menu($args);
    }

	// 批量添加子菜单项
    foreach ($node_item as $args) {
        $wp_admin_bar->add_node($args);
    }

}
add_action('admin_bar_menu', 'custom_toolbar_link', 100);
/* add_action # 后面的数字表示位置： 
10 = 在 Logo 的前面 
15 = 在 logo 和 网站名之间 
25 = 在网站名后面 
100 = 在菜单的最后面
*/



/* 
 * 将链接从左边移动到右边
 * 添加自定义CSS到后台头部
 * #wpadminbar #wp-admin-bar-(id) 
 */ 
function custom_site_name_style() {
    // 
    echo '<style type="text/css">
		#wpadminbar *, #wpadminbar{ color:#ffffff;text-shadow:#444444 0 -1px 0; }  
        #wpadminbar{  
            background-color:#003399;  
            background-image:-ms-linear-gradient(bottom,#000033,#003399 5px);  
            background-image:-moz-linear-gradient(bottom,#000033,#003399 5px);  
            background-image:-o-linear-gradient(bottom,#000033,#003399 5px);  
            background-image:-webkit-gradient(linear,left bottom,left top,from(#000033),to(#003399));  
            background-image:-webkit-linear-gradient(bottom,#000033,#003399 5px);  
            background-image:linear-gradient(bottom,#000033,#003399 5px);  
        }
        #wp-admin-bar-site-name a {
            color: #2271b1 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            padding: 0 8px !important;
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
            transition: all 0.2s ease;
        }
		#wp-admin-bar-custom_new_title{
			float:right !important;
		}
    </style>';
}
// 挂载到admin_head钩子
// add_action('admin_head', 'custom_site_name_style');





// ========================================
// . 后台页脚信息定制 .
// ========================================
function change_admin_footer() {
	
	// 排除特定页面
    if (isset($_GET['page']) && $_GET['page'] === 'theme-options') {
        return;
    }
	
    $footer_info = my_option('opt_footerinfo_fieldset');
    
    if (empty($footer_info)) {
        return;
    }
    
    // 修改左侧页脚文本
    add_filter('admin_footer_text', function() use ($footer_info) {
        return $footer_info['opt_footer_info_left'] ?? '';
    }, 99);
    
    // 修改右侧版本信息
    add_filter('update_footer', function() use ($footer_info) {
        return $footer_info['opt_footer_info_right'] ?? '';
    }, 99);
}
add_action('admin_init', 'change_admin_footer');


