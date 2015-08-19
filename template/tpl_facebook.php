<h3>Paramètres généraux</h3>
<?php
	//$this->_facebook = new Facebook();
	//$this->_facebook->login(array('email','user_likes','publish_actions','read_stream'));
?>
<table class="form-table">
	<tbody>
    	<tr>
            <th scope="row">
                <label for="facebook_app_id">App ID</label>
            </th>
            <td>
            	 <input type="text" name="facebook_app_id" class="regular-text" value="<?php echo get_option('facebook_app_id'); ?>"/>
            </td>
		</tr>
        
        <tr>
            <th scope="row">
                <label for="facebook_app_secret">App Secret</label>
            </th>
            <td>
            	 <input type="text" name="facebook_app_secret" class="regular-text" value="<?php echo get_option('facebook_app_secret'); ?>"/>
                 <p>Afin d'obtenir ces informations (App ID et App Secret) vous avez besoin de créer une application facebook.</p><p>Vous trouverez plus d'information à ce sujet sur le site developers.facebook.com.</p>
            </td>
		</tr>
        
        <tr>
            <th scope="row">
                Afficher le flux provenant
            </th>
            <td>
            	<p><label><input type="radio" name="facebook_stream" value="profil" <?php if ( get_option('facebook_stream') == "profil" ) echo 'checked="checked"'; ?> />de votre profil</label></p>
				<p><label><input type="radio" name="facebook_stream" value="page" <?php if ( get_option('facebook_stream') == "page" ) echo 'checked="checked"'; ?> />d'une page</label></p>
            </td>
		</tr>
        <tr>
            <th scope="row">
                <label for="facebook_page_id">Id de la page</label>
            </th>
            <td>
            	 <input type="text" name="facebook_page_id" class="regular-text" value="<?php echo get_option('facebook_page_id'); ?>"/>
                 <p>Trouvez l'id de la page grace aux photos, l'id de la page se trouve dans l'url.</p>
                 <p>exemple : https://www.facebook.com/photo.php?fbid=546233242073852&set=a.363100383720473.90315.<b>287006437996535</b>&type=1&theater</p>
            </td>
		</tr>
	</tbody>
</table>
<h3>Paramètres du boutton Facebook</h3>
<table class="form-table">
	<tbody>
        <tr>
            <th scope="row">
                Affichage
            </th>
            <td>
            	<fieldset>
                <input type="checkbox" name="facebook_postButton" value="1" <?php echo checked(1, get_option('facebook_postButton'), false ); ?> />
                <label for="facebook_postButton">Afficher le boutton "Partager" sur les posts</label>
                <br />
                <input type="checkbox" name="facebook_pageButton" value="1" <?php echo checked(1, get_option('facebook_pageButton'), false ) ?> />
                <label>Afficher le boutton "Partager" sur les pages</label>
                </fieldset>
            </td>
		</tr>
        <tr>
            <th scope="row">
                Intitulé du boutton "Partager"
            </th>
            <td>
            	<input type="text" name="facebook_nameButton" value="<?php echo get_option('facebook_nameButton', 'Partager'); ?>"/>
            </td>
		</tr>
        <tr>
            <th scope="row">
                Mise en forme
            </th>
            <td>
				<p><label><input type="radio" name="facebook_designButton" value="small" <?php if ( get_option('facebook_designButton') == "small" ) echo 'checked="checked"'; ?> />Petit</label></p>
				<p><label><input type="radio" name="facebook_designButton" value="medium" <?php if ( get_option('facebook_designButton') == "medium" ) echo 'checked="checked"'; ?> />Medium</label></p>
				<p><label><input type="radio" name="facebook_designButton" value="large" <?php if ( get_option('facebook_designButton') == "large" ) echo 'checked="checked"'; ?> />Large</label></p>
            </td>
		</tr>
        <tr>
            <th scope="row">
                Mise en forme personnalisée
            </th>
            <td>
				<input type="text" name="facebook_classButton" class="regular-text" value="<?php echo get_option('facebook_classButton'); ?>" placeholder=".class-personnalisée"/>
                <p class="description">Pour personnaliser l'apparence de votre bouton "Tweeter" ajoutez la class de votre boutton dans le champs ci-dessus.</p>
            </td>
		</tr>
	</tbody>
</table>

<h3>Tracking du boutton Facebook <small>(Google Analytics)</small></h3>
<table class="form-table">
	<tbody>
        <tr>
            <th scope="row">
               
            </th>
            <td>
            	<input type="checkbox" name="facebook_tracking" value="1" <?php echo checked(1, get_option('facebook_tracking'), false ); ?> /><label for="facebook_tracking">Activer le tracking</label>
            </td>
		</tr>
        <tr>
            <th scope="row">
				Nom utilisé pour le tracking
            </th>
            <td>
            	<input type="text" name="facebook_nameTracking" class="regular-text" value="<?php echo get_option('facebook_nameTracking'); ?>"/>
                <p class="description">Par défaut le nom utilisé pour le tracking est le titre du post partagé. Si vous souhaitez un nom générique pour tout les partages Facebook, renseigner le champ ci-dessus.</p>
            </td>
		</tr>
	</tbody>
</table>
