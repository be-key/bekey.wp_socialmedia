<?php
include_once plugin_dir_path(__FILE__).'/lib/dribbble.php';

class Dribbble_Flux{
	
	public $_dribbble;
	
	public function __construct(){
		//global $wpdb;
		$this->_dribbble = new Dribbble();
	}
	
	public function test(){
		$shots = $this->_dribbble->getPlayerShots('_Be_Key_', 1 , 3);
		var_dump($shots);
	}

 }
 
 //----------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------

class Dribbble_Widget extends Wp_Widget{
	
	public $_dribbble;
	
	public function __construct(){
		parent::__construct('dribbble_widget', 'Dribbble', array('description' => 'Affichage des projet dribbble sur votre site'));
		$this->_dribbble = new Dribbble();
    }
    
	//gestion front-office du widget
    public function widget($args, $instance){
		
		$projets = $this->_dribbble->getPlayerShots($instance['pseudo'],2 ,$instance['number_project']);
		
		//mise en forme du contenu du widget
		echo $args['before_widget'];

		echo '<h2 class="widget-title">'.apply_filters('widget_title', $instance['title']).'</h2>';
		
		echo '<ul class="widget-instagram">';
			if(!empty($projets)){
				foreach($projets->shots as $projet){
					echo '<li><a href="'.$projet->url.'" class="item" target="_blank"><img src="'.$projet->image_teaser_url.'" /><div class="overlay"></div></a></li>';
				}
			}else{
				echo '<li>Nous n\'avons pas trouvé d\'utilisateurs possédant ce "pseudo", merci de modifier les paramètres du widget.</li>';
			}
		echo '</ul>';

		echo $args['after_widget'];
    }
	
	//gestion back-office du widget
	public function form($instance){
		
		if( isset($instance['title']) ){ $title = $instance['title']; }else{ $title = ''; }
        if( isset($instance['pseudo']) ){ $pseudo = $instance['pseudo']; }else{ $pseudo = ''; }
		if( isset($instance['number_project']) ){ $number_project = $instance['number_project']; }else{ $number_project = ''; }?>
        
    	<p>
		<?php echo '<label for="'. $this->get_field_name( 'title' ) .'">'. _e( 'Titre :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'title' ).'" name="'. $this->get_field_name( 'title' ) .'" type="text" value="'. $title .'" />'; ?>
		</p>
        
        <p>
		<?php echo '<label for="'. $this->get_field_name( 'pseudo' ) .'">'. _e( 'Pseudo :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'pseudo' ).'" name="'. $this->get_field_name( 'pseudo' ) .'" type="text" value="'. $pseudo .'" />'; ?>
		</p>
        
        <p>
		<?php echo '<label for="'. $this->get_field_name( 'number_project' ) .'">'. _e( 'Nombre de projet à afficher :' ) .'</label>'; ?>
		<?php echo '<input class="widefat" id="'. $this->get_field_id( 'number_project' ).'" name="'. $this->get_field_name( 'number_project' ) .'" type="text" value="'. $number_project .'" />'; ?>
		</p>
        
		<?php
	}
	
 }