<?php
include_once plugin_dir_path(__FILE__).'/lib/instagram.php';

class Instagram_Flux{
	
	public $_instagram;
	
	public function __construct(){
		$this->_instagram = new Instagram(get_option('instagram_name'), get_option('instagram_secretkey'));
		
		/*if(get_option('instagram_socialWall') == 1 ){
			$this->add_instagram_stream_tags();
		}*/
	}
	
	//user
	public function add_instagram_stream_tags(){
		global $wpdb;		
		$medias = $this->_instagram->getUserMedia(array('count' => 2),"bekey");

		//Détermine la date min de la periode
		$startDate = strtotime(get_option('social_wall_date'));		

		//Boucle les requêtes twitter afin de récupérer touts les tweets sur la période donnée
		while (++$i) {

			if($i > 1){
				$medias = $this->_instagram->getUserMedia(array('count' => 2, 'max_id' => $medias->pagination->next_max_id),"bekey");
			}
			
			//récupere la date du dernier media de chaque requête
			$lastMedia = end($medias->data);
			$created_dateLastMedia = $lastMedia->caption->created_time;

			foreach($medias->data as $key => $media){
				
				//Formate la date qui provient du flux instagram
				$created_date = date('Y-m-d H:i:s', $media->caption->created_time);
				//Test l'id du tweet avant de l'enregistré afin d'éviter les doublons
				$media_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}socialmedia WHERE content_id = '".$media->id."'");
				if($media_id == null && $media->caption->created_time > $startDate){
					$wpdb->insert( 
						'wp_socialmedia', 
						array( 
							'type' => 'instagram',
							'content' => $media->caption->text,
							'content_url' => $media->images->standard_resolution->url, 
							'content_id' => $media->id,
							'author_name' => $media->user->username,
							'author_avatar' => $media->user->profile_picture,

							'date' => $created_date 
						)
					);
				}
				
			}
			
			//stop la boucle si la date du dernier media est plus ancienne que la date min de la période
			if( ($created_dateLastMedia < $startDate)){ break; }
		}		
	}
	
	
}

//----------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------

class Instagram_Widget extends Wp_Widget{
	
	public $_instagram;
	
	public function __construct(){
		parent::__construct('instagram_widget', 'Instagram', array('description' => 'Affichage du flux instagram sur votre site'));
		$this->_instagram = new Instagram(get_option('instagram_name'), get_option('instagram_secretkey'));		
    }
    
	//gestion front-office du widget
    public function widget($args, $instance){
		
		//Paramètres du plugin
       	if($instance['number_picture']){ $count = $instance['number_picture']; }else{ $count = 4; }
		$search = $instance['search'];
		$appearance = $instance['appearance'];
		//requête instagram
		if($search == "tag" && !empty($instance['pseudo'])){
			$medias = $this->_instagram->getTags(array('count' => $count),$instance['pseudo']);
		}else{
			
			$medias = $this->_instagram->getUserMedia(array('count' => $count),$instance['pseudo']);
		}
		//mise en forme du contenu du widget
		echo $args['before_widget'];
		echo '<h2 class="widget-title">'.apply_filters('widget_title', $instance['title']).'</h2>';
		if($appearance == "slider"){
			echo '<div id="slider" class="slider-instagram">';
			if(!empty($medias)){
				foreach($medias->data as $media){
					echo '<a href="'.$media->link.'" class="item" target="_blank"><img src="'.$media->images->low_resolution->url.'"></a>';
				}
			}
			echo '</div>';
		}else{
		echo '<ul class="widget-instagram">';
			if(!empty($medias)){
				foreach($medias->data as $media){
					echo '<li><a href="'.$media->link.'" class="item" target="_blank"><img src="'.$media->images->low_resolution->url.'" /><div class="overlay"></div></a></li>';
				}
			}
		echo '</ul>';
		}
		echo $args['after_widget'];
    }
	
	//gestion back-office du widget
	public function form($instance){
		
		$search = $instance['search'];
		$appearance = $instance['appearance'];
		if( isset($instance['title']) ){ $title = $instance['title']; }else{ $title = ''; }
        if( isset($instance['pseudo']) ){ $pseudo = $instance['pseudo']; }else{ $pseudo = ''; }
		if( isset($instance['number_picture']) ){ $number_picture = $instance['number_picture']; }else{ $number_picture = ''; }?>
        
    	<p>
		<?php echo '<label for="'. $this->get_field_name( 'title' ) .'">'. _e( 'Titre :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'title' ).'" name="'. $this->get_field_name( 'title' ) .'" type="text" value="'. $title .'" />'; ?>
		</p>

        <p>
		<?php echo '<label for="'. $this->get_field_name( 'pseudo' ) .'">'. _e( 'Terme de la recherche :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'pseudo' ).'" name="'. $this->get_field_name( 'pseudo' ) .'" type="text" value="'. $pseudo .'" />'; ?>
        </p>
        
        <p>
        <label><input type="radio" name="<?php echo $this->get_field_name( 'search' ); ?>" id="<?php echo $this->get_field_id( 'search' ); ?>" value="user" <?php if ( $search == "user" ) echo 'checked="checked"'; ?> />Utilisateur</label>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'search' ); ?>" id="<?php echo $this->get_field_id( 'search' ); ?>" value="tag" <?php if ( $search == "tag" ) echo 'checked="checked"'; ?> />Mot clef</label>
        
		</p>
        
        <p>
		<?php echo '<label for="'. $this->get_field_name( 'number_picture' ) .'">'. _e( 'Nombre de photos à afficher :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'number_picture' ).'" name="'. $this->get_field_name( 'number_picture' ) .'" type="text" value="'. $number_picture .'" />'; ?>
		</p>
		
        <p>
        <label><input type="radio" name="<?php echo $this->get_field_name( 'appearance' ); ?>" id="<?php echo $this->get_field_id( 'appearance' ); ?>" value="grid" <?php if ( $appearance == "grid" ) echo 'checked="checked"'; ?> />Grille</label>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'appearance' ); ?>" id="<?php echo $this->get_field_id( 'appearance' ); ?>" value="slider" <?php if ( $appearance == "slider" ) echo 'checked="checked"'; ?> />Slider</label>
        
		</p>
		<?php
	}
	
 }