<?php
/**
 * This file contains HTML generation functions.
 *
 * @author  Pexeto
 */


if ( !function_exists( 'pexeto_build_portfolio_carousel_html' ) ) {

	/**
	 * Generates the portfolio carousel HTML code.
	 *
	 * @param array   $posts containing all the post objects that will be displayed
	 * in the carousel
	 * @param string  $title the title of the carousel
	 * @return string        the generated HTML code of the carousel
	 */
	function pexeto_build_portfolio_carousel_html( $posts, $title, $link = null, $link_title=null ) {
		global $pexeto_page;
		$full_layout = true;

		if(isset($pexeto_page['layout']) && ($pexeto_page['layout']=='left' || $pexeto_page['layout']=='right')){
			$full_layout = false;
		}
		
		$columns = 2;
		$i=0;
		$add_class = empty($title) ? ' pc-no-title':'';
		if(!$link && !$link_title){
			$add_class.=' pc-no-link';
		}
		$html= '<div class="portfolio-carousel'.$add_class.'"><div class="pc-header">';
		$html.='<div class="carousel-title"> <h4 class="small-title">'.$title.'</h4>';
		if($link_title){
			$html.='<a class="link-title" href="'.$link.'">'.$link_title.'<span class="more-arrow">&rsaquo;</span></a>';
		}

		$html.='</div></div><div class="pc-wrapper"><div class="pc-holder">';
		$height = $full_layout ? 230 : 150;
		foreach ( $posts as $post ) {
			$preview = pexeto_get_portfolio_preview_img( $post->ID );
			//open a page wrapper div on each first image of the page/slide
			if ( $i%$columns==0 ) {
				$html.='<div class="pc-page-wrapper">';
			}
			//pexeto_build_portfolio_image_html() is located in lib/functions/gallery.php
			$html.=pexeto_get_gallery_thumbnail_html( $post, 3, $height, 'pc-item' );
			if ( ( $i+1 )%$columns==0 || $i+1==sizeof( $posts ) ) {
				//close the page wrapper div on the last image
				$html.='</div>';
			}

			$i++;
		}
		$html.='</div></div><div class="clear"></div></div>';
		return $html;
	}
}


if ( !function_exists( 'pexeto_get_share_btns_html' ) ) {

	/**
	 * Generates the sharing buttons HTML code.
	 *
	 * @param int     $post_id      the ID of the post that the buttons will be
	 * added to
	 * @param string  $content_type the type of the containing element - can
	 * be a post, page, portfolio or slider
	 * @return string               the HTML code of the buttons
	 */
	function pexeto_get_share_btns_html( $post_id, $content_type ) {
		if ( !in_array( $content_type, pexeto_option( 'show_share_buttons' ) ) ) {
			return '';
		}
		$display_buttons = pexeto_option( 'share_buttons' );
		$permalink = get_permalink( $post_id );
		$title = get_the_title( $post_id );
		$html = '<div class="social-share"><div class="share-title">'
			.__( 'Share', 'pexeto' ).'</div><ul>';

		foreach ( $display_buttons as $btn ) {
			switch ( $btn ) {
			case 'facebook':
				$html.='<li title="Facebook" class="share-item share-fb" data-url="'.$permalink
					.'" data-type="'.$btn.'" data-title="'.$title.'"></li>';
				break;

			case 'googlePlus':
				$html.='<li title="Google+" class="share-item share-gp" data-url="'.$permalink
					.'" data-lang="'.pexeto_option( 'gplus_lang' ).'" data-title="'.$title
					.'" data-type="'.$btn.'"></li>';
				break;

			case 'twitter':
				$html.='<li title="Twitter" class="share-item share-tw" data-url="'.$permalink
					.'" data-title="'.$title.'" data-type="'.$btn.'"></li>';
				break;

			case 'pinterest':
				$img = pexeto_get_portfolio_preview_img( $post_id );
				$img = $img['img'];
				$html.='<li title="Pinterest" class="share-item share-pn" data-url="'.$permalink
					.'" data-title="'.$title.'" data-media="'.$img.'" data-type="'.$btn.'"></li>';
				break;
			}
		}

		$html.='</ul></div><div class="clear"></div>';

		return $html;
	}
}


if ( !function_exists( 'pexeto_get_video_html' ) ) {

	/**
	 * Generates a video HTML. For Flash videos uses the standard flash embed code
	 * and for other videos uses the WordPress embed tag.
	 *
	 * @param string  $video_url the URL of the video
	 * @param string  $width     the width to set to the video
	 */
	function pexeto_get_video_html( $video_url, $width ) {
		$video_html='<div class="video-wrap">';
		//check if it is a swf file
		if ( strstr( $video_url, '.swf' ) ) {
			//print embed code for swf file
			$video_html .= '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
			WIDTH="'.$width.'" id="pexeto-flash" ALIGN=""><PARAM NAME=movie VALUE="'.$video_url.'">
			<PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#333399> <EMBED src="'.$video_url.'"
			quality=high bgcolor=#333399 WIDTH="'.$width.'" NAME="pexeto-flash" ALIGN=""
			TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
			</EMBED> </OBJECT>';
		}else {
			$video_html .= apply_filters( 'the_content', '[embed width="'.$width.'"]' . $video_url . '[/embed]' );
		}
		$video_html.='</div>';
		return $video_html;
	}
}


