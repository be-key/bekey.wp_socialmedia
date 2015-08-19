<?php
include_once plugin_dir_path(__FILE__).'/lib/twitter.php';

class Twitter_Flux{
	
	public $_twitter;
	public $_tweets;
	
	//Construction de la classe twitter
	public function __construct(){
		$this->_twitter = new Twitter();
		
		//Détecte le préfix du terme recherché (@ : user, # : mot clef)
		$terms = get_option('twitter_socialWallTermSearch' );
		$terms_prefix = substr($terms, 0, 1);
		
		//mets automatiquement à jour le social wall si celui-ci est actif
		if(get_option('twitter_socialWall') == 1 ){
			if($terms_prefix == "#"){
				$this->add_tweet_stream_tags();
			}else{
				$this->add_tweet_stream_user();
			}
		}
		
		//add_filter('the_content', array($this, 'add_tweet_button'));
		
	}

	//Début enregistrement et suppréssion des Tweets dans la bdd--------------------------------------------
	
	//Enregistrement des hastag
	public function add_tweet_stream_tags(){
		global $wpdb;
		
		//Paramètres de la requête et requête twitter
		$count = 100;
		$terms = get_option('twitter_socialWallTermSearch' );
		$params = array( 'count' => $count, 'q' => $terms );		
		$tweets = $this->_tweets =  $this->_twitter->get('search/tweets', $params);
		$tweets = $tweets->statuses;
		
		//Détermine la date min de la periode
		$startDate = strtotime(get_option('twitter_socialWallDate'));		
		
		//Boucle les requêtes twitter afin de récupérer touts les tweets sur la période donnée
		while (++$i) {
			
			//$lastTweet = end($tweets);
			
			if($i > 1){
				$params = array( 'count' => $count, 'max_id' => $lastTweet->id_str, 'q' => $terms );
				$tweets = $this->_tweets =  $this->_twitter->get('search/tweets', $params);
				$tweets = $tweets->statuses;
			}
			
			//Récupere la date du dernier tweet apres chaque requête
			$lastTweet = end($tweets);
			$created_dateLastTweet = strtotime($lastTweet->created_at);
			
			//Enregiste les tweet en bdd
			foreach($tweets as $key => $tweet){
				//Formate la date qui provient du flux twitter
				$created_date = date('Y-m-d H:i:s', strtotime($tweet->created_at));
				
				//Test l'id du tweet avant de l'enregistré afin d'éviter les doublons
				$tweet_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}socialmedia WHERE content_id = ".$tweet->id_str);
				if($tweet_id == null && strtotime($tweet->created_at) > $startDate){
					$wpdb->insert( 
						'wp_socialmedia', 
						array( 
							'type' => 'tweet',
							'content' => $tweet->text, 
							'content_id' => $tweet->id_str,
							'author_name' => $tweet->user->screen_name,
							'author_avatar' => $tweet->user->profile_image_url,
							'author_profil_url' => "https://twitter.com/".$tweet->user->screen_name,
							'date' => $created_date 
						)
					);
				}
	
			}
			//limite l'enregistrement à 600 tweet maximum
			if( ($created_dateLastTweet < $startDate) || $i >= 3){ break; }
		}
		
	}
	
	//Enregistrement des utilisateurs
	public function add_tweet_stream_user(){
		global $wpdb;
		
		//Paramètres de la requête et requête twitter
		$count = 200;
		$terms = substr(get_option('twitter_socialWallTermSearch' ), 1);
		$params = array( 'count' => $count, 'screen_name' => $terms );
		$tweets = $this->_tweets =  $this->_twitter->get('statuses/user_timeline', $params);
			
		//Détermine la date min de la periode
		$startDate = strtotime(get_option('social_wall_date'));
		
		//Boucle les requêtes twitter afin de récupérer touts les tweets sur la période donnée
		while (++$i) {
			
			if($i > 1){
				$params = array( 'count' => $count, 'screen_name' => $terms, 'max_id' => $lastTweet->id_str );
				$tweets = $this->_tweets =  $this->_twitter->get('statuses/user_timeline', $params);
			}
			
			//Récupere la date du dernier tweet apres chaque requête
			$lastTweet = end($tweets);
			$created_dateLastTweet = strtotime($lastTweet->created_at);
			
			//Enregistre les tweets en bdd
			foreach($tweets as $key => $tweet){
				//Formate la date qui provient du flux twitter
				$created_date = date('Y-m-d H:i:s', strtotime($tweet->created_at));
				
				//test l'id du tweet avant de l'enregistré afin d'éviter les doublons
				$tweet_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}socialmedia WHERE content_id = ".$tweet->id_str);
				if($tweet_id == null && strtotime($tweet->created_at) > $startDate){
					$wpdb->insert( 
						'wp_socialmedia', 
						array( 
							'type' => 'tweet',
							'content' => $tweet->text, 
							'content_id' => $tweet->id_str,
							'author_name' => $tweet->user->screen_name,
							'author_avatar' => $tweet->user->profile_image_url,
							'author_profil_url' => "https://twitter.com/".$tweet->user->screen_name,
							'date' => $created_date 
						)
					);
				}
	
			}
			//limite l'enregistrement à 600 tweet maximum
			if( ($created_dateLastTweet < $startDate) || $i >= 3){ break; }
		}
		
	}
	
	//Supprime tout les tweets de la base de donnée
	public function remove_tweet_stream(){
		global $wpdb;
		$wpdb->query("DELETE FROM {$wpdb->prefix}socialmedia WHERE type = 'tweet'");
	}
	
	//fin enregistrement et suppréssion des Tweets dans la bdd----------------------------------------------
	
	//Début mise en place du bouton Partage-----------------------------------------------------------------
	
	//Ajoute un bouton partage sur les posts et/ou les pages
	
	
	//Fin mise en place du bouton Partage-----------------------------------------------------------------
	
}

