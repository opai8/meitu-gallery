<?php

// 防止直接访问
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



/**
 * =============================================================
 * SEO 优化函数集
 * =============================================================
 * 
 */

/**
 * 获取SEO关键字
 * 
 */
function get_seo_keywords() {
    // 先尝试获取自定义关键字
    $custom_keywords = my_option('seo_keyword');
    
    // 检查是否设置了自定义关键字（包括空字符串）
    if (false !== $custom_keywords) {
        // 如果用户明确设置了空字符串，返回空字符串
        // 否则返回自定义关键字
        return $custom_keywords;
    }
    
    // 获取站点名称和描述
    $blog_name = get_bloginfo('name');
    $blog_description = get_bloginfo('description');
    
    // 组合默认关键字
    $default_keywords = $blog_name;
    if (!empty($blog_description)) {
        $default_keywords .= ', ' . $blog_description;
    }
    
    return $default_keywords;
}

/**
 * 获取SEO标题
 * 
 */
function get_seo_title() {
    // 先尝试获取自定义标题
    $custom_title = my_option('seo_title');
    
    // 检查是否设置了自定义标题（包括空字符串）
    if (false !== $custom_title) {
        // 如果用户明确设置了空字符串，返回空字符串
        // 否则返回自定义标题
        return $custom_title;
    }
    
    // 如果没有自定义标题，返回WordPress生成的标题
    return wp_get_document_title();
}

/**
 * 获取SEO描述
 * 
 */
function get_seo_description() {
    // 先尝试获取自定义描述
    $custom_description = my_option('seo_description');
    
    // 检查是否设置了自定义描述（包括空字符串）
    if (false !== $custom_description) {
        // 如果用户明确设置了空字符串，返回空字符串
        // 否则返回自定义描述
        return $custom_description;
    }
    
    // 如果没有自定义描述，返回站点描述
    return get_bloginfo('description');
}

/**
 * 输出SEO meta标签（关键字、描述和标题）到wp_head
 */
function output_seo_meta() {
    // 获取值
    $keywords = get_seo_keywords();
    $description = get_seo_description();
    $title = get_seo_title();
    
    // 标题标签 (非常重要)
    if (!empty($title)) {
        echo '<title>' . esc_html($title) . '</title>' . "\n";
    }
    
    // 关键字 Meta (注意：Google等主要搜索引擎已不再将此作为排名因素，但保留无害)
    if (!empty($keywords)) {
        echo '<meta name="keywords" content="' . esc_attr($keywords) . '" />' . "\n";
    }
    
    // 描述 Meta (非常重要)
    if (!empty($description)) {
        echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
    }
}
add_action('wp_head', 'output_seo_meta');





/**
 * 通过图片 URL 获取附件 ID
 *
 * 优先通过 GUID 快速查找，失败后通过 _wp_attached_file 元数据进行更可靠的查找。
 * 使用 $wpdb->prepare() 防止 SQL 注入。
 *
 * @param string $link 图片 URL
 * @return int|null 附件 ID（失败返回 null）
 */
function meitu_get_attachment_id_from_src( $link ) {
    global $wpdb;

    // 1. 清理 URL：移除域名和尺寸参数
    $link = str_replace( home_url( '/', 'relative' ), '', $link );
    $link = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|webp)$)/i', '', $link );
    $link = ltrim( $link, '/' );

    if ( empty( $link ) ) {
        return null;
    }

    // 2. 优先尝试通过 GUID 快速查找
    $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s", $link ) );
    if ( $attachment_id ) {
        return (int) $attachment_id;
    }

    // 3. 通过 _wp_attached_file 元数据进行可靠查找
    $attachment_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
        WHERE meta_key = '_wp_attached_file'
        AND meta_value = %s",
        $link
    ) );

    return $attachment_id ? (int) $attachment_id : null;
}

