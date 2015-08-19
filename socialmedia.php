<?php
/*
Plugin Name: Social media
Plugin URI: #
Description: Plugin permattant l'intégration d'un flux Twitter et ou Instagram
Version: 0.1
Author: les chinois
Author URI: http://www.leschinois.com
License: GPL2
*/

//création de la class socialmedia
class Plugin_socialmedia{
		
	public $_twitterFlux;
	public $_instagramFlux;
	public $_DribbbleFlux;
	public $_facebookFlux;
	public $_socialmedia;
	
    public function __construct(){
		include_once plugin_dir_path( __FILE__ ).'/core/function.php';
		include_once plugin_dir_path( __FILE__ ).'/core/socialmedia.php';
		$this->_socialmedia = new Socialmedia;
		
		//appel de la class twitter
        include_once plugin_dir_path( __FILE__ ).'/twitter.php';
    	$this->_twitterFlux = new Twitter_Flux();
		
		//appel de la class instagram
        include_once plugin_dir_path( __FILE__ ).'/instagram.php';
    	$this->_instagramFlux = new Instagram_Flux();
		
		//appel de la class dribbble
        include_once plugin_dir_path( __FILE__ ).'/dribbble.php';
    	$this->_DribbbleFlux = new Dribbble_Flux();
		
		//appel de la class facebook
        include_once plugin_dir_path( __FILE__ ).'/facebook.php';
    	$this->_facebookFlux = new facebook_Flux();
		
		//appel des shortcodse twitter
        include_once plugin_dir_path( __FILE__ ).'/twitter_shortcode.php';
    	new Twitter_Shortcode();
		
		add_action('widgets_init', function(){register_widget('Twitter_Widget');});
		add_action('widgets_init', function(){register_widget('Instagram_Widget');});
		//add_action('widgets_init', function(){register_widget('Dribbble_Widget');});
				 
		add_action('admin_menu', array($this, 'add_admin_menu'), 20);
		add_action('admin_bar_menu', array($this, 'add_menu_admin_bar'), 80);
		add_action('admin_init', array($this, 'register_settings'));
		
		add_action('wp_enqueue_scripts', array($this, 'front_end_include_files'));
		add_action('admin_enqueue_scripts', array($this, 'back_end_include_files'));
		
		add_action( 'wp_ajax_ajax_hide_item', array($this, 'ajax_hide_item') );
		
		register_activation_hook(__FILE__, array('Socialmedia', 'install'));

    }

	//Ajout d'un lien permettant la gestion du plugin dans l'administration
	public function add_admin_menu(){
    	add_menu_page('Paramètres du plugin Social Media', 'Social Media', 'manage_options', 'socialmedia', array($this, 'home_html'),'dashicons-share');
		$twitter_add = add_submenu_page('socialmedia', 'Paramètres du plugin Social Media - Twitter', 'Twitter', 'manage_options', 'socialmedia-twitter', array($this, 'twitter_html'));
		$instagram_add = add_submenu_page('socialmedia', 'Paramètres du plugin Social Media - Instagram', 'Instagram', 'manage_options', 'socialmedia-instagram', array($this, 'instagram_html'));
		add_submenu_page('socialmedia', 'Paramètres du plugin Social Media - Dribbble', 'Dribbble', 'manage_options', 'socialmedia-dribbble', array($this, 'dribbble_html'));
		$facebook_add = add_submenu_page('socialmedia', 'Paramètres du plugin Social Media - Facebook', 'Facebook', 'manage_options', 'socialmedia-facebook', array($this, 'facebook_html'));
		add_submenu_page('socialmedia', 'Paramètres du plugin Social Media - Gestion de l\'affichage', 'Social Wall', 'manage_options', 'socialmedia-social-wall', array($this, 'socialwall_html'));
		add_submenu_page('socialmedia', 'Paramètres du plugin Social Media - Gestion de l\'affichage', 'Aperçu', 'manage_options', 'apercu-social-wall', array($this, 'apercu_html'));
		
		add_action('load-'.$twitter_add, array($this, 'process_action_twitter'));
		add_action('load-'.$facebook_add, array($this, 'process_action_facebook'));
		add_action('load-'.$instagram_add, array($this, 'process_action_instagram'));
	}
	
	//Début activation ou désactivation du social wall Twitter----------------------------------------------
	public function process_action_twitter(){
		
		if(isset($_POST['twitter_socialWall'])){
			//Détecte le préfix du terme recherché (@ : user, # : mot clef)
			$terms = get_option('twitter_socialWallTermSearch' );
			$terms_prefix = substr($terms, 0, 1);
			if(get_option('twitter_socialWall') == 0 ){
				//Active le flux twitter dans le socialwall
				update_option('twitter_socialWall', 1);
				if($terms_prefix == "#"){
					$this->_twitterFlux->add_tweet_stream_tags();
				}else{
					$this->_twitterFlux->add_tweet_stream_user();
				}
			}else{
				//Désactive le flux twitter dans le socialwall
				//Supprime les données de type "twitter" dans la base de données
				update_option('twitter_socialWall', 0);
				$this->_twitterFlux->remove_tweet_stream();
			}
		}
		
	}
	
