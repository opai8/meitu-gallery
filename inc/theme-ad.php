<?php

/* ========== 广告管理 ad ========== */
/*
 * 首页广告  theme-ad-index
 * 
 * 
 */

CSF::createSection( $prefix, array(
	// 'id'	    => 'ad_fields',
	'title'     => '广告中心',
	'icon'      => 'fas fa-ad',
	'fields'    => array(
		array(
            'type'    => 'submessage',
            'style'   => 'success',
            'content' => '警惕「去广告插件」干扰,测试功能时必须关闭',
        ),
		array(
            'id' => 'opt_ad_banner',
            'type' => 'group',
            'title' => '顶部横幅广告',
            'subtitle' => '菜单栏与主题内容之间',
            'desc' => '点击添加广告，最多3个',
            'min' => 0,
            'max' => 3,
            'fields' => array(
                array(
                    'id' => 'ad_id',
                    'type' => 'text',
                    'title' =>  '唯一标识',
                    'subtitle' => '仅用于识别广告内容，可以作为备注使用'
                ),
                array(
                    'id' => 'ad_img',
                    'type' => 'upload',
                    'title' => '广告图片',
                    'subtitle' => '可以直接填写图片链接，也可以上传图片',
                    'library' => 'image',
                    'preview' => true
                ),
                array(
                    'id' => 'ad_url',
                    'type' => 'text',
                    'title' =>  '广告跳转网址',
                    'subtitle' =>  '需要填写完整的链接地址，包含协议头'
                ),
                array(
                    'id' => 'ad_switcher',
                    'type' => 'switcher',
                    'title' => '功能开关',
                    'subtitle' => '开启/关闭此条广告',
                    'text_on' => '开启',
                    'text_off' => '关闭',
                    'default' => true
                )
            )
        ),
		
		array(
            'id' => 'opt_ad_insert',
            'type' => 'group',
            'title' => '插入图片广告',
            'subtitle' => '广告图片插入美图中间',
            'desc' => '不建议超过5个',
            'fields' => array(
                array(
                    'id' => 'ad_id',
                    'type' => 'text',
                    'title' =>  '唯一标识',
                    'subtitle' => '仅用于识别广告内容，可以作为备注使用'
                ),
                array(
                    'id' => 'ad_img',
                    'type' => 'upload',
                    'title' => '广告图片',
                    'subtitle' => '可以直接填写图片链接，也可以上传图片',
                    'library' => 'image',
                    'preview' => true
                ),
                array(
                    'id' => 'ad_url',
                    'type' => 'text',
                    'title' =>  '广告跳转网址',
                    'subtitle' =>  '需要填写完整的链接地址，包含协议头'
                ),
                array(
                    'id' => 'ad_switcher',
                    'type' => 'switcher',
                    'title' => '功能开关',
                    'subtitle' => '开启/关闭此条广告',
                    'text_on' => '开启',
                    'text_off' => '关闭',
                    'default' => true
                )
            )
        )
		

    )
	
) );

