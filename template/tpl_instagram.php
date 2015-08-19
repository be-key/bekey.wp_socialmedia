<h3>Paramètres généraux</h3>
<table class="form-table">
	<tbody>
		<tr>
            <th scope="row">
                <label for="instagram_name">Pseudo Instagram</label>
            </th>
            <td>
                <input type="text" name="instagram_name" class="regular-text" value="<?php echo get_option('instagram_name'); ?>"/>
            </td>
		</tr>
        
        <tr>
            <th scope="row">
                <label for="instagram_secretkey">Instagram Client ID</label>
            </th>
            <td>
                <input type="text" name="instagram_secretkey" class="regular-text" value="<?php echo get_option('instagram_secretkey'); ?>"/>
            </td>
		</tr>
        
        <?php /*?><tr>
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
		</tr><?php */?>
	</tbody>
</table>