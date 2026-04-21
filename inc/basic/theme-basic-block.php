<?php

/* ========== 基础设置 basic ========== */

/*
 * 功能屏蔽 theme-basic-block
 */

// 防止直接访问
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/*
 * 钩子注册（统一入口点）
 */

// 后台专属功能
add_action( 'admin_init', function() {
	remove_opt_func();
	disable_opt_transform();
	remove_opt_embed();	
	disable_opt_editor();
});

// 前端专属功能
add_action( 'init', function() {
	disable_opt_common();
	disable_opt_frontend();
});

// 后台清理操作
add_action( 'wp_dashboard_setup', 'remove_opt_backend');

// 移除后台菜单的操作
add_action( 'admin_menu', 'remove_opt_page');




// ========================================
// 常规功能
// * 包括：文章修订、草稿清理、XML-RPC、自动更新、邮箱验证
// ========================================
function disable_opt_common() {
    // 获取选项值
    $option = my_option( 'opt_block_common' );
    
    // 确保返回数组
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }
    
    // 根据选项启用对应功能
    if ( in_array( 'opt_revise', $option ) ) {
        disable_post_revise();
    }
	
	if ( in_array( 'opt_revision', $option ) ) {
        // 注意：此函数会删除数据库数据，需谨慎使用
        disable_post_revision(); 
    }
    
    if ( in_array( 'opt_draft', $option ) ) {
        // 注意：此函数会删除数据库数据，需谨慎使用
        disable_draft_data(); 
    }
    
    if ( in_array( 'opt_xmlrpc', $option ) ) {
        disable_xmlrpc();
    }
    
    if ( in_array( 'opt_auto_update', $option ) ) {
        disable_auto_update();
    }
    
    if ( in_array( 'opt_email_check', $option ) ) {
        disable_email_check();
    }
	
	if ( in_array( 'opt_screen_option', $option ) ) {
        remove_screen_option();
    }
}

// ========================================
// 函数禁用
// * 禁止访问 WordPress.org 获取版本/翻译信息
// ========================================
function remove_opt_func() {
    $option = my_option( 'opt_block_func' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_translations_api', $option ) ) {
        remove_translations_api();
    }
    
    if ( in_array( 'opt_wp_phpv', $option ) ) {
        remove_wp_phpv();
    }
    
    if ( in_array( 'opt_check_browserv', $option ) ) {
        remove_check_browserv();
    }
}

// ========================================
// 转换功能
// * 包括：Emoji、字符格式化、大小写修正
// ========================================
function disable_opt_transform() {
    $option = my_option( 'opt_block_transform' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_emoji', $option ) ) {
        disable_emoji();
    }
    
    if ( in_array( 'opt_wptexturize', $option ) ) {
        disable_wptexturize();
    }
    
    if ( in_array( 'opt_capitalization', $option ) ) {
        disable_capitalization();
    }
}

// ========================================
// 前台优化
// * 包括：Trackback、Feed、样式优化
// ========================================
function disable_opt_frontend() {
    $option = my_option( 'opt_block_frontend' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_trackback', $option ) ) {
        disable_trackback();
    }
    
    if ( in_array( 'opt_feed', $option ) ) {
        disable_feed();
    }
    
    if ( in_array( 'opt_unuse_style', $option ) ) {
        disable_unuse_style();
    }
}

// ========================================
// 后台功能
// * 包括：工具栏、仪表盘等
// ========================================
function remove_opt_backend() {
    $option = my_option( 'opt_block_backend' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_home_bar', $option ) ) {
        remove_home_bar();
    }
    
    if ( in_array( 'opt_admin_bar', $option ) ) {
        remove_admin_bar();
    }
    
    if ( in_array( 'opt_dashboard_tool', $option ) ) {
        remove_dashboard_tool();
    }

}

