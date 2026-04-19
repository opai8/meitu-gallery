<?php
/**
 * 美图库主题主模板 - 单文章图集版
 *
 * @package 美图库
 * @version 2.0.0
 */

get_header();

// 获取图片数量设置（默认 50）且不包含特色图片
$images = meitu_get_image( 50, true );

// 获取瀑布流内置广告（支持多个广告）
$waterfall_ads = meitu_get_waterfall_ads();
?>

<!-- ========== 瀑布流容器 ========== -->
<div class="waterfall-container" id="waterfall">
    <?php if ( ! empty( $images ) ) : ?>
        
        <?php 
        // 如果有广告，在指定位置插入广告
        $ad_index = 0;
        $total_ads = count( $waterfall_ads );
        ?>
        
        <?php foreach ( $images as $index => $image ) : 
            // 在每个广告索引位置插入广告（每 10 张图片插入一个）
            if ( $ad_index < $total_ads && $index === $ad_index * 10 ) : 
                $ad = $waterfall_ads[ $ad_index ];
                ?>
                <!-- 瀑布流广告 -->
                <div class="waterfall-item waterfall-ad" data-ad-url="<?php echo esc_url( $ad['ad_url'] ); ?>">
                    <a href="<?php echo esc_url( $ad['ad_url'] ); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="waterfall-ad-link">
                        <img src="<?php echo esc_url( $ad['ad_img'] ); ?>" 
                             alt="广告" 
                             loading="lazy">
                        <span class="ad-badge">AD</span>
                    </a>
                </div>
                <?php
                $ad_index++;
            endif;
            ?>
            
            <?php
			
            // 1. 准备图片 URL
            $url = esc_url( $image['url'] );
            $full_url = esc_url( $image['full_url'] );

            // 2. 智能处理 title 和 alt 属性
            // 优先使用图片自身的 title，如果为空，则使用网站名
            $title = ! empty( $image['title'] ) ? $image['title'] : get_bloginfo( 'name' );
            
            // 优先使用图片自身的 alt，如果为空，则使用 title，再为空则使用网站名
            $alt = ! empty( $image['alt'] ) ? $image['alt'] : ( ! empty( $image['title'] ) ? $image['title'] : get_bloginfo( 'name' ) );

            // 3. 转义输出
            $title_attr = esc_attr( $title );
            $alt_attr = esc_attr( $alt );

            ?>
			
            <div class="waterfall-item" data-index="<?php echo esc_attr( $index ); ?>" data-full="<?php echo esc_url( $full_url ); ?>">
                <img src="<?php echo esc_url( $url ); ?>" 
                     alt="<?php echo esc_attr( $alt_attr ); ?>" 
                     loading="lazy" 
                     <?php if ( ! empty( $title_attr ) ) : ?>title="<?php echo esc_attr( $title_attr ); ?>"<?php endif; ?>>
            </div>
        <?php endforeach; ?>
        
        <?php 
        // 如果还有剩余广告未显示，在末尾显示
        while ( $ad_index < $total_ads ) : 
            $ad = $waterfall_ads[ $ad_index ];
            ?>
            <div class="waterfall-item waterfall-ad" data-ad-url="<?php echo esc_url( $ad['ad_url'] ); ?>">
                <a href="<?php echo esc_url( $ad['ad_url'] ); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="waterfall-ad-link">
                    <img src="<?php echo esc_url( $ad['ad_img'] ); ?>" 
                         alt="广告" 
                         loading="lazy">
                    <span class="ad-badge">AD</span>
                </a>
            </div>
            <?php
            $ad_index++;
        endwhile; ?>
        
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
