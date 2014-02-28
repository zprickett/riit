<?php
/**
 * This file contains all the shortcode register functionality.
 * All the functions are pluggable which means that they can be replaced in a child theme.
 *
 * @author Pexeto
 */


if(!function_exists('pexeto_circle_cta_shortcode')){
	function pexeto_circle_cta_shortcode($atts){
		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					'title' => '',
					'small_title' => '',
					'button_link' => '',
					'button_text' => ''
				), $atts ) );

		$html = '<div class="cta-circle">';
		if($small_title) $html.='<h4 class="cta-small-title">'.$small_title.'</h4>';
		$html.='<h2 class="cta-title">'.$title.'</h2>';

		if($button_link && $button_text){
			$html.='<a class="button" href="'.$button_link.'">'.$button_text.'</a>';
		}

		$html.='</div>';

		return $html;
	}
}

add_shortcode( 'pexcirclecta', 'pexeto_circle_cta_shortcode' );

if(!function_exists('pexeto_video_shortcode')){
	function pexeto_video_shortcode($atts){
		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					'src' => '',
					'width' => '',
					'height' => ''
				), $atts ) );

		$html = pexeto_get_video_html($src, $width, $height);

		return $html;
	}
}

add_shortcode( 'pexyoutube', 'pexeto_video_shortcode' );
add_shortcode( 'pexvimeo', 'pexeto_video_shortcode' );
add_shortcode( 'pexflash', 'pexeto_video_shortcode' );

if(!function_exists('pexeto_nivo_slider_shortcode')){
	function pexeto_nivo_slider_shortcode($atts, $content = null){

		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					'sliderid' => ''
				), $atts ) );

		global $pexeto_page, $pexeto_content_sizes, $post;


		$id = isset($post) ? $post->ID : '';

		$size_keys = array(
			'full' => 'fullwidth',
			'left' => 'content',
			'right' => 'content'
		);
		$size_key = $size_keys[$pexeto_page['layout']];
		$slider_data = PexetoCustomPageHelper::get_instance_data( PEXETO_NIVOSLIDER_POSTTYPE, $sliderid );

		$slider_init_data = pexeto_get_nivo_data($slider_data, $size_key, '_content', $id);

		$nivo_html = pexeto_get_nivo_slider_html(
			$slider_init_data['images'], 
			$slider_init_data['options'], 
			$slider_init_data['slider_div_id'], 
			$slider_init_data['height'], 
			$slider_init_data['autoresizing']);

		return '<div class="nivo-content">'.$nivo_html.'</div>';
	}
}

add_shortcode( 'pexnivoslider', 'pexeto_nivo_slider_shortcode' );


if(!function_exists('pexeto_bgsection_shortcode')){
	function pexeto_bgsection_shortcode($atts, $content = null){

		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					'titlecolor' => '',
					'textcolor' => '',
					'bgcolor' => '',
					'title' => '',
					'subtitle' => '',
					'image' => '',
					'style' => 'section-custom',
					'imageopacity' => '',
					'bgimagestyle'=>'static'
				), $atts ) );

		$add_class = $bgimagestyle=='static'?'':' '.$bgimagestyle;

		$html = '<div class="section-full-width '.$style.$add_class.'" style="background-color:#'.$bgcolor.';">';
		if($image){
			$html.='<div style="background-image:url('.$image.'); opacity:'.$imageopacity.';'.
				' filter: alpha(opacity='.((float)$imageopacity*100).');" class="full-bg-image" ></div>';
		}
		$html.= '<div class="section-boxed" style="color:#'.$textcolor.';">';
		$subtitle = $subtitle ? '<h4 style="color:#'.$titlecolor.';" class="sub-title">'.$subtitle.'</h4>' : '';
		if($style=='section-light'){
			$html.=$subtitle;
		}
		$html.=$title ? '<h2 class="section-title" style="color:#'.$titlecolor.';">'.$title.'</h2>' : '';
		if($style!='section-light'){
			$html.=$subtitle;
		}
		$html.= do_shortcode($content);
		$html.='</div></div>';

		return $html;
	}
}

add_shortcode( 'bgsection', 'pexeto_bgsection_shortcode' );


if(!function_exists('pexeto_strip_attr_prefix')){
	function pexeto_strip_attr_prefix($atts){
		$stripped_atts = array();
		foreach ($atts as $key => $value) {
			$stripped_key = str_replace('pex_attr_', '', $key);
			$stripped_atts[$stripped_key] = $value;
		}

		return $stripped_atts;
	}
}



