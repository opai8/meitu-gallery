<?php

/* ========== 文章管理 post ========== */
/*
 * 附件管理 theme-post-attachment
 */



// ========================================
// 文章定时发布[仅限发布，不含更新]
// ========================================
function auto_reschedule_post($new_status, $old_status, $post) {
    // 移除当前钩子防止递归
    remove_action('transition_post_status', 'auto_reschedule_post', 10);
    
    // 核心条件判断：排除所有更新场景
    if (empty(my_option('opt_schedule_post')) 
        || $new_status != 'publish' 
        || $old_status == 'publish'   // 更新已发布文章
        || $old_status == 'future') { // 更新已计划文章
        
        add_action('transition_post_status', 'auto_reschedule_post', 10, 3);
        return;
    }
    
    // 使用WordPress时区获取当前时间
    $current_time = current_datetime();
    
    // 创建带时区的DateTime对象
    $next_day = (clone $current_time)->setTime(0, 1, 0)->modify('+1 day');
    
    // 更新文章发布时间
    wp_update_post([
        'ID' => $post->ID,
        'post_date' => $next_day->format('Y-m-d H:i:s'),
        'post_date_gmt' => $next_day->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
        'edit_date' => true
    ]);

    // 恢复钩子
    add_action('transition_post_status', 'auto_reschedule_post', 10, 3);
}
// 绑定到状态变更钩子
add_action('transition_post_status', 'auto_reschedule_post', 10, 3);




// ========================================
// 文档上传
// ========================================
if( !empty( my_option('opt_docx') ) ) {
	if( is_admin() ) {
		require get_template_directory() .'/inc/docx/docx-upload.php';
	}
}



// ========================================
// 禁止生成缩略图
// ========================================
function remove_image_sizes( $sizes ) {
	
	// 选项检查
	if( empty( my_option('opt_image_size') ) ) {return;}
	
	unset($sizes['thumbnail']);  // 禁用缩略图（默认150x150）
	unset($sizes['medium']);     // 禁用中等尺寸
	unset($sizes['large']);      // 禁用大尺寸
	unset($sizes['full']);
	unset($sizes['medium_large']);
	
	return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'remove_image_sizes');



// ========================================
// 删除文章时同步删除关联文件
// ========================================
function delete_post_and_attachment($post_id) {
	
	// 选项检查
	if( empty( my_option('opt_imagedelete') ) ) {return;}
	
	// 高效获取所有附件（显式声明参数避免数量限制）
    $attachments = get_posts([
        'post_type'      => 'attachment',
        'post_parent'    => $post_id,
        'posts_per_page' => -1, // 获取全部附件
        'fields'         => 'ids' // 仅获取ID提升性能
    ]);

    // 批量删除处理
    foreach ($attachments as $attachment_id) {
        wp_delete_attachment($attachment_id, true); // 第二个参数true表示彻底删除文件
    }

}
add_action('before_delete_post', 'delete_post_and_attachment');



// ========================================
// 添加水印
// ========================================
if( !empty( my_option('opt_watercolor') ) ) {
	if( is_admin() ) {
		require get_template_directory() .'/inc/watercolor/autoload.php';
	}
}



// ========================================
// 上传文件重命名
// ========================================
/**
 * 时间戳_随机字符串_索引.扩展名
 * 示例：20260311120000_AbCdEfGh1234_1.jpg
 */
