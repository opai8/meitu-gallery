<?php

/* ========== 广告管理 ad ========== */
/*
 * AD theme-ad-group
 */


/**
 * 内部辅助函数：从指定选项获取并过滤广告数据（私有，请勿外部调用）
 *
 * @param string $option_name 要获取的 WordPress 选项名称 (e.g., 'opt_ad_banner', 'opt_ad_insert').
 * @param bool   $clean_data  是否清理数据，只保留必要字段 (ad_id, ad_img, ad_url).
 * @return array 过滤后的广告数组.
 */
function _meitu_get_ad_option( string $option_name, bool $clean_data = false ): array {
    // 1. 直接从 my_option 获取原始广告配置。
    // 如果选项不存在，my_option 会返回我们提供的默认值 []。
    $ads_config = my_option( $option_name, [] );

    // 2. 【关键防御】确保获取到的是一个数组。
    // 这可以防止 my_option 返回意外的非数组值（如 string, null, false）时导致后续代码出错。
    if ( ! is_array( $ads_config ) ) {
        return [];
    }

    // 3. 过滤：仅保留 'ad_switcher' 严格等于 '1' 的启用广告。
    $enabled_ads = array_filter( $ads_config, function( $ad ) {
        return isset( $ad['ad_switcher'] ) && $ad['ad_switcher'] === '1';
    } );

    // 4. 重新索引数组键。
    // 这是必要的，因为 array_filter 会保留原始数组的键，可能导致键不连续（如 [0, 2, 5]）。
    // 重新索引后，数组键总是从 0 开始连续（如 [0, 1, 2]），便于 foreach 循环和数组访问。
    $enabled_ads = array_values( $enabled_ads );

    // 5. 根据参数决定是否清理数据。
    if ( ! $clean_data ) {
        return $enabled_ads;
    }

    // 6. 清理数据：只保留前端所需的必要字段。
    // 这是必要的，可以减少内存占用和数据传输量，并为前端提供一个干净、可预测的数据结构。
    $clean_ads = array_map( function( $ad ) {
        // 使用空合并运算符 ?? 提供默认值，防止键不存在时产生 Notice 错误。
        return [
            'ad_id'  => $ad['ad_id'] ?? '',
            'ad_img' => $ad['ad_img'] ?? '',
            'ad_url' => $ad['ad_url'] ?? '',
        ];
    }, $enabled_ads );

    return $clean_ads;
}



/**
 * 获取横幅广告配置数据（用于通知栏下方）
 *
 * 这是一个公共API函数，用于获取通知栏下方的横幅广告。
 * 它返回包含所有字段的完整广告配置数组。
 *
 * @return array 横幅广告配置数组。如果没有启用的广告，则返回空数组。
 *               示例: array( array('ad_img' => '...', 'ad_url' => '...', 'ad_switcher' => '1', 'ad_title' => '...') )
 */
function meitu_get_advertisements(): array {
    // 调用内部辅助函数，明确表示不需要清理数据。
    // 这样做的好处是，如果未来 _meitu_get_ad_option 的逻辑改变，这里依然能获得完整的广告配置。
    return _meitu_get_ad_option( 'opt_ad_banner', false );
}



/**
 * 获取瀑布流内置广告（支持多个广告）
 * 
 * 从 index_ad 配置中获取所有启用的广告
 * 
 * @return array 广告数据数组，如果没有广告则返回空数组
 * 
 * 返回格式：
 * array(
 *     array(
 *         'ad_id'   => 'ad1',
 *         'ad_img'  => 'https://example.com/ad.jpg',
 *         'ad_url'  => 'https://example.com/link',
 *     ),
 *     ...
 * )
 */
function meitu_get_waterfall_ads(): array {
    // 调用内部辅助函数，并要求清理数据。
    return _meitu_get_ad_option( 'opt_ad_insert', true );
}