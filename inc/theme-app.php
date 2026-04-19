<?php

/* ========== 移动端设置 app ========== */

CSF::createSection( $prefix, array(
	'title'       => '移动端设置',
	'icon'        => 'fab fa-android',
	'fields'      => array(
		array(
			'id' => 'opt_app_restapi',
			'type' => 'switcher',
			'title' => '禁用REST API',
			'desc' => '禁用所有REST路由「/wp/v2/posts」',
			'default' => true
			)
	)
) );