// ========================================
// 页面功能
// * 移除不需要的后台菜单项和页面
// ========================================
function remove_opt_page() {
    $option = my_option( 'opt_block_page' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_theme_menu', $option ) ) {
        remove_theme_menu();
    }
    
    if ( in_array( 'opt_site_editor', $option ) ) {
        remove_site_editor();
    }
    
    if ( in_array( 'opt_customize_manage', $option ) ) {
        remove_customize_manage();
    }
    
    if ( in_array( 'opt_theme_editor', $option ) ) {
        remove_theme_editor();
    }
    
    if ( in_array( 'opt_site_health', $option ) ) {
        remove_site_health();
    }
    
    if ( in_array( 'opt_gdpr_privacy', $option ) ) {
        remove_gdpr_privacy();
    }
	
	if ( in_array( 'opt_editor_metabox', $option ) ) {
        // remove_editor_metabox();
    }
	
}

// ========================================
// 嵌入功能
// * 禁用 Auto Embeds 和 WordPress Embed
// ========================================
function remove_opt_embed() {
    $option = my_option( 'opt_block_embed' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_auto_embed', $option ) ) {
        disable_auto_embed();
    }
    
    if ( in_array( 'opt_wp_embed', $option ) ) {
        disable_wp_embed();
    }
}

// ========================================
// 古腾堡编辑器
// * 切换回经典编辑器和经典小工具
// ========================================
function disable_opt_editor() {
    $option = my_option( 'opt_block_editor' );
    
    if ( empty( $option ) || ! is_array( $option ) ) {
        return;
    }

    if ( in_array( 'opt_gutenberg_editor', $option ) ) {
        disable_gutenberg_editor();
    }
    
    if ( in_array( 'opt_widget_editor', $option ) ) {
        disable_widget_editor();
    }
}





// ========================================
// . 屏蔽文章修订功能 .
// ========================================
function disable_post_revise() {
    // ==========================================================
    // 第一部分：禁用修订功能
    // ==========================================================
    
    // 1. 定义常量彻底禁用（如果 wp-config.php 未定义）
    if ( ! defined( 'WP_POST_REVISIONS' ) ) {
        define( 'WP_POST_REVISIONS', false );
    }

    // 2. 禁用自动保存
    add_filter( 'autosave_interval', '__return_false' );

    // 3. 移除自动保存 JS 脚本
    add_action( 'admin_print_scripts', function() {
        wp_deregister_script( 'autosave' );
    });

    // 4. 移除编辑器中的修订版本元框
    add_action( 'admin_menu', function() {
        remove_meta_box( 'revisionsdiv', 'post', 'normal' );
        remove_meta_box( 'revisionsdiv', 'page', 'normal' );
        
        // 支持自定义文章类型
        $post_types = get_post_types( array( 'public' => true ), 'names' );
        foreach ( $post_types as $post_type ) {
            remove_meta_box( 'revisionsdiv', $post_type, 'normal' );
        }
    });

    // 5. 保存文章时移除修订版本动作
    add_action( 'save_post', function( $post_id, $post ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        remove_action( 'pre_post_update', 'wp_save_post_revision' );
    }, 10, 2 );

    // 6. 禁用 REST API 修订版本端点
    add_filter( 'rest_prepare_revision', '__return_null' );
    
    // 7. 设置保留修订版本数量为 0
    add_filter( 'wp_revisions_to_keep', '__return_zero', 10, 2 );    
}

// ========================================
// . 清理所有修订版本 .
// ========================================
function disable_post_revision() {
	// 仅在管理员访问后台时执行一次清理
    if ( is_admin() && current_user_can( 'manage_options' ) ) {
        global $wpdb;
        
        // 优化方案：使用单条 SQL 删除，避免多次查询
        // 先删除 postmeta 中的孤立数据（更高效的方式）
        $wpdb->query( "DELETE pm FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.ID IS NULL" );
        
        // 删除所有修订版本（使用 LIMIT 分批处理，避免锁表）
        // 但为了效率，如果数据量不大，可以一次性删除
        $deleted = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'revision'" );
        
        // 只有在实际删除了数据时才标记为已清理
        if ( $deleted > 0 ) {
            // 优化表（可选，根据需要开启）
            $wpdb->query( "OPTIMIZE TABLE {$wpdb->posts}" );
            $wpdb->query( "OPTIMIZE TABLE {$wpdb->postmeta}" );
           
            // 可选：记录清理数量（用于调试）
            // update_option( 'dpr_cleaned_count', $deleted );
        }
    }
}

