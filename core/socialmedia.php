<?php

class Socialmedia{
	
	public $_post_id;
	
	public function __construct(){
		$this->delete_post();
		
		add_filter('the_content', array($this, 'add_share_button'));
		add_filter('the_content', array($this, 'add_tweet_button'));
		
		add_action('wp_head', array($this, 'add_meta_opengraph'));
		add_action('wp_footer', array($this, 'add_script_callback'));
		
		add_action( 'wp_ajax_nopriv_ajax_sharecount', array($this, 'ajax_sharecount'));

	}
	
	//Début suppréssion des posts socialmedia dans la bdd---------------------------------------------------
	public function delete_post(){
		global $wpdb;
		
		//formate la date pour avoir la data maxi des posts
		$date = strtotime(date('Y-m-d H:i:s'));		
		$date = date('Y-m-d H:i:s', strtotime( get_option('social_wall_date') , $date));
		
		$wpdb->query("DELETE FROM {$wpdb->prefix}socialmedia WHERE date <= '".$date."'");
		
	}
	//Fin suppréssion des posts socialmedia dans la bdd-----------------------------------------------------
	
	
	
	public function add_tweet_button($content){
		//posts
		if ( is_single() && !empty(get_option('twitter_postButton')) ){
			$content = $this->position_share_button($content, 'twitter');
		}
		//pages
		if ( is_page() && !empty(get_option('twitter_pageButton')) ){
			$content = $this->position_share_button($content, 'twitter');
		}
		return $content;
	}
	
	public function add_share_button($content){
		//posts
		if ( is_single() && !empty(get_option('facebook_postButton')) ){
			$content = $this->position_share_button($content, 'facebook');
		}
		//pages
		if ( is_page() && !empty(get_option('facebook_pageButton')) ){
			$content = $this->position_share_button($content, 'facebook');
		}
		return $content;
	}
	
	public function position_share_button($content, $name_socialnetwork){
		
		$titre = substr(get_the_title(),0,100);
		$url = get_permalink();
		$this->_post_id = get_the_ID();
		
		if( !empty( get_option($name_socialnetwork.'_classButton') ) ){
			$class = "class='" .get_option($name_socialnetwork.'_classButton'). " share-".$name_socialnetwork."'";
		}else{
			$class = "class='" .get_option($name_socialnetwork.'_designButton', 'medium'). " share-".$name_socialnetwork."'";
		}
		
		if( !empty( get_option($name_socialnetwork.'_tracking') ) ){
			
			if( empty( get_option($name_socialnetwork.'_nameTracking') ) ){
				$nameTracking = get_the_title();
			}else{
				$nameTracking = get_option($name_socialnetwork.'_nameTracking');
			}
		
			$gatracking = 'onclick="ga(\'send\', \'event\', \'Réseaux sociaux\', \'Link\', \'' .$nameTracking. '\');"';
		}
		
		if($name_socialnetwork === "twitter"){
			$button = "<a href='https://twitter.com/intent/tweet?status=" .$titre. " - " .$url. " via @" .get_option('twitter_name'). "' " .$class.$gatracking. ">" .get_option('twitter_nameButton', 'tweeter'). "</a>";
		}elseif($name_socialnetwork === "facebook"){
			$button = "<a href='http://www.facebook.com/share.php?u=".$url."' target='_blank' " .$class.$gatracking. ">" .get_option('facebook_nameButton', 'partager'). "</a>";
		}
		
		//Positionement des boutons de partage
		if(get_option($name_socialnetwork.'_positionButton') === "top")
			$newcontent = $button . $content;
		else{
			$newcontent = $content . $button;
		}
		return $newcontent;
		
	}
	
	public function add_meta_opengraph(){
		echo '<!-- Début Open graph facebook -->';
		echo '<meta property="og:type" content="article" />';
		echo '<meta property="og:site_name" content="'.get_bloginfo('name').'"/>';
		echo '<meta property="og:url" content="'.get_the_permalink().'" />';
		echo '<meta property="og:title" content="'.get_the_title().'" />';
		echo '<meta property="og:description" content="'.get_the_content().'" />';
		echo '<meta property="og:locale" content="fr_FR" />';
		echo '<!-- Fin Open graph facebook -->';
	}

	public function add_script_callback(){
		?>
<script type="text/javascript">
			$(document).ready(function(){
				
				//Callback twitter
				function reward_user( event ) {
					if ( event ) {
						
						data_item = {};
						data_item['id'] = <?php echo $this->_post_id ?>;
		
						$.ajax({
							url: "/wp-admin/admin-ajax.php",
							type: "POST",
							data: {
								'action': 'ajax_sharecount',
								'data': data_item 
							},
							success: function(data){
									
							}
						});
	
					}
				}
 
				window.twttr = (function (d,s,id) {
					var t, js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
					js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
					return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
				}(document, "script", "twitter-wjs"));
 
				twttr.ready(function (twttr) {
					twttr.events.bind('tweet', reward_user);
				});
				
			})
		</script>
		<?php
		
	}
	
	public function ajax_sharecount(){
		
		global $wpdb;
		var_dump($_POST['data']);
		$id = $_POST['data']['id'];
		$posts_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}socialmedia_sharecount WHERE post_id = ".$id );
		$twitter_count = $posts_id->twitter_count +1;
						
		if($posts_id == null){
			$wpdb->insert( 
				'wp_socialmedia_sharecount', 
				array( 
					'post_id' => $this->_post_id,
					'twitter_count' => 1, 
					'created_date' => "",
					'modified_date' => ""
				)
			);
		}else{
			$wpdb->update('wp_socialmedia_sharecount', array('twitter_count' => $twitter_count), array('post_id' => $id));
		}
		
	}
	
 }
 ?>
<!--<script src="//connect.facebook.net/en_US/all.js"></script>
 <script>
 


  



        FB.init({
          appId      : '785451964876620',
          xfbml      : true,
          version    : 'v2.1'
        });
      


FB.ui(
 {
  method: 'share',
  href: 'https://developers.facebook.com/docs/'
}, function(response){});
 
</script>-->
