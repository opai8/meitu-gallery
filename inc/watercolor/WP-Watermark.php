<?php

class WP_Watermark{

    function __construct() {
		// 在WordPress生成图片附件元数据时自动添加水印
        add_filter('wp_generate_attachment_metadata', array($this, 'do_watermark'), 999);
		// 在WordPress后台菜单添加设置
        add_action('admin_menu', array($this, 'menu_page'));
        add_action('init', array($this, 'update_preview_data'));
    }
	
	// 创建图片水印文件夹,字体文件夹
    function make_dir() {
		// 获取上传目录信息
        $uploads = wp_upload_dir();
		// 设置目录名为watermark-upload
        $dw_dir = $uploads['basedir'] . '/watermark-upload';
        // 检查目录是否存在，不存在则创建 
		if (!is_dir($dw_dir)) {
            wp_mkdir_p($dw_dir);
            wp_mkdir_p($dw_dir . '/font');
        }
    }
	
	// create image water
	function image_water( $options='',$args=array() ){
		//data
		$dst_file = $args['dst_file'];
		$src_file = $args['src_file'];
		$alpha = $args[ 'alpha' ];
		$position = $args['position'];
		$im_file = $args[ 'im_file' ];
		
		$dst_data = @getimagesize( $dst_file );
		$dst_w = $dst_data[0];
		$dst_h = $dst_data[1];
		$min_w = isset( $options['min_width'] ) && $options['min_width'] ? $options['min_width'] : 300 ;
		$min_h = isset( $options['min_height'] ) && $options['min_height'] ? $options['min_height'] : 300 ;
		if( $dst_w <= $min_w || $dst_h <= $min_h ) return;
		$dst_mime = $dst_data['mime'];
		$src_data = @getimagesize( $src_file );
		$src_w = $src_data[0];
		$src_h = $src_data[1];
		$src_mime = $src_data['mime'];
		
		//create
		$dst = $this->create_image( $dst_file, $dst_mime );
		$src = $this->create_image( $src_file, $src_mime );
		$dst_xy = $this->position( $position, $src_w, $src_h, $dst_w, $dst_h );
		$merge = $this->imagecopymerge_alpha( $dst, $src, $dst_xy[0], $dst_xy[1], 0, 0, $src_w, $src_h, $alpha );
		if( $merge ){
			$this->make_image( $dst, $dst_mime, $im_file );
		}
		imagedestroy( $dst );
		imagedestroy( $src );
	}
	
	//create text water
	function text_water( $options='', $args=array() ){
		//data
		$file = $args['file'];
		$font = $args['font'];
		$text = $args['text'];
		$alpha = $args['alpha'];
		$size = $args['size'];
		$red = $args['color'][0];
		$green = $args['color'][1];
		$blue = $args['color'][2];
		$position = $args['position'];
		$im_file = $args['im_file'];
		
		$dst_data = @getimagesize( $file );
		$dst_w = $dst_data[0];
		$dst_h = $dst_data[1];
		$min_w = ( isset( $options['min_width'] ) && $options['min_width'] ) ? $options['min_width'] : 300 ;
		$min_h = ( isset( $options['min_height'] ) && $options['min_height'] ) ? $options['min_height'] : 300 ;
		if( $dst_w <= $min_w || $dst_h <= $min_h ) return;
		$dst_mime = $dst_data['mime'];
		
		//create
		$coord = imagettfbbox( $size, 0, $font, $text );
		$w = abs( $coord[2]-$coord[0] ) + 5;
		$h = abs( $coord[1]-$coord[7] ) ;
		$H = $h+$size/2;
		$src = $this->image_alpha( $w, $H );
		$color = imagecolorallocate( $src, $red, $green, $blue );
		$posion = imagettftext( $src, $size, 0, 0, $h, $color, $font, $text );
		$dst = $this->create_image( $file, $dst_mime );
		$dst_xy = $this->position( $position,$w, $H, $dst_w, $dst_h );
 		$merge = $this->imagecopymerge_alpha( $dst, $src, $dst_xy[0], $dst_xy[1], 0, 0, $w, $H, $alpha );
		$this->make_image( $dst, $dst_mime, $im_file );
		imagedestroy( $dst );
		imagedestroy( $src );				
	}
	
