<h3>Paramètres généraux</h3>
<table class="form-table">
	<tbody>
		<tr>
            <th scope="row">
                <label for="twitter_name">Pseudo twitter</label>
            </th>
            <td>
                <input type="text" name="twitter_name" class="regular-text" value="<?php echo get_option('twitter_name'); ?>"/>
            </td>
		</tr>
        <tr>
            <th scope="row">
                <label for="twitter_auth_key">Auth token</label>
            </th>
            <td>
                <input type="text" name="twitter_oauth_token" class="regular-text" value="<?php echo get_option('twitter_oauth_token'); ?>"/>
            </td>
		</tr>
        <tr>
            <th scope="row">
                <label for="twitter_oauth_token_secret">Auth token secret</label>
            </th>
            <td>
                <input type="text" name="twitter_oauth_token_secret" class="regular-text" value="<?php echo get_option('twitter_oauth_token_secret'); ?>"/>
                <p class="description">Afin d'obtenir ces informations (Auth key et Secret Key) vous avez besoin de créer une application Twitter.</p>
                <p class="description">Vous trouverez plus d'information à ce sujet sur le site <a href="#">twitter.dev</a>.</p>
            </td>
		</tr>
        <tr>
            <th scope="row">
                <label for="twitter_socialWallTermSearch">Terme de la recherche</label>
            </th>
            <td>
                <input type="text" name="twitter_socialWallTermSearch" class="regular-text" value="<?php echo get_option('twitter_socialWallTermSearch'); ?>"/>
                <p class="description">Vous pouvez rechercher les tweets depuis un profil utilisateur ou un hastag. Pour cela préfixez repectivement le terme par @ ou #</p>
            </td>
		</tr>
	</tbody>
</table>

<h3>Paramètres du boutton Twitter</h3>
<table class="form-table">
	<tbody>
        <tr>
            <th scope="row">
                Affichage
            </th>
            <td>
            	<fieldset>
                <input type="checkbox" name="twitter_postButton" value="1" <?php echo checked(1, get_option('twitter_postButton'), false ); ?> />
                <label>Afficher le bouton "Tweet" sur les posts</label>
                <br />
                <input type="checkbox" name="twitter_pageButton" value="1" <?php echo checked(1, get_option('twitter_pageButton'), false ) ?> />
                <label>Afficher le bouton "Tweet" sur les pages</label>
                </fieldset>
            </td>
		</tr>
        <tr>
            <th scope="row">
				Position du bouton
            </th>
            <td>
            	<p><label><input type="radio" name="twitter_positionButton" value="top" <?php if ( get_option('twitter_positionButton') == "top" ) echo 'checked="checked"'; ?> />Haut (sous le titre)</label></p>
				<p><label><input type="radio" name="twitter_positionButton" value="bottom" <?php if ( get_option('twitter_positionButton') == "bottom" ) echo 'checked="checked"'; ?> />Bas (sous le contenu)</label></p>
            </td>
		</tr>
        <tr>
            <th scope="row">
                Intitulé du bouton "Tweet"
            </th>
            <td>
            	<input type="text" name="twitter_nameButton" value="<?php echo get_option('twitter_nameButton', 'Tweeter'); ?>"/>
            </td>
		</tr>
        <tr>
            <th scope="row">
                Mise en forme
            </th>
            <td>
				<p><label><input type="radio" name="twitter_designButton" value="small" <?php if ( get_option('twitter_designButton') == "small" ) echo 'checked="checked"'; ?> />Petit</label></p>
				<p><label><input type="radio" name="twitter_designButton" value="medium" <?php if ( get_option('twitter_designButton') == "medium" ) echo 'checked="checked"'; ?> />Medium</label></p>
				<p><label><input type="radio" name="twitter_designButton" value="large" <?php if ( get_option('twitter_designButton') == "large" ) echo 'checked="checked"'; ?> />Large</label></p>
            </td>
		</tr>
        <tr>
            <th scope="row">
                Mise en forme personnalisée
            </th>
            <td>
				<input type="text" name="twitter_classButton" class="regular-text" value="<?php echo get_option('twitter_classButton'); ?>" placeholder=".class-personnalisée"/>
                <p class="description">Pour personnaliser l'apparence de votre bouton "Tweeter" ajoutez la class de votre boutton dans le champs ci-dessus.</p>
            </td>
		</tr>
	</tbody>
</table>

<h3>Tracking du boutton Twitter <small>(Google Analytics)</small></h3>
<table class="form-table">
	<tbody>
        <tr>
            <th scope="row">
               
            </th>
            <td>
            	<input type="checkbox" name="twitter_tracking" value="1" <?php echo checked(1, get_option('twitter_tracking'), false ); ?> /><label for="twitter_tracking">Activer le tracking</label>
            </td>
		</tr>
        <tr>
            <th scope="row">
				Nom utilisé pour le tracking
            </th>
            <td>
            	<input type="text" name="twitter_nameTracking" class="regular-text" value="<?php echo get_option('twitter_nameTracking'); ?>"/>
                <p class="description">Par défaut le nom utilisé pour le tracking est le titre du post partagé. Si vous souhaitez un nom générique pour tout les partages Twitter, renseigner le champ ci-dessus.</p>
            </td>
		</tr>
	</tbody>
</table>