/* ----------------------------------------------------------------------------*
 * SERVICES
 * ---------------------------------------------------------------------------*/

if ( !function_exists( 'pexeto_show_services' ) ) {
	/**
	 * Generates the services boxes HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the boxes
	 */
	function pexeto_show_services( $atts, $content = null ) {
		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					'title' => '',
					'desc' => '',
					'set' => 'default',
					'columns' => '3',
					'layout' => 'photo',
					'parallax' => 'disabled',
					'crop' => 'enabled'
				), $atts ) );

		$columns = intval($columns);
		if ( !empty( $title ) || !empty( $desc ) ) {
			$columns+=1;
		}



		//get the services boxes from this set
		$boxes_data = PexetoCustomPageHelper::get_instance_data( PEXETO_SERVICES_POSTTYPE, $set );
		$boxes = array();
		$data_keys=array( 'box_title', 'box_image', 'box_desc', 'box_link' );
		foreach ($boxes_data['posts'] as $box_post) {
			$boxes[]=pexeto_get_multi_meta_values( $box_post->ID, $data_keys, PEXETO_CUSTOM_PREFIX );
		}

		if($layout=='circle'){
			$image_width = 235;
			$image_height = 235;
		}else if($layout=='list'){
			$image_width = 55;
			$image_height = 55;
		}else{
			$img_size = pexeto_get_image_size_options($columns, 'services');
			$image_width = $img_size['width'];
			$image_height = round($image_width/1.5);
		}

		if($crop == 'enabled'){
			foreach ($boxes as $key => $box) {
				$boxes[$key]['box_image'] = pexeto_get_resized_image($box['box_image'], $image_width, $image_height);
			}
		}
		

		$parallax = $parallax=='enabled' ? true : false;

		if($layout!=='list'){
			return pexeto_get_services_standard_style_html($boxes, $layout, $title, $desc, $columns, $parallax);
		}else{
			return pexeto_get_services_list_style_html($boxes, $title, $desc, $parallax);
		}

		

		

		return $html;
	}
}
add_shortcode( 'pexservices', 'pexeto_show_services' );


/* ----------------------------------------------------------------------------*
 * CAROUSEL
 * ---------------------------------------------------------------------------*/

if ( !function_exists( 'pexeto_show_carousel' ) ) {

	/**
	 * Generates the portfolio carousel HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the carousel
	 */
	function pexeto_show_carousel( $atts, $content = null ) {
		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					'title' => '',
					'cat' => '-1',
					'orderby' => 'date',
					'order' => 'DESC',
					'maxnum' => '-1',
					'link' => null,
					'link_title' => ''
				), $atts ) );
		$html='';

		$args = array(
			'post_type'=>PEXETO_PORTFOLIO_POST_TYPE,
			'orderby'=>$orderby,
			'order'=>$order,
			'posts_per_page'=>$maxnum
		);

		if ( $cat!='-1' ) {
			$args[PEXETO_PORTFOLIO_TAXONOMY]=get_term_by( 'id', $cat, PEXETO_PORTFOLIO_TAXONOMY )->slug;
		}


		$car_posts = get_posts( $args );

		$html = pexeto_build_portfolio_carousel_html( $car_posts, $title, $link, $link_title );

		return $html;
	}
}
add_shortcode( 'pexcarousel', 'pexeto_show_carousel' );

/* ----------------------------------------------------------------------------*
 * TABS
 * ---------------------------------------------------------------------------*/

if ( !function_exists( 'pexeto_show_tabs' ) ) {

	/**
	 * Generates the tabs element HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the tabs element
	 */
	function pexeto_show_tabs( $atts, $content = null ) {
		extract( shortcode_atts( array(
					'titles' => '',
					'width' => 'medium'
				), $atts ) );
		$titlearr=explode( ',', $titles );
		$html='<div class="tabs-container"><ul class="tabs ">';
		if ( $width=='small' ) {
			$wclass='w1';
		}elseif ( $width=='big' ) {
			$wclass='w3';
		}else {
			$wclass='w2';
		}
		foreach ( $titlearr as $title ) {
			$html.='<li class="'.$wclass.'"><a href="#">'.$title.'</a></li>';
		}
		$html.='</ul><div class="panes">'.do_shortcode( $content ).'</div></div>';
		return $html;
	}
}
add_shortcode( 'tabs', 'pexeto_show_tabs' );


