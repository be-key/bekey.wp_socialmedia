<?php
//Détecte si le flux facebook doit provenir d'un profil ou d'une page
	function check_facebook_stream(){
		if(get_option('facebook_stream') == 'profil' || empty(get_option('facebook_page_id'))){
			$user_page_id = 'me';
		}if(get_option('facebook_stream') == 'page' || !empty(get_option('facebook_page_id'))){
			$user_page_id = get_option('facebook_page_id');
		};
		return $user_page_id;
	}
	
?>