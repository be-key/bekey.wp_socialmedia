<?php
session_start();

require_once __DIR__ . "/facebook-php-sdk-v4/autoload.php";

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;

class Facebook{
	
	public $_session;
	public $_helper;
	public $_app_id;  //Facebook App ID
	public $_app_secret; //Facebook App Secret
	
	public function __construct(){
		
		FacebookSession::setDefaultApplication(get_option('facebook_app_id'), get_option('facebook_app_secret') );
		
		//FacebookSession::setDefaultApplication($this->_app_id, $this->_app_secret );
		
		//Connecte l'utilisateur à facebook et créer une session
		//Redirige l'utilisateur vers l'url courante
		//$this->_helper = new FacebookRedirectLoginHelper("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
		$this->_helper = new FacebookRedirectLoginHelper("http://wordpress.dev/wp-admin/admin.php?page=socialmedia-facebook");
		
		//Si il n'y pas de session active et d'acces_token on en créer une nouvelle
		if(isset($_SESSION) && isset($_SESSION['fb_token'])){
			$this->_session = new FacebookSession($_SESSION['fb_token']);
			
		}else{
			$this->_session = $this->_helper->getSessionFromRedirect();
		}

		
		//appel de la fonction "logout" si l'utilisateur se deconnecte
		if ($_GET['action'] == 'logout'){$this->logout();}
	}
	
	//Bouton de connexion login/logout
	public function login($permission = array(), $pictureSize = null){
		if($this->_session){
			try{
				$_SESSION['fb_token'] = $this->_session->getToken();
				
				//Retourne les informations du profil utilisateur
				$requestInfo = new FacebookRequest($this->_session, 'GET', '/me');
				$profilInfo = $requestInfo->execute()->getGraphObject('Facebook\graphUser');
				
				//Retourne la photo du profil utilisateur
				$requestPicture = new FacebookRequest($this->_session, 'GET', '/me/picture?type='.$pictureSize.'&redirect=false');
				$profilPicture = $requestPicture->execute()->getGraphObject()->asArray();
				
				//Création du bouton de déconnexion
				//Redirige l'utilisateur vers l'url courante
				$current_url = explode("&code=", $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
				$current_url = $current_url[0];
				echo '<img src="'.$profilPicture['url'].'" alt="photo de profil facebook" class="profilepicture"/>';
				echo 'Successfully logged in! <a href="'.$this->_helper->getLogoutUrl($this->_session, 'http://'.$current_url.'&action=logout' ).'">Logout</a>';
				
			}catch(\Exception $e){
                unset($_SESSION['fb_token']);
				session_destroy();
                header('Location:'.$_SERVER['HTTP_REFERER']);
            }
		}else{
			//création du bouton de connexion
			echo '<a href='.$this->_helper->getLoginUrl($permission).'>se connecter avec facebook</a>';
		}
	}
	
	//function de deconnexion vide l'acess_token et détruit la session active
	public function logout(){
		unset($_SESSION['fb_token']);
		session_destroy();
		header('Location:'.$_SERVER['HTTP_REFERER']);
	}
	
	//retourne les informations de l'utilisateur connecté
	public function infosUser($elts){
		if($this->_session){
			$_SESSION['fb_token'] = $this->_session->getToken();
			//Informations du profil
			if($elts == "profil"){
				$requestProfilInfo = new FacebookRequest($this->_session, 'GET', '/me');
				$profilInfo = $requestProfilInfo->execute()->getGraphObject('Facebook\graphUser')->asArray();
				
				$requestProfilPicture = new FacebookRequest($this->_session, 'GET', '/me/picture?type=small&redirect=false');
				$profilPicture = $requestProfilPicture->execute()->getGraphObject()->asArray();
				
				//merge les infos user et la phopto de profil
				$elts = array_merge($profilInfo, $profilPicture);
			//Photo de profil
			}elseif($elts == "picture"){
				$requestPicture = new FacebookRequest($this->_session, 'GET', '/me/picture?type=large&redirect=false');
				$profilPicture = $requestPicture->execute()->getGraphObject()->asArray();
				$elts = $profilPicture['url'];
			}
			return $elts;
		}else{
			return "Veuillez vous connecter pour continuer !";
		}
	}
	
	//retourne la timeline de l'urilisateur
	public function streamUser($id, $params = null){
		if($this->_session){
			$_SESSION['fb_token'] = $this->_session->getToken();
			
			//Paramètres passé dans la requête Facebook feed
			$params = array(
				'limit' => $params['count'], //Nombre de posts par page
				'until' => $params['next_url_id'] //Url de la page suivante
			);
			$request = new FacebookRequest($this->_session, 'GET', '/'.$id.'/feed', $params);
			$reponseTimeline = $request->execute()->getGraphObject()->asArray();				
			return $reponseTimeline;
		}else{
			return "Veuillez vous connecter pour continuer !";
		}	
	}
	
	//Retourne les likes de l'utilisateurs (pages)
	public function likesUser(){
		if($this->_session){		
			$_SESSION['fb_token'] = $this->_session->getToken();
			$request = new FacebookRequest($this->_session, 'GET', '/me/likes');
			$reponseLikesUser = $request->execute()->getGraphObject()->asArray();
			return $reponseLikesUser;
		}else{
			return "Veuillez vous connecter pour continuer !";
		}
	}
	
}

?>

