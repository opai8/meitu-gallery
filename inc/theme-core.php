<?php

/**
 * 获取文章缩略图（支持特色图片和内容首图）
 * 
 * @param bool $single 是否返回单个缩略图（默认true）
 * @param bool $must 未使用的参数（兼容性保留）
 * @return string 生成的缩略图HTML代码
 */
function hui_get_thumbnail( $single=true, $must=true ) {
    global $post;  // 获取当前文章对象
    $html = '';    // 初始化HTML输出容器
	
	// 1.优先检查是否存在特色图片
    if ( has_post_thumbnail() ) {
		// 使用 simplexml 解析特色图片 HTML
        $domsxe = simplexml_load_string(get_the_post_thumbnail());
        // 提取原始图片 URL
		$src = $domsxe->attributes()->src;
		// 获取附件 ID 并生成缩略图尺寸 URL
        $src_array = wp_get_attachment_image_src(hui_get_attachment_id_from_src($src), 'thumbnail'); // 通过URL获取附件ID, WordPress预定义缩略图尺寸
		// 生成标准img标签
        $html = sprintf('<img src="%s" />', $src_array[0]);
    }
	// 2. 当没有特色图片时处理文章内容中的图片
	else {
		// 获取文章内容
        $content = $post->post_content;
		// 正则匹配所有图片标签的src属性, // $strResult 匹配结果容器
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
		// 提取所有图片URL
        $images = $strResult[1];
		// 统计文章内容中所有图片的总数量
        $counter = count($strResult[1]);
		// $i 记录当前正在处理的图片在数组中的索引位置
        $i = 0;



		// 遍历所有匹配到的图片
        foreach($images as $src){
            $i++;	// 每次循环处理一张图片时, $i的值加 1
			// 获取缩略图尺寸的附件URL
            $src2 = wp_get_attachment_image_src(hui_get_attachment_id_from_src($src), 'thumbnail');
            // 提取URL部分
			$src2 = $src2[0];
			
			// 回退机制：如果获取失败则使用原始URL
            if( !$src2 && true ){
                $src = $src;
            }else{
                $src = $src2;
            }
			
			// 构建带灯箱效果的图片容器
            $item = sprintf('
				<div class="item">
					<a class="fancybox-buttons image-link" data-fancybox-group="button" href="%s">
						<img src="%s" loading="lazy" />
					</a>
				</div>',
				$src,
				$src);
			
			// 单图模式处理：直接返回首个结果
            if( $single){
                return $item;
                break;
            }
			// 多图模式拼接
            $html .= $item;
            // 条件判断
			if(
				($counter >= 10 && $i >= 50)
                // ($counter >= 4 && $counter < 8 && $i >= 4) ||		// 图片总数4-7张时，处理前4张后终止
                // ($counter >= 8 && $i >= 50) ||						// 图片总数≥8张时，处理前50张后终止
                // ($counter > 0 && $counter < 4 && $i >= $counter)		// 图片总数<4张时，处理全部后终止
            ){
                break;
            }
        }
    }
	// 返回生成的HTML代码
    return $html;
}

/**
 * 通过图片URL获取附件ID
 * 
 * @param string $link 图片URL
 * @return int|null 附件ID（失败返回null）
 */
function hui_get_attachment_id_from_src ($link) {
    global $wpdb; // WordPress数据库对象
	
	// 正则移除URL中的尺寸参数（如-150x150）,匹配尺寸模式，并替换为空
    $link = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link);
    
	// 参数化查询：防止SQL注入
	$arr = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE guid='$link'");
	
	return $arr;
}



// 向图片中插入图片广告
function hui_insert_ads($ad_group) {
    $ads_html = '';
    
    if (is_array($ad_group) && !empty($ad_group)) {
		// 遍历输出
        foreach ($ad_group as $ad) {
            // 验证广告有效性
            if (
                !empty($ad['ad_switcher']) 
                && $ad['ad_switcher'] == '1' 
                && !empty($ad['ad_img']) 
                && filter_var($ad['ad_url'], FILTER_VALIDATE_URL)
            ) {
                $ads_html .= sprintf(
                    '<div class="item">
						<span class="ad-label">AD</span>ad
						<a href="%s" target="_blank" class="fancybox-buttons image-link" 
						data-fancybox-group="button"
						rel="nofollow">
							<img src="%s" alt="广告图片" loading="lazy"/>
						</a>
					</div>',
                    // esc_attr($ad['ad_id']),
                    esc_url($ad['ad_url']),
                    esc_url($ad['ad_img'])
					
                );
            }
        }
    }
    
    return $ads_html;
}