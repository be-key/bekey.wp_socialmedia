<?php
class Twitter_Shortcode{
	
	public function __construct(){
		add_shortcode('socialwall_twitter', array($this, 'socialwall_twitter_html'));
		
	}
	
	
	
	public function socialwall_twitter_html($atts, $content){
		global $wpdb;
		
		$atts = shortcode_atts(array('numberposts' => 5), $atts);
		
		$posts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}socialmedia LIMIT ".$atts['numberposts']);
		
		$html = array();
		$html[] = $content;
		$html[] = '<ul>';
		foreach ($posts as $key => $post) {
			$html[] = '<li>';
			if(get_option('twitter_socialWallPhoto') == 1){
				$html[] = '<img src="' .$post->author_avatar. '" width="50" />';
			}
			$html[] = $post->content;
			$html[] = '<br>';
			$html[] = $post->author_name;
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		return implode('', $html);
			
	}
	
	
	
}