// ========================================
// . 删除所有自动保存草稿 .
// ========================================
function disable_draft_data() {
	
	// 仅在后台环境执行
    if (!is_admin() || !current_user_can('manage_options')) return;
	
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
    // 层次 1: 核心功能禁用
    add_filter( 'pings_open', '__return_false', 100 ); // 阻止新文章接收 ping
    add_filter( 'trackback_enabled', '__return_false' ); // 禁用 trackback 功能
    add_filter( 'xmlrpc_methods', function( $methods ) {
        unset( $methods['pingback.ping'] );
        unset( $methods['pingback.extensions.getPingbacks'] );
        return $methods;
    });

    // 层次 2: 请求拦截层 (防止扫描和利用)
    add_action( 'template_redirect', function() {
        // 拦截所有 Trackback/Pingback 特征请求
        if ( isset( $_GET['tb_id'] ) || isset( $_POST['tb_id'] ) || 
             preg_match( '/trackback\?/i', $_SERVER['REQUEST_URI'] ) ||
             ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
            wp_die(
                '本站已彻底禁用 Trackback/Pingback 功能',
                '服务已禁用',
                [ 'response' => 403, 'back_link' => false ]
            );
        }
    });

    // 层次 3: 接口层禁用 (REST API)
    add_filter( 'rest_endpoints', function( $routes ) {
        foreach ( $routes as $route => $callback ) {
            if ( preg_match( '/(trackback|pingback)/i', $route ) ) {
                unset( $routes[ $route ] );
            }
        }
        return $routes;
    });

    // 层次 4: UI 层移除 (前后台)
    add_action( 'admin_init', function() {
        remove_meta_box( 'trackbacksdiv', [ 'post', 'page', 'link' ], 'normal' );
        
        // 移除讨论设置中的 Trackback 选项 (WordPress 5.0+)
        add_filter( 'discussion_settings', function( $fields ) {
            unset( $fields['trackback_enabled'] );
            unset( $fields['pingback_enabled'] ); // 通常一并禁用
            return $fields;
        }, 10, 1 );
    });

    // 层次 5: 数据库清理 (可选，执行前务必备份)
    // 要启用此功能，请取消下面代码的注释。建议在首次优化时手动执行一次。
    /*
    add_action( 'admin_init', function() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->comments} WHERE comment_type IN ('trackback', 'pingback')" );
		$wpdb->query( "DELETE FROM {$wpdb->commentmeta} WHERE comment_id NOT IN (SELECT comment_ID FROM {$wpdb->comments})" );
    });
    */
}

// ========================================
// . 彻底禁用并隐藏XML-RPC功能 .
// ========================================
function disable_xmlrpc() {
    // 层1：核心禁用
    add_filter('xmlrpc_enabled', '__return_false', 1);
    
    // 层2：请求拦截
    add_action('parse_request', function() {
        if (basename($_SERVER['PHP_SELF']) === 'xmlrpc.php') {
            wp_die('XML-RPC已禁用', 403);
        }
    });

    // 层3：头部清理
    remove_action('wp_head', 'rsd_link');
    add_filter('wp_headers', function($headers) {
        unset($headers['X-Pingback']);
        return $headers;
    });
    
    // 层4：REST API防护
    add_filter('rest_endpoints', function($routes) {
        return array_filter($routes, fn($route) => !str_contains($route, 'xmlrpc'));
    });
}