//----------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------

class Twitter_Widget extends Wp_Widget{
	
	public $_twitter;
	
	public function __construct(){
		
		
		parent::__construct('twitter_widget', 'Twitter', array('description' => 'Affichage du flux twitter sur votre site'));
		$this->_twitter = new Twitter();
		
    }
    
	

	//gestion front-office du widget
    public function widget($args, $instance){
		if(isset($instance['retweet'])){ $instance['retweet'] = true; }else{ $instance['retweet'] = false; }
		$search = $instance['search'];
		
		if($search == "tag"){
			$params = array(
				'q' => $instance['pseudo'],
				'count' => $instance['number_tweet'],
			);
			$tweets = $this->_twitter->get('search/tweets', $params);
			$tweets = $tweets->statuses;
		}elseif($search == "user"){
			$params = array(
				'screen_name' => $instance['pseudo'],
				'count' => $instance['number_tweet'],
				'include_rts' => $instance['retweet']
			);
			$tweets = $this->_twitter->get('statuses/user_timeline', $params);
		}
		
		
		//mise en forme du contenu du widget
		echo $args['before_widget'];

		echo '<h2 class="widget-title">'.apply_filters('widget_title', $instance['title']).'</h2>';
		
		echo '<ul>';
			if(!empty($tweets)){
				foreach($tweets as $tweet){
					echo '<li>'.$tweet->text.'</li>';
				}
			}else{
				echo '<li>Nous n\'avons pas trouvé d\'utilisateurs possédant ce "pseudo", merci de modifier les paramètres du widget.</li>';
			}
		echo '</ul>';

		echo $args['after_widget'];
    }
	
	//gestion back-office du widget
	public function form($instance){
		
		$search = $instance['search'];
		if( isset($instance['title']) ){ $title = $instance['title']; }else{ $title = ''; }
        if( isset($instance['pseudo']) ){ $pseudo = $instance['pseudo']; }else{ $pseudo = ''; }
		if( isset($instance['number_tweet']) ){ $number_tweet = $instance['number_tweet']; }else{ $number_tweet = ''; }?>
        
    	<p>
		<?php echo '<label for="'. $this->get_field_name( 'title' ) .'">'. _e( 'Titre :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'title' ).'" name="'. $this->get_field_name( 'title' ) .'" type="text" value="'. $title .'" />'; ?>
		</p>
        
        <p>
		<?php echo '<label for="'. $this->get_field_name( 'pseudo' ) .'">'. _e( 'Pseudo :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'pseudo' ).'" name="'. $this->get_field_name( 'pseudo' ) .'" type="text" value="'. $pseudo .'" />'; ?>
		</p>
        
        <p>
        <label><input type="radio" name="<?php echo $this->get_field_name( 'search' ); ?>" id="<?php echo $this->get_field_id( 'search' ); ?>" value="user" <?php if ( $search == "user" ) echo 'checked="checked"'; ?> />Utilisateur</label>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'search' ); ?>" id="<?php echo $this->get_field_id( 'search' ); ?>" value="tag" <?php if ( $search == "tag" ) echo 'checked="checked"'; ?> />Mot clef</label>
		</p>
        
        <p>
		<?php echo '<label for="'. $this->get_field_name( 'number_tweet' ) .'">'. _e( 'Nombre de tweet à afficher :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'number_tweet' ).'" name="'. $this->get_field_name( 'number_tweet' ) .'" type="text" value="'. $number_tweet .'" />'; ?>
		</p>
        
        <p>
        <?php echo '<input type="checkbox" id="'. $this->get_field_id( 'retweet' ) .'" name="'.$this->get_field_name( 'retweet' ).'" value="1"' . checked( 1, $instance['retweet'], false ) . '/>'; ?>
         <?php echo '<label for="'. $this->get_field_name( 'retweet' ) .'">'. _e( 'Afficher les retweet' ) .'</label>'; ?>
		</p>
        
        
		<?php
	}
	
 }