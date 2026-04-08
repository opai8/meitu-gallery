<?php
/**
 * 美图库主题函数文件 - 单文章图集版
 *
 * @package 美图库
 * @version 1.0.0
 */

// 防止直接访问
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 主题设置
 */
function meitu_setup() {
    // 让主题支持翻译
    load_theme_textdomain( 'meitu', get_template_directory() . '/languages' );

    // 添加默认主题支持
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    // 注册导航菜单
    register_nav_menus( array(
        'primary' => esc_html__( '主导航菜单', 'meitu' ),
        'footer'  => esc_html__( '页脚菜单', 'meitu' ),
    ) );

    // 设置缩略图默认尺寸
    set_post_thumbnail_size( 400, 300, true );
    add_image_size( 'meitu-large', 1200, 0, false ); // 大图尺寸
    add_image_size( 'meitu-thumb', 400, 0, false );  // 缩略图尺寸
}
add_action( 'after_setup_theme', 'meitu_setup' );

/**
 * 加载主题样式和脚本
 */
function meitu_scripts() {
    // 加载主样式表
    wp_enqueue_style( 'meitu-style', get_stylesheet_uri(), array(), '1.0.0' );

    // 加载 jQuery（CDN）
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', get_template_directory_uri() . '/asset/jquery-3.7.1.min.js', array(), '3.7.1', true );
    wp_enqueue_script( 'jquery' );

    // 加载主题脚本
    wp_enqueue_script( 'meitu-script', get_template_directory_uri() . '/asset/script.js', array( 'jquery' ), '1.0.0', true );

    // 传递 AJAX URL 到前端
    wp_localize_script( 'meitu-script', 'meituAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'meitu-nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'meitu_scripts' );

/**
 * 注册侧边栏
 */
function meitu_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( '侧边栏', 'meitu' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( '在此添加小工具。', 'meitu' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'meitu_widgets_init' );

/**
 * 通过图片 URL 获取附件 ID
 * 
 * @param string $link 图片 URL
 * @return int|null 附件 ID（失败返回 null）
 */
function meitu_get_attachment_id_from_src( $link ) {
    global $wpdb;
    
    // 移除 URL 中的尺寸参数（如 -150x150）
    $link = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|webp)$)/i', '', $link );
    
    // 使用参数化查询防止 SQL 注入
    $link = esc_sql( $link );
    $attachment_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE guid='{$link}'" );
    
    return $attachment_id;
}

/**
 * 获取 WordPress 最新一篇已发布文章的所有图片（不含特色图片）
 * 
 * @param int $limit 获取数量（默认 50，不足时返回全部）
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
function meitu_get_image( $limit = 50 ) {
    // 获取最新一篇已发布文章（排除草稿、待审等）
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',  // 仅已发布文章
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
    
    // 获取文章内容中的所有图片（不含特色图片）
    $post = get_post( $post_id );
    $content = $post->post_content;
    
    // 正则匹配所有 img 标签的 src 属性
    preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches );
    
    if ( ! empty( $matches[1] ) ) {
        foreach ( $matches[1] as $src ) {
            // 达到限制数量则停止
            if ( $image_count >= $limit ) {
                break;
            }
            
            // 尝试获取附件 ID
            $attachment_id = meitu_get_attachment_id_from_src( $src );
            
            if ( $attachment_id ) {
                // 如果是 WordPress 媒体库图片，生成缩略图和大图 URL
                $thumbnail_url = wp_get_attachment_image_url( $attachment_id, 'medium' );
                $full_url = wp_get_attachment_image_url( $attachment_id, 'meitu-large' );
                
                $images[] = array(
                    'url'       => $thumbnail_url ?: $src,
                    'full_url'  => $full_url ?: $src,
                    'title'     => $post_title,
                    'alt'       => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: $post_title,
                );
            } else {
                // 外部图片或其他来源，直接使用原 URL
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
    
    wp_reset_postdata();
    
    return $images;
}

/**
 * 添加自定义主题选项页面
 */
function meitu_theme_customizer( $wp_customize ) {
    // 添加设置区域
    $wp_customize->add_section( 'meitu_settings', array(
        'title'    => __( '美图库设置', 'meitu' ),
        'priority' => 30,
    ) );

    // 每页显示图片数量
    $wp_customize->add_setting( 'meitu_images_per_page', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'meitu_images_per_page', array(
        'label'   => __( '每页显示图片数量', 'meitu' ),
        'section' => 'meitu_settings',
        'type'    => 'number',
        'input_attrs' => array(
            'min'  => 10,
            'max'  => 100,
            'step' => 5,
        ),
    ) );
}
add_action( 'customize_register', 'meitu_theme_customizer' );
