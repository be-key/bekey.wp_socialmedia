<?php
include_once plugin_dir_path(__FILE__).'/lib/facebook.php';

		
class Facebook_Flux{
	
	public $_facebook;
	public $_user_page_id;
	//construction de la classe facebook
	public function __construct(){
		$this->_facebook = new Facebook();
		
		//$this->_socialmedia = new Socialmedia;
		$this->_user_page_id = check_facebook_stream();
		
		//$timeline = $this->_facebook->streamUser($this->_user_page_id);
		//var_dump($timeline);
		//mets automatiquement à jour le social wall si celui-ci est actif
		if(get_option('facebook_socialWall') == 1 ){
			$this->add_facebook_stream($this->_user_page_id);
		}
	}
	
	public function login(){
		$this->_facebook->login(array('email','user_likes','publish_actions','read_stream'));
	}
	//Début enregistrement et suppréssion des posts facebook dans la bdd------------------------------------
	
	//ajout les posts facebook facbook en base de données
	//ajoute uniquement les posts publiés sur facebook par l'utilisateur
	public function add_facebook_stream($user_page_id){
		global $wpdb;		
		$posts = $this->_facebook->streamUser($user_page_id,array('count' => 10));
		$userInfo = $this->_facebook->infosUser(get_option('facebook_stream'));
		//test si la fonction streamUser retourne des informations
		if(is_array($posts)){
			
			//Détermine la date minimum de la periode
			$startDate = strtotime(get_option('social_wall_date'));		
	
			//Boucle les requêtes facebook afin de récupérer touts les posts sur la période donnée
			while (++$i) {
				
				//Découpe l'url de la page suivant pour récupérer uniquement l'id de la page
				if(preg_match_all('/\&until=(.*)\&/',$posts['paging']->next,$nextUrl)){ 
					$nextUrlId = implode($nextUrl[1]);            
				}
				
				//Relance la fonction streamUser pour récupérer les posts de la page suivante
				if($i > 1){
					$posts = $this->_facebook->streamUser($user_page_id, array('count' => 10, 'next_url_id' => $nextUrlId));
				}
				
				//Récupere la date du dernier posts apres chaque requête
				$lastPosts = end($posts['data']);
				$created_dateLastPosts = strtotime($lastPosts->created_time);
				
				//Enregiste les post en bdd si ceux-ci ne proviennent pas d'une application
				foreach($posts['data'] as $key => $post){
					
					if(empty($post->application)){
						//Formate la date qui provient du flux facebook
						$created_date = date('Y-m-d H:i:s', strtotime($post->created_time));
						
						//Test l'id du post avant de l'enregistrer afin d'éviter les doublons
						$media_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}socialmedia WHERE content_id = '".$post->id."'");	
						if($media_id == null && strtotime($post->created_time) > $startDate){
							$wpdb->insert( 
								'wp_socialmedia', 
								array( 
									'type' => 'facebook',
									'content' => $post->message,
									'content_picture' => $post->picture,
									'content_url' => $post->link,
									'content_description_url' => $post->description, 
									'content_id' => $post->id,
									'content_params_1' => count($post->likes->data),
									'author_name' => $userInfo['name'],
									'author_avatar' => $userInfo['url'],
									'date' => $created_date 
								)
							);
						}
					}
					
				}
				
				//Stop la boucle si la date du dernier posts est plus ancienne que la date min de la période
				if( ($created_dateLastPosts < $startDate)){ break; }
			}
		}else{
			
		}
	}
	
	//supprime tous les posts facebook de la bdd
	public function remove_facebook_stream(){
		global $wpdb;
		$wpdb->query("DELETE FROM {$wpdb->prefix}socialmedia WHERE type = 'facebook'");
	}

	//fin enregistrement et suppréssion des posts facebook dans la bdd--------------------------------------

 }