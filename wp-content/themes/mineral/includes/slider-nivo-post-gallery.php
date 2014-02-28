<?php
/**
 * Nivo slider in the content of a post/page template.
 */
global $pexeto_page, $post, $pexeto_scripts;

$columns = isset($pexeto_page['columns']) ? $pexeto_page['columns'] : 1;
$img_size = pexeto_get_image_size_options($columns, 'blog');

?>
<div class="post-gallery">
	
	<?php
	//print the attachment images
	$attachments = pexeto_get_post_attachments( $post->ID );
	$images = array();


	foreach ( $attachments as $attachment ) {
		$img =  wp_get_attachment_image_src($attachment->ID, 'full');
		$imgurl = pexeto_get_resized_image( $img[0], $img_size['width'], $img_size['height'], $img_size['crop'] );

		$images[]= array(
			'url' => $imgurl,
			'link' => '',
			'description' => $attachment->post_content
		);

	} 

	//slider navigation
	$options = pexeto_get_nivo_args('_post');
	
	$slider_div_id = 'post-gallery-'.$post->ID;

	echo pexeto_get_nivo_slider_html($images, $options, $slider_div_id, $img_size['height'], $img_size['crop']);

	?>

</div>