/**
 * 获取 WordPress 最新一篇已发布文章的所有图片（不含特色图片）
 * 
 * @param int  $limit                获取数量（默认 50，传入 null 或 0 表示获取全部）
 * @param bool $exclude_featured_image 是否排除特色图片（默认 false，即包含）
 * @return array 图片资源数组
 * 
 * 返回格式：
 * array(
 *     array(
 *         'url'       => '缩略图URL',
 *         'full_url'  => '大图URL',
 *         'title'     => '图片标题',
 *         'alt'       => '替代文本',
 *     ),
 *     ...
 * )
 */
function meitu_get_image( $limit = 50, $exclude_featured_image = false ) {
    // 1. 获取最新一篇已发布文章
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return array();
    }

    $query->the_post();
    $post_id = get_the_ID();
    $post_title = get_the_title();
    $images = array();
    $image_count = 0;

    // 2. 获取文章内容和特色图片 ID
    $post = get_post( $post_id );
    $content = $post->post_content;
    $featured_image_id = get_post_thumbnail_id( $post_id );

    // 3. 确定实际限制数量
    // 如果 $limit 为 null、0 或负数，表示获取全部图片
    $actual_limit = ( $limit && is_numeric( $limit ) && $limit > 0 ) ? (int) $limit : PHP_INT_MAX;

    // 4. 正则匹配所有 img 标签的 src 属性
    preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches );

    if ( ! empty( $matches[1] ) ) {
        foreach ( $matches[1] as $src ) {
            // 达到限制数量则停止
            if ( $image_count >= $actual_limit ) {
                break;
            }

            // 清理 URL 以便查询
            $clean_src = str_replace( home_url( '/', 'relative' ), '', $src );
            $clean_src = ltrim( $clean_src, '/' );
            $clean_src = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|webp)$)/i', '', $clean_src );

            $attachment_id = meitu_get_attachment_id_from_src( $clean_src );

            if ( $attachment_id ) {
                // 检查是否是特色图片，并根据参数决定是否跳过
                if ( $exclude_featured_image && $attachment_id == $featured_image_id ) {
                    continue;
                }

                // 生成缩略图和大图 URL
                $thumbnail_url = wp_get_attachment_image_url( $attachment_id, 'medium' );
                $full_url = wp_get_attachment_image_url( $attachment_id, 'meitu-large' ) ?: wp_get_attachment_image_url( $attachment_id, 'full' ) ?: $src;

                $images[] = array(
                    'url'       => $thumbnail_url ?: $src,
                    'full_url'  => $full_url,
                    'title'     => $post_title,
                    'alt'       => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: $post_title,
                );
            } else {
                // 外部图片或无法识别的图片
                $images[] = array(
                    'url'       => $src,
                    'full_url'  => $src,
                    'title'     => $post_title,
                    'alt'       => $post_title,
                );
            }

            $image_count++;
        }
    }

    // 5. 添加特色图片（如果存在、未被排除且未达到限制）
    if ( $featured_image_id && ! $exclude_featured_image && $image_count < $actual_limit ) {
        $thumbnail_url = wp_get_attachment_image_url( $featured_image_id, 'medium' );
        $full_url = wp_get_attachment_image_url( $featured_image_id, 'meitu-large' ) ?: wp_get_attachment_image_url( $featured_image_id, 'full' ) ?: get_the_post_thumbnail_url( $post_id, 'full' );

        $images[] = array(
            'url'       => $thumbnail_url ?: get_the_post_thumbnail_url( $post_id, 'medium' ),
            'full_url'  => $full_url,
            'title'     => $post_title,
            'alt'       => get_post_meta( $featured_image_id, '_wp_attachment_image_alt', true ) ?: $post_title,
        );
        $image_count++;
    }

    wp_reset_postdata();

    return $images;
}

/**
// 1. 直接调用，使用默认限制 50 张
$images = meitu_get_image();

// 2. 获取全部图片（传入 null 或 0）
$all_images = meitu_get_image( null );
// 或者
$all_images = meitu_get_image( 0 );

// 3. 获取指定数量的图片（如 100 张）
$images_100 = meitu_get_image( 100 );

// 4. 获取指定数量且不包含特色图片
$images_no_featured = meitu_get_image( 30, true );

// 5. 获取全部图片且不包含特色图片
$all_images_no_featured = meitu_get_image( null, true );
**/
