<?php

/* ========== 广告管理 ad ========== */
/*
 * 首页广告  theme-ad-index
 * 列表页广告 theme-ad-list
 * 详情页广告 theme-ad-detail
 */

CSF::createSection( $prefix, array(
	'title'     => '广告中心',
	'icon'      => 'fas fa-ad',
	'fields'        => array(
		array(
            'id' => 'home_ad_group',
            'type' => 'group',
            'title' => '文章顶部广告',
            'subtitle' => '点击添加广告，最多3个',
            'min' => 1,
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
        )        

    )
	
) );
 

