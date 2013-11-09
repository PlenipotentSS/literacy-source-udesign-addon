<?php
	$ls_slider_text = get_post_meta($post->ID, '_ls_slider_text', true);
	$ls_slider_link_type = get_post_meta($post->ID, '_ls_slider_link_type', true);
	$link_type_1 = '';
	$link_type_2 = 'checked="checked"';
	$link_display_none = 'style="display:none;"';
	$page_display_none = '';
	if ( $ls_slider_link_type == 'link_url') {
		$link_type_1 = 'checked="checked"';
		$link_type_2 = '';
		$link_display_none = '';
		$page_display_none = 'display:none;';
	}
	$ls_slider_link = get_post_meta($post->ID, '_ls_slider_link', true);
	$ls_slider_page_id = get_post_meta($post->ID, '_ls_slider_page_id', true);
	$ls_slider_image = get_post_meta($post->ID, '_ls_slider_image', true);

	//check if there are any differences
	global $udesign_options;
	$page_notification = '';
	if (isset($udesign_options['c2_slide_default_info_text_'.$post->ID]) ) {
		if ( $ls_slider_link_type == 'link_url' && $ls_slider_link != $udesign_options['c2_slide_link_url_'.$post->ID]) {
			$ls_slider_link = $udesign_options['c2_slide_link_url_'.$post->ID];
		} else if ( $ls_slider_link_type == 'link_page' ) {
			$page_url = get_page_link(intval( strip_tags($_POST['ls_slider_page_id']) ));
			if ($page_url != $udesign_options['c2_slide_img_url_'.$post->ID] ) {
				$page_notification = "Page link differs from below, check udesign settings for accurate link - to prevent future misassociation, please only edit individual slider information through the 'Hompage Slider' custom post type.";
			}
		}
		if ( $ls_slider_image != $udesign_options['c2_slide_img_url_'.$post->ID] ) {
			$ls_slider_image = $udesign_options['c2_slide_img_url_'.$post->ID];
		}
	}
?>
	<script type="text/javascript" src="<?echo plugins_url('/literacy_source_udesign_addon/js/jquery.js');?>"></script>
    <script type="text/javascript" src="<?echo plugins_url('/literacy_source_udesign_addon/js/jquery.maxlength.js'); ?>"></script>
	<style>
		.notification {
			border:3px solid #d55b5b;
			background-color: #ffcdcd;
			padding:5px;
		}
	</style>
	<script type="text/javascript"> 
		function put_img_into_place() {
			var theIMG = document.getElementById('ls_slider_image').value;
			document.getElementById('ls_slider_img_obj').src = theIMG;
		}
		function show_link_type(div_name) {
			document.getElementById('link_url').style.display = 'none';
			document.getElementById('link_page').style.display = 'none';

			document.getElementById(div_name).style.display = 'inline';
		}
	</script>
	<p><label for='ls_slider_text'><b>Brief Description:</b> <span style='color:#A0A0A0'>Maximum 500 characters</span></label><br />
	<textarea id='textarea_1_1' name='ls_slider_text' rows='8' style='width:100%;'><?echo $ls_slider_text ?></textarea>
	<script type="text/javascript"> 
		$(document).ready(function(){   
			$('#textarea_1_1').maxlength(); 
		});
	</script>
	</p>
	<p><label for="ls_slider_image"><b>Slide Image:</b><span style='color:#A0A0A0'>(optimized for size: 286h x 485w)</span></label> <br />
	<input id='ls_slider_image' type='text' size='60' name='ls_slider_image' value ='<? echo esc_attr($ls_slider_image); ?>'> 
	<input id='put_img' type='button' onclick='put_img_into_place()' value='View Image' class='button-secondary' /><br>&nbsp;<input id='upload_image_button' type='button' value='Media Library Image' class='button-secondary' />  
	Enter a URL to an image or use an image from the Media Library</p>
	<center><div style='background-color:#FFF; border: 1px solid #000; width: 485px;'><img id='ls_slider_img_obj' src='<? echo esc_attr($ls_slider_image); ?>' style='max-width:485px; max-height:286px;'></div></center><br />
	<p><label for="ls_slider_link"><b>Link to :</b></label>
		<input type='radio' name='ls_slider_link_type' id='link_1' onclick='show_link_type(this.value);' value ='link_url' <? echo $link_type_1; ?> /> Enter URL 
		<input type='radio' name='ls_slider_link_type' id='link_2' onclick='show_link_type(this.value);' value ='link_page' <? echo $link_type_2; ?> /> Select Page
	</p>
	<p style='width:100%;'>	
	<div id='link_url' <?echo $link_display_none;?>>
		<input type='text' name='ls_slider_link' style="width:100%" value='<?echo $ls_slider_link ?>' />
	</div>
	<div id='link_page' style='margin: 0 auto; <?echo $page_display_none?>'>
		<? if ( $page_notification != '' ) { ?>
			<div style='margin: 0 auto; padding: 4px 0px 0px 0px; width:100%; text-align: center; height: 20px; background-color:#FFB6C1; border: 1px solid #FF0000;'>
				<? echo $page_notification; ?>
			</div>
			<br />
		<? } ?>
		<select size='10' name='ls_slider_page_id' multiple='no'>
			<option value=''> - Select Page - </option>
			<?
			$args = array('post_type' => 'page');
			$the_query = new WP_Query( $args );
			while ( $the_query->have_posts()  ): $the_query->the_post();
				$this_id = $the_query->post->ID;
				$this_title = get_the_title();
				//run through all pages (except those with homepage in title)
				if ( strpos(strtolower($this_title),'homepage') === false ) {
					//if previously selected
					if (intval($ls_slider_page_id) == $this_id ) {
						echo "<option value='".$this_id."' selected='selected'>".$this_title."</option>";
					} else {
						echo "<option value='".$this_id."'>".$this_title."</option>";
					}
				}
			endwhile;
			?>
		</select>
	</div>
	</p>