// ========================================
// . 完全禁用WordPress的所有自动更新 .
// ========================================
function disable_auto_update() {
    
    // ==========================================================
    // 第一部分：禁用所有自动更新功能 (过滤器)
    // ==========================================================

    // 1. 禁用核心自动更新 (包括开发版、次要版本、主要版本)
    add_filter( 'auto_update_core', '__return_false' );
    add_filter( 'allow_dev_auto_core_updates', '__return_false' );
    add_filter( 'allow_minor_auto_core_updates', '__return_false' );
    add_filter( 'allow_major_auto_core_updates', '__return_false' );

    // 2. 禁用插件自动更新
    add_filter( 'auto_update_plugin', '__return_false' );

    // 3. 禁用主题自动更新
    add_filter( 'auto_update_theme', '__return_false' );

    // 4. 禁用翻译文件自动更新
    add_filter( 'auto_update_translation', '__return_false' );

    // 5. 【关键】阻止WordPress检查更新 (最高效的禁用方式)
    // 通过返回null，让WordPress认为没有可用的更新
	// 与后面'顶部admin_bar'重叠
    // add_filter( 'pre_site_transient_update_core', '__return_null' );
    // add_filter( 'pre_site_transient_update_plugins', '__return_null' );
    // add_filter( 'pre_site_transient_update_themes', '__return_null' );

    // ==========================================================
    // 第二部分：移除相关的定时任务和通知
    // ==========================================================

    // 1. 移除自动更新相关的Cron任务 (仅后台执行)
    add_action( 'init', function() {
        if ( ! is_admin() ) {
            return;
        }
        wp_clear_scheduled_hook( 'wp_version_check' );
        wp_clear_scheduled_hook( 'wp_update_plugins' );
        wp_clear_scheduled_hook( 'wp_update_themes' );
    });

    // 2. 移除后台的"请更新"通知 (仅后台执行)
    add_action( 'admin_notices', function() {
        // 移除核心更新提醒
        remove_action( 'admin_notices', 'update_nag', 3 );
        // 移除维护模式提醒
        remove_action( 'admin_notices', 'maintenance_nag', 10 );
        // 移除网络后台的更新提醒
        remove_action( 'network_admin_notices', 'update_nag', 3 );
        remove_action( 'network_admin_notices', 'maintenance_nag', 10 );
    });
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

    // 安全移除：版本号暴露（安全风险）
    if (function_exists('wp_generator')) {
        remove_action('wp_head', 'wp_generator');
    }
	// 移除Windows Live Writer支持（如果存在）
    remove_action('wp_head', 'wlwmanifest_link');
    
    // 移除短链接
    remove_action('wp_head', 'wp_shortlink_wp_head');

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
	
	// 移除REST API链接
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
function remove_translations_api() {
	add_filter('translations_api', 'disable_translations_api', 999, 2);
}

// ========================================
// . 禁止 wp_check_php_version .
// ========================================
function remove_wp_phpv() {
	// 直接拦截函数输出
	add_filter('wp_check_php_version', '__return_empty_array');
}

// ========================================
// . 禁止 wp_check_browser_version .
// ========================================
function remove_check_browserv() {
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
                    strpos($menu_item[0], '样板') !== false
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
	
	// 确保仅在后台执行
    if (!is_admin() || !current_user_can('manage_options')) return;
	
	// 第一层：URL强制拦截
	if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/theme-editor.php') !== false) {
        wp_redirect(admin_url(), 301);
        exit;
    }
	
	// 第二层：移除菜单项
	add_action('admin_menu', function() {
        // 移除外观菜单下的主题编辑器子菜单
        remove_submenu_page('themes.php', 'theme-editor.php');
 
        // 强制清理全局菜单变量
		/*
        global $submenu;
        if (isset($submenu['themes.php'])) {
            foreach ($submenu['themes.php'] as $key => $item) {
                if (isset($item[2]) && $item[2] === 'theme-editor.php') {
                    unset($submenu['themes.php'][$key]);
                }
            }
            // 重新索引数组
            $submenu['themes.php'] = array_values($submenu['themes.php']);
			}
		*/
		}, 999); // 高优先级确保最后执行
		
	
	// 第三层：权限彻底过滤（核心安全层）
    add_filter('user_has_cap', function($allcaps, $caps, $args) {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        
        // 拦截主题编辑器页面的所有权限
        if ($page === 'theme-editor.php') {
            // 移除所有相关权限
            $allcaps['edit_themes'] = false;
            $allcaps['edit_files'] = false;
            $allcaps['edit_plugins'] = false;
            $allcaps['manage_options'] = false;
            $allcaps['switch_themes'] = false;
            $allcaps['edit_theme_options'] = false;
            $allcaps['edit_theme_plugin_files'] = false;
        }
        
        // 拦截文件编辑权限
        if (isset($_GET['action']) && $_GET['action'] === 'edit') {
            $allcaps['edit_files'] = false;
            $allcaps['edit_themes'] = false;
        }
        
        return $allcaps;
    }, 10, 3);
	
	// 第四层：清理资源加载
    add_action('admin_enqueue_scripts', function($hook_suffix) {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        
        // 仅在主题编辑器页面清理资源
        if ($page === 'theme-editor.php') {
            // 移除编辑器核心资源
            wp_dequeue_style('theme-editor');
            wp_deregister_style('theme-editor');
            wp_dequeue_script('theme-editor');
            wp_deregister_script('theme-editor');
            
            // 移除代码编辑器资源
            wp_dequeue_script('code-editor');
            wp_deregister_script('code-editor');
            wp_dequeue_style('code-editor');
            wp_deregister_style('code-editor');
            
            // 移除相关依赖
            wp_dequeue_script('wp-theme-plugin-editor');
            wp_deregister_script('wp-theme-plugin-editor');
            wp_dequeue_style('wp-codemirror');
            wp_deregister_style('wp-codemirror');
        }
    }, 999);
	
	// 第五层：禁用保存接口（POST请求）
    add_action('admin_post_edit-theme-plugin-file', function() {
        // 拦截所有文件保存请求
        wp_redirect(admin_url(), 301);
        exit;
    }, 0); // 最高优先级
    
    // 禁用主题编辑器的POST请求
    add_action('admin_post_edit_theme_file', function() {
        wp_redirect(admin_url(), 301);
        exit;
    }, 0);
	
	// 第六层：禁用AJAX接口
	add_action('wp_ajax_edit-theme-plugin-file', function() {
        // 返回JSON错误响应
        wp_send_json_error([
            'message' => '主题编辑器已被禁用',
            'code' => 'editor_disabled',
            'data' => '您没有权限编辑主题文件'
        ], 403);
    }, 0);
    
    // 禁用文件编辑AJAX
    add_action('wp_ajax_edit_theme_file', function() {
        wp_send_json_error([
            'message' => '文件编辑功能已被禁用',
            'code' => 'file_edit_disabled'
        ], 403);
    }, 0);
	
	// 第七层：页面内容锁定（即便进入页面也无法操作）
    add_action('admin_head-theme-editor.php', function() {
        // 注入CSS隐藏编辑器界面
        echo '<style>
            .theme-editor-php #wpbody-content,
            .theme-editor-php .wrap,
            .theme-editor-php #template,
            .theme-editor-php #templateside,
            .theme-editor-php #theme-editor-warning {
                display: none !important;
            }
            .theme-editor-php::before {
                content: "主题编辑器已被禁用";
                display: block;
                padding: 30px;
                background: #d32f2f;
                color: #fff;
                text-align: center;
                font-size: 24px;
                font-weight: bold;
                margin: 20px;
                border-radius: 5px;
            }
        </style>';
        
        // 注入JS阻止所有操作
        echo '<script>
            jQuery(document).ready(function($) {
                // 禁用所有按钮
                $("button, input[type=submit]").prop("disabled", true).css("opacity", "0.5");
                
                // 禁用所有文本框
                $("textarea").prop("readonly", true).css("background", "#f5f5f5");
                
                // 移除所有事件监听器
                $("*").off("click");
                
                // 显示警告信息
                alert("主题编辑器已被管理员禁用！\\n\\n您无法编辑或保存任何文件。");
                
                // 跳转回首页
                setTimeout(function() {
                    window.location.href = "' . admin_url() . '";
                }, 1000);
            });
        </script>';
    }, 999);
	
	// 第八层：清理元数据和用户选项
    add_action('admin_init', function() {
        // 删除用户的主题编辑器相关选项
        $current_user = wp_get_current_user();
        if ($current_user) {
            delete_user_meta($current_user->ID, 'theme_editor_warning_dismissed');
            delete_user_meta($current_user->ID, 'editable_extensions');
        }
        
        // 删除站点选项
        delete_option('theme_editor_warning_dismissed');
        delete_option('can_edit_themes');
    });

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
    // 确保仅在后台执行
    if (!is_admin() || !current_user_can('manage_options')) return;

    // === 菜单项移除 ===
    add_action('admin_menu', 'remove_site_health_menus', 11); // 优先级11确保最后执行
    function remove_site_health_menus() {
        // 移除主菜单和子菜单
        remove_menu_page('site-health.php');
        remove_submenu_page('tools.php', 'site-health.php');
        remove_submenu_page('site-health.php', 'site-health.php'); // 备用移除
    }

    // === 仪表盘面板移除 ===
    add_action('wp_dashboard_setup', 'remove_site_health_dashboard');
    function remove_site_health_dashboard() {
        // 移除所有相关元框
        $meta_boxes = [
            'dashboard_site_health',
            'dashboard_php_nag',
            'dashboard_health_status',
            'dashboard_health_tests'
        ];
        
        foreach ($meta_boxes as $box) {
            remove_meta_box($box, 'dashboard', 'normal');
            remove_meta_box($box, 'dashboard', 'side');
        }
    }

    // === 访问拦截 ===
    add_action('admin_init', 'block_site_health_access');
    function block_site_health_access() {
        // 安全拦截所有相关页面
        if (isset($_GET['page']) && in_array($_GET['page'], [
            'site-health', 
            'site-health.php',
            'health-check',
            'health-check.php'
        ], true)) {
            wp_safe_redirect(admin_url(), 301); // 使用安全重定向
            exit;
        }
    }

    // === 功能禁用 ===
    add_filter('site_health_send_email_notification', '__return_false');
    add_filter('site_health_run_tests', '__return_false');
    add_filter('site_health_collect_data', '__return_false');
    add_filter('site_health_enable_api', '__return_false');
    add_filter('site_health_navigation_tabs', '__return_empty_array');
    add_filter('site_health_error_log', '__return_false');

    // === 定时任务清理 ===
    add_action('init', 'remove_site_health_cron');
    function remove_site_health_cron() {
        wp_clear_scheduled_hook('site_health_cron_hook');
        wp_clear_scheduled_hook('health-check-site-status');
    }

    // === 资源移除 ===
    add_action('admin_enqueue_scripts', 'dequeue_site_health_assets');
    function dequeue_site_health_assets($hook) {
        // 精确匹配相关页面
        if (strpos($hook, 'site-health') !== false || 
            strpos($hook, 'health-check') !== false) {
            
            // 移除主脚本/样式
            wp_dequeue_script('site-health');
            wp_dequeue_style('site-health');
            
            // 移除内联脚本
            wp_add_inline_script('site-health', 
                'console.warn("Site Health scripts blocked for security");', 
                'before'
            );
        }
    }

    // === 屏幕访问拦截 ===
    add_action('current_screen', 'block_site_health_screen');
    function block_site_health_screen() {
        $screen = get_current_screen();
        if ($screen && in_array($screen->id, [
            'site-health', 
            'dashboard_page_site-health',
            'health-check'
        ])) {
            wp_safe_redirect(admin_url(), 301);
            exit;
        }
    }

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
// . 移除[显示帮助]映射在经典编辑器下方的字段 .
// ========================================
function remove_editor_metabox() {echo '996';
	// 移除摘要元框
    remove_meta_box('postexcerpt', 'post', 'normal');
    
    // 移除讨论元框（评论/引用）
    remove_meta_box('commentstatusdiv', 'post', 'normal');
    remove_meta_box('commentsdiv', 'post', 'normal');
    
    // 移除作者元框
    remove_meta_box('authordiv', 'post', 'normal');
    
    // 移除自定义字段元框
    remove_meta_box('postcustom', 'post', 'normal');
    
    // 移除别名元框
    remove_meta_box('slugdiv', 'post', 'normal');
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


