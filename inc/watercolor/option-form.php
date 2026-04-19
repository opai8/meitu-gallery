<?php 
	//update options and get options value
	global $wm_options;
	$wm_options = $this->update_options();
	$this->uploade_image();
?>

<style type="text/css">
#dx-watermark{ width:750px; background-color:#f7f7f7; border:1px solid #ddd; padding:10px; margin-top:20px; float:left; margin-right:20px; }
#dx-watermark label{ width:120px; display:inline-block; }

<?php if( $wm_options['type']!='image' ):?>
.text-on{display:block;}
#dw-upload{display:none;}
<?php else: ?>
.text-on{display:none;}
#dw-upload{display:block;}
<?php endif;?>
#dx-watermark .example{ color: #606060; }
</style>

<div class="wrap">

	<h2>水印配置选项</h2>
	
	<div id="dx-watermark">
		<form action="" method="post" id="dw-form">
			<div id="dw-switch">			
				<label>类型: </label>
				<input type="radio" name="type"  value="text" <?php checked( 'text', $wm_options['type'] ); if( empty( $wm_options['type'] ) ) echo 'checked'; ?>/> 文字
				<input type="radio" name="type" value="image" <?php checked( 'image', $wm_options['type'] ); ?>/> 图片
			</div>
			<?php
				// 加载 整个设置
				$this->dw_form();	
				// WordPress 自带生成提交按钮函数
				submit_button('保存设置', 'primary', 'submit', true);
			?>
			<p> 提示：修改配置后记得点击保存 </p>
		</form>

		<form action="" method="post" id="dw-upload" enctype="multipart/form-data">
			<label for="dw-image">选择水印图片: </label>
			<input type="file" name="upload-image" id="dw-image"/> <input type="submit" name="dw-image" value="上传图片"/>
			<input type="hidden" id="upload-file" value="<?php echo $wm_options['upload_image']; ?>"/>
			<div id="show-image">
				<?php if( $wm_options['upload_image_url'] ): ?>
				<img src="<?php echo $wm_options['upload_image_url']; ?>"/>
				<?php else: ?>
				<span style="color:#dd0066;">你还没有上传水印图片!</span>
				<?php endif; ?>
				<p> 使用方法：请先"保存配置"-选择文件-上传图片-保存配置,方可成功预览! ( 上传背景透明的 .png 图片 ) </p>
			</div>
		</form>
	</div>
	
	<div id="preview-box">	
		<form action="" method="post" name="preview_new_watermark" id="preview_new_watermark">
			<?php 
				submit_button(
					'点击生成预览图',
					'primary',
					'',
					true,
					array( 
						'name' => 'preview_the_watermark'
						// 'id' => 'preview_the_watermark'
						)
				);
			?>
        </form>
		<?php
			if(isset($_POST['preview_the_watermark']) && stripslashes_deep($_POST['preview_the_watermark'])){
				$wp_get_preview = new WP_Watermark();
				$preview_image = $wp_get_preview->get_preview_with_watermark();
			}
			if(isset($preview_image) && stripslashes_deep($preview_image)){
				$result .= '<div class="preview_image"><img src="'.$preview_image.'?tempid='.strtotime(date("H:i:s",time())).'"></div>';
			}else{
				$result .= '<img src="'.WM_DATA_URI.'/preview.jpg">';
			}
			echo $result;
		?>
	</div>
	<div style="clear:both;"></div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	
	/*switch*/
	$('#dw-switch input').change(function(){
		var sSwitch = $(this).val();
		if( sSwitch == 'image' ){
			$('.text-on').css('display','none');
			$('#dw-upload').css('display','block');
		}
		else{
			$('.text-on').css('display','block');
			$('#dw-upload').css('display','none');
		}
	});
	
	/* 加载WordPress自带的颜色选择器样式 */
	$('#dw-color').wpColorPicker();
	
	/*color picker*/
	$('.excolor').modcoder_excolor({
	   sb_slider : 1,
	   effect : 'zoom',
	   callback_on_ok : function() {
		  // You can insert your code here 
	   }
	});	
	
});
</script>