if(!function_exists('pexeto_get_nivo_slider_html')){
	function pexeto_get_nivo_slider_html($images, $options, $slider_div_id, $height, $static_height=true){
		global $pexeto_scripts;

		$style=$static_height ? 'style="max-height:'.$height.'px;"' : 'style="min-height:'.$height.'px;"';
		$html='<div class="nivo-wrapper"><div class="nivo-slider" id="'.$slider_div_id.'" '.$style.'>';	

		foreach ($images as $image) {
			if ( !empty( $image['link'] ) ) { 
				$html.='<a href="'.$image['link'].'">';
			}
			$html.='<img src="'.$image['url'].'" title="'.$image['description'].'"/>';
			if ( !empty( $image['link'] ) ) { 
				$html.='</a>';
			}
		}

		$html.='</div></div>';

		if ( !isset( $pexeto_scripts['nivo'] ) ) {
			$pexeto_scripts['nivo'] = array();
		}
		$pexeto_scripts['nivo'][]=array( 'selector'=>'#'.$slider_div_id, 'options'=>$options );

		return $html;
	}
}


if(!function_exists('pexeto_get_services_standard_style_html')){
	function pexeto_get_services_standard_style_html($boxes, $layout, $title, $desc, $columns, $parallax){
		$services_class = 'services-wrapper services-'.$layout;
		if($layout != 'circle'){
			$services_class.=' cols-wrapper cols-'.($columns);
		}
		if($parallax){
			$services_class.=' pexeto-parallax';
		}
		$html='<div class="'.$services_class.'">';

		$title_box_included = false;
		if ( !empty( $title ) || !empty( $desc ) ) {
			$title_box_included = true;

			$html.='<div class="services-title-box col">';
			if ( !empty( $title ) ) $html.='<h2>'.$title.'</h2>';
			if ( !empty( $desc ) ) $html.='<p>'.$desc.'</p>';
			$html.='</div>';
		}

		for ( $i=0; $i<sizeof( $boxes ); $i+=$columns ) {
			$max_index = min( $i+$columns, sizeof( $boxes ) );
			$add_class = ( $i==0 && $title_box_included==true ) ? ' small-wrapper':'';

			for ( $j=$i; $j<$max_index; $j++ ) {

				//print the single box
				$box = $boxes[$j];
				$open_link = empty( $box['box_link'] )?'':'<a href="'.$box['box_link'].'" />';
				$close_link = empty( $box['box_link'] )?'':'</a>';
				$add_class = $j==$max_index-1 && $layout!=='circle'?' nomargin':'';

				$style=$layout=='circle'?' style="background-image:url('.$box['box_image'].')"':'';

				$html.='<div class="services-box col'.$add_class.'"'.$style.'>';


				if ( !empty( $box['box_image'] ) && $layout!=='circle') {
					$html.=$open_link;
					$html.='<img src="'.$box['box_image'].'" />';
					$html.=$close_link;
				}


				$html.='<div class="services-content"><div class="sc-wraper">';
				$html.=$open_link.'<h3>'.$box['box_title'].'</h3>';
				if($layout!='circle'){
					$html.=$close_link;
				}

				if ( !empty( $box['box_desc'] ) )
					$html.='<p>'.$box['box_desc'].'</p>';

				if($layout=='circle'){
					$html.=$close_link;
				}

				$html.='</div></div></div>';
			}

		}

		$html.='<div class="clear"></div></div>';

		return $html;
	}
}


if(!function_exists('pexeto_get_services_list_style_html')){
	function pexeto_get_services_list_style_html($boxes, $title, $desc, $parallax){
		$services_class = 'services-wrapper cols-wrapper cols-2 services-list';
		if($parallax){
			$services_class.=' pexeto-parallax';
		}
		$html='<div class="'.$services_class.'">';

		if ( !empty( $title ) ) $html.='<h2 class="services-title">'.$title.'</h2>';

		$html.='<div class="sl-wrapper"><div class="col services-description"><div class="sl-description">'.$desc.'</div></div>';

		$html.='<div class="col sl-icons"><ul>';

		foreach ($boxes as $box) {
			$open_link = empty( $box['box_link'] )?'':'<a href="'.$box['box_link'].'" />';
			$close_link = empty( $box['box_link'] )?'':'</a>';

			$html.='<li>';

			if ( !empty( $box['box_image'] )){
				$html.=$open_link.'<span class="sl-icon" style="background-image:url('.$box['box_image'].')"></span>'.$close_link;
			}

			
			$html.='<div class="sl-content">';
			$html.=$open_link.'<h3 class="sl-title">'.$box['box_title'].'</h3>'.$close_link;

			if ( !empty( $box['box_desc'] ) ) $html.='<p>'.$box['box_desc'].'</p>';

			$html.='</div></li>';

		}

		$html.='</ul>';

		$html.='</div></div></div>';

		return $html;
	}
}