	public function process_action_instagram(){
		if(isset($_POST['instagram_socialWall'])){
			if(get_option('instagram_socialWall') == 0 ){
				//Active le flux instagram dans le socialwall
				update_option('instagram_socialWall', 1);
				$this->_instagramFlux->add_instagram_stream_tags();
			}else{
				//Désactive le flux instagram dans le socialwall
				//Supprime les données de type "instagram" dans la base de données
				update_option('instagram_socialWall', 0);
			}
		}
	}
	
	public function process_action_facebook(){
		if(isset($_POST['facebook_socialWall'])){
			if($_POST['facebook_socialWall'] == 1 ){
				//Active le flux facebook dans le socialwall
				update_option('facebook_socialWall', 1);
				$user_page_id = check_facebook_stream();
				$this->_facebookFlux->add_facebook_stream($user_page_id);
			}elseif($_POST['facebook_socialWall'] == 0 ){
				//Désactive le flux facebook dans le socialwall
				//Supprime les données de type "facebook" dans la base de données
				update_option('facebook_socialWall', 0);
				$this->_facebookFlux->remove_facebook_stream();
			}
		}
		
	}
	//fin activation ou désactivation du social wall Twitter------------------------------------------------
	
	function add_menu_admin_bar() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu(array(
			'title' => 'Social Media', // Titre du menu
			'href' => "/wp-admin/admin.php?page=socialmedia", // Lien du menu
		));
	}
	
	//initialise la base de données pour le socialWall lors de l'installation du plugin
	public static function install(){
    	global $wpdb;
		//BDD social wall
    	$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}socialmedia (id INT AUTO_INCREMENT PRIMARY KEY, type VARCHAR(45) NOT NULL, content TEXT NOT NULL, content_id VARCHAR(255) NOT NULL, author_name VARCHAR(128) NOT NULL, author_avatar VARCHAR(255) NOT NULL, author_profil_url VARCHAR(255) NOT NULL, date DATETIME NOT NULL);");
		//BDD compteur partage
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}socialmedia_sharecount (id INT AUTO_INCREMENT PRIMARY KEY, post_id int(11) NOT NULL, twitter_count int(11) NOT NULL, facebook_count int(11) NOT NULL, created_date datetime NOT NULL, modified_date datetime NOT NULL);");
	}

	//gestion des pages de l'administration
	public function home_html(){
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
	}
	
	public function twitter_html(){
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Renseignez les paramètres du flux Twitter</p>';
		
		echo '<form method="post" action="">';
		if(get_option('twitter_socialWall') == 0 ){
			echo '<input type="hidden" name="twitter_socialWall" value="1" />';
			submit_button('Activer SocialWall Twitter');
		}else{
			echo '<input type="hidden" name="twitter_socialWall" value="0" />';
			submit_button('Désactiver SocialWall Twitter', 'button-danger');
		}
    	echo '</form>';
		
		echo '<form method="post" action="options.php">';
			settings_fields('socialmedia_settings');
			include('template/tpl_twitter.php');
			submit_button();
    	echo '</form>';
	}

	public function instagram_html(){
		
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
		
		echo '<form method="post" action="">';
		settings_fields('instagram_social_wall_activate');
		if(get_option('instagram_socialWall') == 0 ){
			echo '<input type="hidden" name="instagram_socialWall" value="1" />';
			submit_button('Activer SocialWall Instagram');
		}else{
			echo '<input type="hidden" name="instagram_socialWall" value="0" />';
			submit_button('Désactiver SocialWall Instagram', 'button-danger');
		}
    	echo '</form>';
		
		echo '<form method="post" action="options.php">';
			settings_fields('instagram_settings');
			include('template/tpl_instagram.php');
			submit_button();
    	echo '</form>';
	}
	
	public function dribbble_html(){
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
		
		$this->_DribbbleFlux->test();
	}
	
	public function facebook_html(){
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
		
		echo '<form method="post" action="">';
		settings_fields('facebook_social_wall_activate');
		if(get_option('facebook_socialWall') == 0 ){
			echo '<input type="hidden" name="facebook_socialWall" value="1" />';
			submit_button('Activer SocialWall Facebook');
		}else{
			echo '<input type="hidden" name="facebook_socialWall" value="0" />';
			submit_button('Désactiver SocialWall Facebook', 'button-danger');
		}
    	echo '</form>';
		
		$this->_facebookFlux->login();
		echo '<form method="post" action="options.php">';
			settings_fields('facebook_settings');
			include('template/tpl_facebook.php');
			submit_button();
    	echo '</form>';
	}
	
	//Paramétrage Social wall
	public function socialwall_html(){
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<form method="post" action="options.php">';
			settings_fields('social_wall_settings');
			include('template/tpl_socialwall.php');
			submit_button();
    	echo '</form>';
	}
	
	//Affichage du socialWall coté back-office
	public function apercu_html(){
		wp_enqueue_style( 'socialmedia-style' );
		wp_enqueue_script( 'socialmedia-jquery' );
		wp_enqueue_script( 'socialmedia-masonry' );
		wp_enqueue_script( 'socialmedia-default' );
		include('template/tpl_affichage-socialwall.php');
	}
	
	public function ajax_hide_item() {
		global $wpdb;
		
		$id = $_POST['data']['id'];
		$action = $_POST['data']['action'];
		
		if($action == "delete"){
			$statut = -1;
		}elseif($action == "hide"){
			$statut = 0;
		}elseif($action == "show"){
			$statut = 1;
		}
		
		$wpdb->update('wp_socialmedia', array('statut' => $statut), array('id' => $id));
	}
	
	

	//permet d'autoriser l'enregistrement des options
	public function register_settings(){
    	register_setting('socialmedia_settings', 'twitter_name');
		register_setting('socialmedia_settings', 'twitter_oauth_token');
		register_setting('socialmedia_settings', 'twitter_oauth_token_secret');
		register_setting('socialmedia_settings', 'twitter_numberTweet');
		register_setting('socialmedia_settings', 'twitter_postButton');
		register_setting('socialmedia_settings', 'twitter_pageButton');
		register_setting('socialmedia_settings', 'twitter_nameButton');
		register_setting('socialmedia_settings', 'twitter_designButton');
		register_setting('socialmedia_settings', 'twitter_positionButton');
		register_setting('socialmedia_settings', 'twitter_classButton');
		register_setting('socialmedia_settings', 'twitter_tracking');
		register_setting('socialmedia_settings', 'twitter_nameTracking');
		register_setting('socialmedia_settings', 'twitter_socialWall');
		
		register_setting('social_wall_settings', 'social_wall_date');
		//register_setting('socialmedia_settings', 'twitter_socialWallTermSearch');
		register_setting('social_wall_settings', 'social_wall_designDate');
		register_setting('social_wall_settings', 'social_wall_photo');
		register_setting('social_wall_settings', 'social_wall_statut');
		
		register_setting('instagram_settings', 'instagram_name');
		register_setting('instagram_settings', 'instagram_secretkey');
		register_setting('instagram_social_wall_activate', 'instagram_socialWall');
		
		register_setting('facebook_social_wall_activate', 'facebook_socialWall');
		
		register_setting('facebook_settings', 'facebook_stream');
		register_setting('facebook_settings', 'facebook_page_id');
		register_setting('facebook_settings', 'facebook_app_secret');
		register_setting('facebook_settings', 'facebook_app_id');
		register_setting('facebook_settings', 'facebook_postButton');
		register_setting('facebook_settings', 'facebook_pageButton');
		register_setting('facebook_settings', 'facebook_nameButton');
		register_setting('facebook_settings', 'facebook_designButton');
		register_setting('facebook_settings', 'facebook_classButton');
		register_setting('facebook_settings', 'facebook_tracking');
		register_setting('facebook_settings', 'facebook_nameTracking');		
		
		wp_register_style( 'socialmedia-style', plugins_url('style-plugin.css', __FILE__) );
		wp_register_script( 'socialmedia-jquery', plugins_url('/js/jquery-1.11.2.min.js', __FILE__) );
		wp_register_script( 'socialmedia-masonry', plugins_url('/js/masonry.js', __FILE__) );
		wp_register_script( 'socialmedia-default', plugins_url('/js/default.js', __FILE__) );
	}
	
	function front_end_include_files() {
		//css
		wp_enqueue_style('socialmedia-style', plugins_url('style-plugin.css', __FILE__));
		wp_enqueue_style('socialmedia-nivo-slider', plugins_url('/css/nivo-slider.css', __FILE__));
		//js
		wp_enqueue_script('socialmedia-jquery', plugins_url('/js/jquery-1.11.2.min.js', __FILE__) );
		wp_enqueue_script('socialmedia-nivo-slider', plugins_url('/js/jquery.nivo.slider.js', __FILE__));
		wp_enqueue_script('socialmedia-default', plugins_url('/js/default.js', __FILE__) );
	}
	
	function back_end_include_files() {
		wp_enqueue_media();
		//js
		wp_enqueue_script( 'custom-header' );
		wp_enqueue_script( 'socialmedia-default', plugins_url('/js/default.js', __FILE__) );
	}

}

//appel de la class socialmedia
new Plugin_socialmedia();