if ( !function_exists( 'pexeto_show_pane' ) ) {

	/**
	 * Generates the single tab pane HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the single tab pane
	 */
	function pexeto_show_pane( $atts, $content = null ) {
		return '<div>'.do_shortcode( $content ).'</div>';
	}
}
add_shortcode( 'pane', 'pexeto_show_pane' );


if ( !function_exists( 'pexeto_show_accordion' ) ) {

	/**
	 * Generates the accordion element HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the accordion element
	 */
	function pexeto_show_accordion( $atts, $content = null ) {
		return '<div class="accordion-container">'.do_shortcode( $content ).'</div>';
	}
}
add_shortcode( 'accordion', 'pexeto_show_accordion' );

if ( !function_exists( 'pexeto_show_apane' ) ) {

	/**
	 * Generates the accordion pane HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the accordion pane
	 */
	function pexeto_show_apane( $atts, $content = null ) {
		extract( shortcode_atts( array(
					'title' => ''
				), $atts ) );
		return '<div class="accordion-title">'.$title
			.'<span class="ac-indicator"></span></div><div class="pane">'
			.do_shortcode( $content ).'</div>';
	}
}
add_shortcode( 'apane', 'pexeto_show_apane' );

/* ----------------------------------------------------------------------------*
 * TESTIMONIALS
 * ---------------------------------------------------------------------------*/

if ( !function_exists( 'pexeto_show_testim' ) ) {

	/**
	 * Generates the testimonial element HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the testimonial element
	 */
	function pexeto_show_testim( $atts, $content = null ) {
		$atts = pexeto_strip_attr_prefix($atts);
		extract( shortcode_atts( array(
					"name" => '',
					"img" =>'',
					"org" =>'',
					"link" =>'',
					"occup" =>'',
					'parallax' => 'disabled'
				), $atts ) );

		$addClass=$img?'':' no-image';
		if($parallax=='enabled'){
			$addClass.=' pexeto-parallax';
		}
		$testim='<div class="testimonial-container'.$addClass.'">';
		$testim.='<div class="testimonial-info">';
		if ( $img ) $testim.='<img class="img-frame testimonial-img" src="'.
			pexeto_get_resized_image($img, 150, 150, true).'" alt="" />';
		$testim.='<div class="testimonials-details"><h2>'.$name.'</h2><span>'.$occup;
		if ( $org ) {
			$testim.=' / ';
			if ( $link ) $testim.='<a href="'.$link.'">';
			$testim.=$org;
			if ( $link ) $testim.='</a>';
		}
		$testim.='</span></div></div>';
		$testim.='<blockquote><p>'.do_shortcode( $content ).'</p></blockquote><div class="clear">';
		$testim.='</div></div>';
		return $testim;
	}
}
add_shortcode( 'pextestim', 'pexeto_show_testim' );


/* ----------------------------------------------------------------------------*
 * CONTACT FORM
 * ---------------------------------------------------------------------------*/

if ( !function_exists( 'pexeto_contact_form' ) ) {

	/**
	 * Generates the contact form HTML from the shortcode
	 *
	 * @param array   $atts    the shortcode attributes
	 * @param string  $content
	 * @return string          the generated HTML code of the contact form
	 */
	function pexeto_contact_form() {
		$html='<div class="widget-contact-form">
			<form action="'.get_template_directory_uri().'/includes/send-email.php" method="post" 
			id="submit-form" class="pexeto-contact-form">
			<div class="error-box error-message"></div>
			<div class="info-box sent-message"></div>
			<input type="text" name="name" class="required placeholder" id="name_text_box" 
			placeholder="'.__( 'Name', 'pexeto' ).'" />
			<input type="text" name="email" class="required placeholder email" 
			id="email_text_box" placeholder="'.__( 'Your e-mail', 'pexeto' ).'" />
			<textarea name="question" rows="" cols="" class="required"
			id="question_text_area"></textarea>
			<input type="hidden" name="widget" value="true" />

			<a class="button send-button"><span>'.__( 'Send', 'pexeto' ).'</span></a>
			<div class="contact-loader"></div><div class="check"></div>

			</form><div class="clear"></div></div>';
		return $html;
	}
}

add_shortcode( 'contactform', 'pexeto_contact_form' );