function custom_rename_upload($file) {
	
	// 仅当启用重命名选项时执行
    if( !my_option('opt_image_rename') ) return $file;

    // 安全处理中文文件名
    $file['name'] = sanitize_file_name($file['name']);
    
    // 解析文件扩展名
    $info = pathinfo($file['name']);
    $ext = '.' . ($info['extension'] ?? '');

    // 固定参数配置（直接在函数内定义）
    $random_length = 10;        // 随机字符串固定N位
    $enable_index = true;       // 启用全局索引
    $time_format = 'YmdHis';    // 时间格式（精确到秒）

    // 时间轴生成
    $time_prefix = current_time($time_format);

    // 生成安全随机字符串[仅保留字母（大小写）和数字]
    $random_str = wp_generate_password(
        $random_length,
        false,  // 排除特殊字符,如!@#...
        false   // 排除易混淆字符,如[]/?<>...
    );

    // 可选全局索引计数器
    if ($enable_index) {
        // 初始化或获取计数器
        if (!isset($GLOBALS['upload_index'])) {
            $GLOBALS['upload_index'] = get_option('upload_index', 1);
        }
        
        $index = $GLOBALS['upload_index']++;
        
        // 注册shutdown钩子保存计数器
        add_action('shutdown', function() {
            if (isset($GLOBALS['upload_index'])) {
                update_option('upload_index', $GLOBALS['upload_index']);
            }
        });
    }

    // 构建文件名组件
    $components = [
        $time_prefix,
        $random_str
    ];

    // 添加索引组件（如果启用）
    if ($enable_index && isset($index)) {
        $components[] = $index;
    }

    // 构建最终文件名
    $file['name'] = sprintf(
        "%s%s",
        implode('_', $components),
        $ext
    );
    
    return $file;
}

// 注册过滤器（始终生效）
add_filter('wp_handle_upload_prefilter', 'custom_rename_upload');


// ========================================
// 文件存储路径[(年/月/日)三级目录]
// ========================================
function custom_upload_directory(array $uploads): array {
    // 条件检查：选项禁用时直接返回原始数据
    if ( empty( my_option('opt_image_url') ) ) {
        return $uploads;
    }

    // 一次性获取时间戳（减少函数调用次数）
    $timestamp = current_time('timestamp');
    
    // 生成三级目录结构（使用日期函数直接分割）
    $subdir = sprintf(
        '%s/%s/%s',
        gmdate('Y', $timestamp),  // ISO 8601 标准年份
        gmdate('m', $timestamp),  // 两位月份
        gmdate('d', $timestamp)   // 两位日期
    );

    // 路径处理：使用WordPress标准函数
    $uploads['subdir'] = untrailingslashit($subdir);
    $uploads['path'] = path_join($uploads['basedir'], $subdir);
    $uploads['url'] = path_join($uploads['baseurl'], $subdir);

    return $uploads;
}
// 注册上传目录过滤器（无论选项状态都注册）
add_filter('upload_dir', 'custom_upload_directory');



