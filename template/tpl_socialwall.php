<h3>Paramètres du Social Wall</h3>
<table class="form-table">
	<tbody>
        <tr>
            <th scope="row">
               Période
            </th>
            <td>
            	<select name="social_wall_date">
					<option value="-2 week" <?php if ( get_option('social_wall_date') == "-2 week" ) echo 'selected="selected"'; ?>>2 semaines</option>
            		<option value="-1 month" <?php if ( get_option('social_wall_date') == "-1 month" ) echo 'selected="selected"'; ?>>1 mois</option>
            		<option value="-3 month" <?php if ( get_option('social_wall_date') == "-3 month" ) echo 'selected="selected"'; ?>>3 mois</option>
            		<option value="-6 month" <?php if ( get_option('social_wall_date') == "-6 month" ) echo 'selected="selected"'; ?>>6 mois</option>
				</select>
            </td>
		</tr>
        <tr>
            <th scope="row">
               Statut par défaut
            </th>
            <td>
            	<p><label><input type="radio" name="social_wall_statut" value="1" <?php if ( get_option('social_wall_statut') == "1" ) echo 'checked="checked"'; ?> />Visible</label></p>
				<p><label><input type="radio" name="social_wall_statut" value="0" <?php if ( get_option('social_wall_statut') == "0" ) echo 'checked="checked"'; ?> />Masqué</label></p>
            </td>
		</tr>
        <?php /*?><tr>
            <th scope="row">
				Terme de la recherche
            </th>
            <td>
            	<input type="text" name="twitter_socialWallTermSearch" class="regular-text" value="<?php echo get_option('twitter_socialWallTermSearch'); ?>"/>
                <p class="description">Indiquez le pseudo utilisateur ou le Hastag pour sélectionner les informations que vous souhaitez afficher dans le social wall.<br>
				<i><b>Attention :</b> il est important de préfixer votre recherche pas @ ou # en fonction du terme recherché</i></p>
            </td>
		</tr><?php */?>
        <tr>
            <th scope="row">
				Apparence de la date
            </th>
            <td>
            	<p><label><input type="radio" name="social_wall_designDate" value="from" <?php if ( get_option('social_wall_designDate') == "from" ) echo 'checked="checked"'; ?> />Posté le...</label></p>
				<p><label><input type="radio" name="social_wall_designDate" value="since" <?php if ( get_option('social_wall_designDate') == "since" ) echo 'checked="checked"'; ?> />Posté depuis...</label></p>
            </td>
		</tr>
        <tr>
            <th scope="row">
				Apparence des Informations
            </th>
            <td>
            	<input type="checkbox" name="social_wall_photo" value="1" <?php echo checked(1, get_option('social_wall_photo'), false ); ?> /><label for="social_wall_photo">Afficher les photos de profil</label>
            </td>
		</tr>
	</tbody>
</table>



<!--<label for="upload_image">
    <input id="upload_image" type="text" size="36" name="ad_image" value="http://" /> 
    <input id="upload_image_button" class="button" type="button" value="Upload Image" />
    <br />Enter a URL or upload an image
</label>-->