	//create image from file
	function create_image( $file, $mime ){
		switch( $mime ){
			case 'image/jpeg' : $im = imagecreatefromjpeg( $file ); break;
			case 'image/png' : $im = imagecreatefrompng( $file ); break;
			case 'image/gif' : $im = imagecreatefromgif( $file ); break;
		}
		return $im;
	}
	
	//make image
	function make_image( $im, $mime, $im_file ){
		switch( $mime ){
			case 'image/jpeg' : {
				$options = get_option( 'wp-watermark-option' );
				$quality = ( isset( $options['jpeg_quality'] ) && $options['jpeg_quality'] ) ? $options['jpeg_quality'] : 95;
				imagejpeg( $im, $im_file, $quality );
				break;
			}
			case 'image/png' : imagepng( $im, $im_file ); break;
			case 'image/gif' : imagegif( $im, $im_file ); break;
		}
	}
	
	//imagecopymerge alpha
	function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        $opacity=$pct;
        // getting the watermark width
        $w = imagesx($src_im);
        // getting the watermark height
        $h = imagesy($src_im);
         
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        // copying that section of the background to the cut
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        // inverting the opacity
         
        // placing the watermark now
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        $merge = imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity);
		return $merge;
    }		
	
	function image_alpha( $w, $h ){
		$im=imagecreatetruecolor( $w, $h );
		imagealphablending( $im, true );	//启用Alpha合成
		imageantialias( $im, true );	//启用抗锯齿
		imagesavealpha( $im, true );	//启用Alpha通道		
		$bgcolor = imagecolorallocatealpha( $im,255,255,255,127 ); 		//创建透明颜色（最后一个参数0不透明，127完全透明）
		imagefill( $im, 0, 0, $bgcolor );//使图片底色透明
		return $im;
	}
	
	//wartermark position
	function position( $position, $s_w, $s_h, $d_w, $d_h ){
		switch( $position ){
			case 1 : $x=5; $y=0; break;
			case 2 : $x=($d_w-$s_w)/2; $y=0; break;
			case 3 : $x=($d_w-$s_w-5); $y=0; break;
			case 4 : $x=5; $y=($d_h-$s_h)/2; break;
			case 5 : $x=($d_w-$s_w)/2; $y=($d_h-$s_h)/2; break;
			case 6 : $x=($d_w-$s_w-5); $y=($d_h-$s_h)/2; break;
			case 7 : $x=5; $y=($d_h-$s_h); break;
			case 8 : $x=($d_w-$s_w)/2; $y=($d_h-$s_h); break;
			default: $x=($d_w-$s_w-5); $y=($d_h-$s_h); break;
		}
		$res = get_option( 'wp-watermark-option' );
		$x += $res['level'];
		$y += $res['vertical']; 		
		$xy = array( $x, $y );
		return $xy;
	}
	
	//judge dynamic gif
	function IsAnimatedGif( $file ){
		$content = file_get_contents($file);
		$bool = strpos($content, 'GIF89a');
		if($bool === FALSE)
		{
			return strpos($content, chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0')===FALSE?0:1;
		}
		else
		{
			return 1;
		}
	}

	//hex to dec
	function hex_to_dec( $str ){
		$r = hexdec( substr( $str, 1, 2 ) );
		$g = hexdec( substr( $str, 3, 2 ) );
		$b = hexdec( substr( $str, 5, 2 ) );
		$color = array( $r, $g, $b );
		return $color;
	}	
	
	//do watermark
	function do_watermark( $metadata ){
		$options = get_option( 'wp-watermark-option' );
		$upload_dir = wp_upload_dir();
		$dst = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $metadata['file'];
		if( $this->IsAnimatedGif( $dst ) ) return $metadata;
		$src = $options['upload_image'];
		$size= $options['size'] ? $options['size'] : 16;
		$alpha= $options['transparency'] ? $options['transparency'] : 90;
		$position = $options['position'] ? $options['position'] : 9;
		$color = $options['color'] ? $this->hex_to_dec( $options['color'] ) : array(255,255,255);
		$default_path = WM_DATA_DIR . '/font/arial.ttf';
		$font = $options['font'] ? stripslashes($options['font']) : $default_path;
		$text = $options['text'] ? stripslashes($options['text']) : get_bloginfo('url');
		
		if( $options['type']=='image' ){
			$args=array(
				'dst_file' => $dst,
				'src_file' => $src,
				'alpha' => $alpha,
				'position' => $position,
				'im_file' => $dst
			);	
			$this->image_water( $options, $args );		
		}
		else{
			$args=array(
				'file'=>$dst,
				'font'=>$font,
				'size'=>$size,
				'alpha'=>$alpha,
				'text'=>$text,
				'color'=>$color,
				'position'=>$position,
				'im_file' => $dst
			);
			$this->text_water( $options, $args );	
		}
		return $metadata;
	}
	
	//add menu page
	function menu_page(){
		add_menu_page(
            'WP-Watermark',     // 页面标题
			'水印设置',     		// 菜单标题
			'manage_options',   // 权限要求
			'wp-watermark',     // 菜单slug
            array($this, 'option_form'), // 回调函数
            WM_DATA_URI . '/icon.png' //菜单图标
        );
	}
	
	//menu page form
	function option_form(){
		// 加载WordPress自带的颜色选择器样式
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		include( 'option-form.php' );
		
	}
	
	
	//get default fonts
	function default_fonts(){
		$font_dir = WM_DATA_DIR.'/font/';	//物理地址:/var/www/
		$font_names = scandir( $font_dir );
		unset( $font_names[0] );
		unset( $font_names[1] );
		foreach( $font_names as $font_name ){
			$fonts[$font_name] = $font_dir.$font_name;
		}
		return $fonts;
	}
	
	//get custom fonts
	function custom_fonts(){
		$uploads = wp_upload_dir();
		$font_dir = $uploads['basedir'].'/watermark-upload/font/'; 
		if( is_dir( $font_dir ) ){
			$font_names = scandir( $font_dir );
			unset( $font_names[0] );
			unset( $font_names[1] );
			foreach( $font_names as $font_name ){
				$fonts[$font_name] = $font_dir.$font_name;
			}
			return $fonts;			
		}
	}
	
	//text form
	function dw_form(){
		global $wm_options;
		wp_enqueue_script(
        'jq-excolor',
        WM_DATA_URI . '/excolor/jquery.modcoder.excolor.js',
		['jquery']
    );

?>


<p>
    <label>忽略: </label>
    
    <label for="dw-minwidth" style="width:70px;">最小宽度: </label>
    <input type="text" id="dw-minwidth" name="min_width" 
           value="<?php echo $wm_options['min_width'] ? $wm_options['min_width'] : 300; ?>"/> 
    
    <label for="dw-minheight" style="width:70px;">最小高度: </label>
    <input type="text" id="dw-minheight" name="min_height" 
           value="<?php echo $wm_options['min_height'] ? $wm_options['min_height'] : 300; ?>"/>
</p>

<p>
    <label>位置: </label>
    
    <table width="200" border="1" cellspacing="0" cellpadding="5" bordercolor="#ccc" id="dw-position" style="margin-left:125px;">
        <tr>
            <td><input type="radio" name="position" value="1" 
                    <?php checked('1', $wm_options['position']); ?>/>1</td>
            <td><input type="radio" name="position" value="2" 
                    <?php checked('2', $wm_options['position']); ?>/>2</td>
            <td><input type="radio" name="position" value="3" 
                    <?php checked('3', $wm_options['position']); ?>/>3</td>
        </tr>
        
        <tr>
            <td><input type="radio" name="position" value="4" 
                    <?php checked('4', $wm_options['position']); ?>/>4</td>
            <td><input type="radio" name="position" value="5" 
                    <?php checked('5', $wm_options['position']); ?>/>5</td>
            <td><input type="radio" name="position" value="6" 
                    <?php checked('6', $wm_options['position']); ?>/>6</td>
        </tr>
        
        <tr>
            <td><input type="radio" name="position" value="7" 
                    <?php checked('7', $wm_options['position']); ?>/>7</td>
            <td><input type="radio" name="position" value="8" 
                    <?php checked('8', $wm_options['position']); ?>/>8</td>
            <td><input type="radio" name="position" value="9" 
                    <?php checked('9', $wm_options['position']); 
                    if (empty($wm_options['position'])) echo 'checked'; ?>/>9</td>
        </tr>					
    </table>
</p>

<p>
    <label for="dw-level">水平调整: </label>
    <input type="text" name="level" id="dw-level" size="60" 
           value="<?php echo $wm_options['level'] ? $wm_options['level'] : 0; ?>"/> px 
    <span class="example">( 例： 5 or -5 )</span>
</p>

<p>
    <label for="dw-vertical">垂直调整: </label>
    <input type="text" name="vertical" id="dw-vertical" size="60" 
           value="<?php echo $wm_options['vertical'] ? $wm_options['vertical'] : 0; ?>"/> px 
    <span class="example">( 例. 10 or -10 )</span>
</p>

<p class="text-on">
    <label for="dw-fonts">字体: </label>
    <select id="dw-fonts" name="font">
        <?php 
        $default_fonts = $this->default_fonts();
        foreach ($default_fonts as $key => $default_font): ?>
            <option value="<?php echo $default_font; ?>" 
                    <?php selected($default_font, stripslashes($wm_options['font'])); ?>>
                <?php echo $key; ?>
            </option>
        <?php endforeach; ?>
        
        <?php 
        $custom_fonts = $this->custom_fonts();
        if ($custom_fonts):
            foreach ($custom_fonts as $key => $custom_font): ?>
                <option value="<?php echo $custom_font; ?>" 
                        <?php selected($custom_font, stripslashes($wm_options['font'])); ?>>
                    <?php echo $key; ?>
                </option>
        <?php endforeach; 
        endif; ?>			
    </select>
</p>

<p class="text-on example">
    <?php 
    $uploads = wp_upload_dir();
    $custom_fonts_dir = $uploads['basedir'] . '/watermark-upload/font/';
    $fonts_note = '请自行上传中文.ttf字体文件到 /uploads/wm-upload/font/';
    echo $fonts_note;
    ?>
</p>

<p class="text-on">
    <label for="dw-text">文字: </label>
    <input type="text" name="text" id="dw-text" size="60" 
           value="<?php echo $wm_options['text'] ? stripslashes($wm_options['text']) : get_bloginfo('url'); ?>"/>
</p>

<p class="text-on">
    <label for="dw-size">大小: </label>
    <input type="text" name="size" id="dw-size" size="60" 
           value="<?php echo $wm_options['size'] ? $wm_options['size'] : 16; ?>"/> px 
    <span class="example">( 例子. 16 )</span>
</p>
<!--
<p class="text-on">
    <label for="dw-color">颜色: </label>
    <input type="text" name="color" id="dw-color" class="excolor" size="30" 
           value="xxx"/>
    <span class="example">( 例. #000000 )</span>
</p>
-->
<p class="text-on">
    <label for="dw-color">颜色: </label>
    <input type="text" name="color" id="dw-color" class="color-picker" 
           value="<?php echo $wm_options['color'] ? $wm_options['color'] : '#ffffff'; ?>"/>
    <span class="example">( 例. #666666 )</span>
</p>

<p>
    <label for="dw-transparency">透明度: </label>
    <input type="text" name="transparency" id="dw-transparency" size="60" 
           value="<?php echo $wm_options['transparency'] ? $wm_options['transparency'] : '90'; ?>"/>
    <span class="example">( from 0 - 100 )</span>
</p>

<p>
    <label for="dw-quality">Jpeg图片质量: </label>
    <input type="text" name="jpeg_quality" id="dw-quality" size="60" 
           value="<?php echo $wm_options['jpeg_quality'] ? $wm_options['jpeg_quality'] : '95'; ?>"/>
</p>

<p class="example">
	范围从 1 (文件小, 质量差) 到 100 (文件大, 质量好)
</p>
<!--form end-->



<?php		
	}

	//upload image
	function uploade_image(){
		if( isset( $_POST['dw-image'] ) && $_POST['dw-image'] ){
			$uploads = wp_upload_dir();
			$dw_dir = $uploads['basedir'].'/watermark-upload';
			$dw_url = $uploads['baseurl'].'/watermark-upload';
			$fileinfo = $_FILES['upload-image'];
			$file = $fileinfo['tmp_name'];
			$des = $dw_dir.'/'.$fileinfo['name'];
			$res = move_uploaded_file( $file, $des);
			if( $res ){
				global $wm_options;
				$wm_options['upload_image'] = $des;
				$wm_options['upload_image_url'] = $dw_url.'/'.$fileinfo['name'];
				update_option( 'wp-watermark-option', $wm_options );
			}
		}
	}
	
	//update options data
	function update_options(){
		if( isset( $_POST['submit'] ) && $_POST['submit'] ){
			$pre = get_option( 'wp-watermark-option' );
			$data = array(
				'type' => $_POST['type'],
				'position' => $_POST['position'],
				'font' => $_POST['font'],
				'text' => $_POST['text'],
				'size' => $_POST['size'],
				'color' => $_POST['color'],
				'level' => $_POST['level'],
				'vertical' => $_POST['vertical'],
				'transparency' => $_POST['transparency'],
				'upload_image' => $pre['upload_image'],
				'upload_image_url' => $pre['upload_image_url'],
				'min_width' => $_POST['min_width'],
				'min_height' => $_POST['min_height'],
				'jpeg_quality' => $_POST['jpeg_quality']
			);
			update_option( 'wp-watermark-option', $data );
			update_option( 'wp-watermark-option-preview', $data );
		}
		return get_option( 'wp-watermark-option' );
	}
	
	//update preview data
	function update_preview_data(){
		if( isset( $_GET['preview'] ) && $_GET['preview']=='data' ){
			update_option( 'wp-watermark-option-preview', $_GET );
		}
	}

	//form bottom action
	
	/**
     *
     */
    function get_preview_with_watermark()
    {
        $options = get_option( 'wp-watermark-option-preview' );
		$dst = WM_DATA_DIR.'/preview.jpg';
		$src = $options['upload_image'];
		$size= $options['size'] ? $options['size'] : 16;
		$alpha= $options['transparency'] ? $options['transparency'] : 90;
		$position = $options['position'] ? $options['position'] : 9;
		$color = $options['color'] ? $this->hex_to_dec( $options['color'] ) : array(255,255,255);
		$font = $options['font'] ? stripslashes($options['font']) : WM_DATA_DIR.'/font/arial.ttf';
		$text = $options['text'] ? stripslashes($options['text']) : get_bloginfo('url');

        $upload_dir = wp_upload_dir();
        $output_file = $upload_dir['basedir'].'/watermark-upload/preview.jpg';

        if(file_exists($output_file)){
            unlink($output_file);
        }

        if( $options['type']=='image' ){
			$args=array(
				'dst_file' => $dst,
				'src_file' => $src,
				'alpha' => $alpha,
				'position' => $position,
				'im_file' => $output_file,
				'is_preview' => true
			);	
			$this->image_water( '', $args );		
		}
		else{
			$args=array(
				'file'=>$dst,
				'font'=>$font,
				'size'=>$size,
				'alpha'=>$alpha,
				'text'=>$text,
				'color'=>$color,
				'position'=>$position,
				'im_file' => $output_file,
				'is_preview' => true
			);
			$this->text_water( '', $args );	
		}

        return $upload_dir['baseurl'] . DIRECTORY_SEPARATOR . 'watermark-upload/preview.jpg';;

    }
	//form bottom action

}



//new WP-Watermark
$WP_Watermark = new WP_Watermark();


add_action('after_setup_theme', array('WP_Watermark', 'make_dir'));



