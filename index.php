<?php
/**
 * 美图库主题主模板 - 单文章图集版
 *
 * @package 美图库
 * @version 1.0.0
 */

get_header();

// 获取图片数量设置（默认 50）
$images_per_page = get_theme_mod( 'meitu_images_per_page', 50 );

// 使用 meitu_get_image() 函数获取最新文章的图片
$images = meitu_get_image( $images_per_page );
?>

<!-- ========== 瀑布流容器 ========== -->
<div class="waterfall-container" id="waterfall">
    <?php if ( ! empty( $images ) ) : ?>
        <?php foreach ( $images as $index => $image ) : 
            $url = esc_url( $image['url'] );
            $full_url = esc_url( $image['full_url'] );
            $title = esc_attr( $image['title'] );
            $alt = esc_attr( $image['alt'] ?: $title );
            ?>
            <div class="waterfall-item" data-index="<?php echo esc_attr( $index ); ?>" data-full="<?php echo esc_url( $full_url ); ?>">
                <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" title="<?php echo esc_attr( $title ); ?>">
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <!-- 无图片提示 -->
        <div class="no-images-notice" role="alert" aria-live="polite">
            <div class="icon-container">
                <img src="<?php echo get_template_directory_uri(); ?>/image/404.jpg" alt="无图片提示">
            </div>
            <h3 class="notice-title">暂无可用图片</h3>
            <p class="notice-description">请在 WordPress 后台发布一篇包含图片的文章</p>
            <?php if ( current_user_can( 'publish_posts' ) ) : ?>
                <a href="<?php echo admin_url( 'post-new.php' ); ?>" class="upload-button">
                    创建文章
                    <span class="arrow">→</span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