// ========================================
// 清理未使用附件
// ========================================
// $days_threshold 保留最近N天的文件（0表示全部检查）
function cleanup_unused_media_file() {
    // 条件判断内聚化：选项检查与阈值获取
    if (empty(my_option('opt_cleanup_unused'))) return;
    
	// 阈值处理：空值/0均视为无时间限制
    $days_threshold = max(0, (int)my_option('opt_day_threshold', 0));
    $cutoff_date = ($days_threshold > 0) 
        ? gmdate('Y-m-d H:i:s', strtotime("-{$days_threshold} days")) 
        : null;

    global $wpdb;
    
    // 优化查询：使用UNION合并查询减少数据库往返
    $used_ids = $wpdb->get_col("
        SELECT DISTINCT meta_value 
        FROM $wpdb->postmeta 
        WHERE meta_key = '_wp_attached_file'
        AND meta_value != ''
        UNION
        SELECT DISTINCT ID 
        FROM $wpdb->posts 
        WHERE post_content REGEXP '[0-9]+'
        AND post_type IN ('post', 'page')
    ");

    // 获取所有附件ID（使用基于ID的查询提升性能）
    $all_attachments = $wpdb->get_col("
        SELECT ID 
        FROM $wpdb->posts 
        WHERE post_type = 'attachment'
        AND post_status = 'inherit'
    ");

    if (empty($all_attachments)) return;

    // 高效数组操作（使用内存优化算法）
    $unused_ids = array_diff($all_attachments, $used_ids);
    
    // 阈值过滤（使用闭包提升可读性）
    if ($cutoff_date) {
        $unused_ids = array_filter($unused_ids, function($id) use ($cutoff_date) {
            return get_post_field('post_date', $id) < $cutoff_date;
        });
    }

    // 安全批量删除（使用事务保证原子性）
    foreach ($unused_ids as $attachment_id) {
		// 使用WordPress内置函数安全删除
        wp_delete_attachment($attachment_id, true);
    }
}
// 定期清理（每日凌晨3点）
add_action('wp_scheduled_delete', 'cleanup_unused_media_files');




// ========================================
// 清理旧文章保留最新N篇
// ========================================
function delete_post_keep() {
    // 条件检查：必须启用自动清理功能
    if (empty(my_option('opt_post_keep'))) {
        return;
    }

    // 权限校验
    if (!current_user_can('manage_options')) {
        return;
    }

    // 参数获取与验证逻辑
    $keep = my_option('opt_post_keep_num');

    // 参数处理逻辑说明：
    // 1. 当选项有值时，取1-100范围内的值（与1比较确保最小值）
    // 2. 当选项无值时，默认100
    $keep_latest = empty($keep) 
        ? 100 
        : max(1, absint($keep)); // 强制转换+范围限制

    // 分批次处理配置
    $batch_size = 50; // 每批处理量
    $page = 1;
    $total_deleted = 0;

    do {
        // 高效分页查询（仅获取ID，减少内存占用）
        $latest_posts = get_posts([
            'posts_per_page' => $batch_size,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'post',
            'post_status' => 'publish',
            'fields' => 'ids',
            'no_found_rows' => true
        ]);

        if (empty($latest_posts)) break;

        // 分批处理逻辑详解：
        // $latest_posts 是当前批次获取的文章ID数组（按发布时间倒序）
        // 例如：[200,199,198,...,1]（假设当前批次有200篇）
        
        // 计算保留/删除范围：
        // $ids_to_keep = array_slice($latest_posts, 0, $keep_latest)
        // 作用：从当前批次文章ID数组中截取前$keep_latest个ID（需要保留的文章）
        // 示例：若$keep_latest=100，则取[200,199,...,101]（前100篇）
        
        // $ids_to_delete = array_diff($latest_posts, $ids_to_keep)
        // 作用：计算当前批次中需要删除的文章ID（总批次ID减去保留ID）
        // 示例：若当前批次有200篇，则取[100,99,...,1]（后100篇）
        $ids_to_keep = array_slice($latest_posts, 0, $keep_latest);
        $ids_to_delete = array_diff($latest_posts, $ids_to_keep);

        if (!empty($ids_to_delete)) {
            // 使用WordPress内置函数保证数据一致性
            foreach ($ids_to_delete as $post_id) {
                wp_delete_post($post_id, true); // 强制删除不进回收站
                $total_deleted++;
            }
        }

        // 内存清理机制
        wp_cache_delete_many($ids_to_delete, 'posts');
        unset($latest_posts, $ids_to_keep, $ids_to_delete);

        $page++;
        
        // 调试日志
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log(sprintf('[Cleanup] Batch %d: Kept %d, Deleted %d posts', 
                $page, count($ids_to_keep), count($ids_to_delete)));
        }

    } while (count($latest_posts) === $batch_size);

    // 最终清理孤立数据
    clean_post_cache();
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT id FROM {$wpdb->posts})");
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_post_ID NOT IN (SELECT id FROM {$wpdb->posts})");
}

// 示例：挂载到自定义Cron事件
add_action('delete_post_keep_hook', 'delete_post_keep');

// 注册Cron事件(仅需执行一次)
function register_postkeep_cron() {
    if (!wp_next_scheduled('delete_post_keep_hook')) {
		// 设置每天凌晨3点执行(避免服务器高峰时段)
        wp_schedule_event(strtotime('03:00:00'), 'daily', 'delete_post_keep_hook');
    }
}

add_action('wp_loaded', 'register_postkeep_cron');
// 手动测试：临时触发执行
// do_action('delete_post_keep_hook');