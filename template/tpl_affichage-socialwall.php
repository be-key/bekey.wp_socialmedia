<?php 
	global $wpdb;
	$queries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}socialmedia WHERE statut != -1 ORDER BY date DESC");	
?>

<h1><?php echo get_admin_page_title(); ?></h1>
<p>Bienvenue sur la page d'accueil du plugin</p>

<div class="socialwall js-masonry">
<?php foreach($queries as $key => $query): ?>
	
    <!-- debut affichage des tweets dans le social wall -->
	<?php if($query->type == "tweet"){ ?>
        <div class="item <?php echo $query->type; ?> <?php if($query->statut == 0){ echo "hidden"; } ?>" data-id="<?php echo $query->id; ?>">
           
            <!-- contenu et photo de profil -->
			<?php if(get_option('twitter_socialWallPhoto')): ?>
            <div class="contentImage">
                <img src="<?php echo $query->author_avatar; ?>" />
            </div>
            <div class="content">
            <?php endif; ?>
                <p><?php echo $query->content; ?></p>
                <?php echo display_date($query->date, $query->author_name); ?>
            <?php if(get_option('twitter_socialWallPhoto')): ?>
            </div>
            <?php endif; ?>
            
            <?php echo action_item($query->statut); ?>
        </div>
    <!-- fin affichage des tweets dans le social wall -->
    
    <!-- debut affichage des photos instagram dans le social wall -->
    <?php }elseif($query->type == "instagram"){ ?>
    	<div class="item <?php echo $query->type; ?> <?php if($query->statut == 0){ echo "hidden"; } ?>" data-id="<?php echo $query->id; ?>">
        	<img src="<?php echo $query->content_url; ?>" />
            
            <!-- contenu et photo de profil -->
			<?php if(get_option('twitter_socialWallPhoto')): ?>
            <div class="contentImage">
                <img src="<?php echo $query->author_avatar; ?>" />
            </div>
            <div class="content">
            <?php endif; ?>
                <p><?php echo $query->content; ?></p>
                <?php echo display_date($query->date, $query->author_name); ?>
            <?php if(get_option('twitter_socialWallPhoto')): ?>
            </div>
            <?php endif; ?>
            
            <?php echo action_item($query->statut); ?>
        </div>
    <!-- fin affichage des photos instagram dans le social wall -->
    
    <!-- debut affichage des posts facebook dans le social wall -->
    <?php }elseif($query->type == "facebook"){ ?>
    
    	<!-- affichage des posts facebook qui possèdent un lien -->
    	<?php if($query->content_url){ ?>
			<div class="item <?php echo $query->type; ?> <?php if($query->statut == 0){ echo "hidden"; } ?>" data-id="<?php echo $query->id; ?>">
           
            <!-- contenu et photo de profil -->
            <?php if(get_option('twitter_socialWallPhoto')): ?>
            <div class="contentImage">
                <img src="<?php echo $query->author_avatar; ?>" />
            </div>
            <div class="content">
            <?php endif; ?>
                <p><?php echo $query->content; ?></p>
                <?php echo display_date($query->date, $query->author_name); ?>
            <?php if(get_option('twitter_socialWallPhoto')): ?>
            </div>
            <?php endif; ?>
            
        	<?php if($query->content_picture){ ?>
            	<div class="wrapper_content">
                    <div class="content_url">
                        <img src="<?php echo $query->content_picture; ?>" />
                    </div>
                    <div class="content_description">                        
						<?php echo truncate($query->content_description_url, 100); ?>
                    </div>
                </div>
            <?php } ?>
            
			<?php echo action_item($query->statut); ?>
			</div>
    <?php }else{ ?>
    
    		<!-- affichage des posts facebook qui ne possèdent pas de lien -->
    		<div class="item <?php echo $query->type; ?> <?php if($query->statut == 0){ echo "hidden"; } ?>" data-id="<?php echo $query->id; ?>">
			<?php if($query->content){ ?>
        		<p><?php echo $query->content; ?></p>
            <?php } ?>
            	<?php echo display_date($query->date); ?>
				<?php echo action_item($query->statut); ?>
        	</div>
		<?php } ?>
    
	<!-- fin affichage des posts facebook dans le social wall -->
	<?php } ?>

<?php endforeach; ?>
</div>
<?php
	
	//Permet de tronquer un texte à partir d'un certain nombre de caratère
	function truncate($chaine, $nb_car, $delim = '...') {
		$length = $nb_car;
		if($nb_car<strlen($chaine)){
			while (($chaine{$length} != " ") && ($length > 0)) {
			   $length--;
			}
			if ($length == 0) return substr($chaine, 0, $nb_car) . $delim;
			else return substr($chaine, 0, $length) . $delim;
		}else return $chaine;
	}	
	
	//affichage des actions hide/show/delete sur les différents item
	function action_item($statut){
		echo '<div class="content-action">';  
		if($statut  == 0 ){ $statut_action = "show"; }else{ $statut_action =  "hide"; }   
		echo '<a class="action '.$statut_action.'"><span class="dashicons dashicons-visibility"></span></a>';
		echo '<a class="action delete"><span class="dashicons dashicons-no"></span></a>';
		echo '</div>';
		echo '<div class="overlay"></div>';
	}
	
	//formatage de la date pour l'affichage dans le socialwall
	function display_date($date, $user = null){
		//affichage de la date avec le nom de l'utilisateur
		if(get_option('social_wall_designDate') == 'since' && $user != null):
			echo '<p class="description">Posté par '.$user.' il y a '.time_ago($date).'</p>';
        elseif(get_option('social_wall_designDate') == 'from' && $user != null):
        	echo '<p class="description">Posté par '.$user.' le '.date('d-m-y', strtotime($date)).'</p>';
		//affichage de la date sans le nom de l'utilisateur
		elseif(get_option('social_wall_designDate') == 'since' && $user == null):
			echo '<p class="description">Posté il y a '.time_ago($date).'</p>';
        elseif(get_option('social_wall_designDate') == 'from' && $user = null):
        	echo '<p class="description">Posté le '.date('d-m-y', strtotime($date)).'</p>';
        endif;
	}
	
	//Calcule depuis combien de temps la publication à été postée
	function time_ago($time){
		$difftime = time() - strtotime($time);
		$tokens = array (
			31536000 => 'année',
			2592000 => 'mois',
			604800 => 'semaine',
			86400 => 'jour',
			3600 => 'heure',
			60 => 'minute',
			1 => 'seconde'
		);
		foreach ($tokens as $unit => $text) {
			if ($difftime < $unit) continue;
			$numberOfUnits = floor($difftime / $unit);
			return $numberOfUnits.' '.$text.( ($numberOfUnits>1 && $text != "mois")?'s':'' );
		}
	}
?>
        
<script>
$ = jQuery.noConflict();
$(document).ready(function(){
		
	
	
	//Enregistre les pattern ajoutés - enregistrement bdd
	$(document).on('click', '.delete, .hide, .show', function(){ 
	
		var $this = $(this);	
		var item = $this.parent().parent();
		var data_id = item.attr("data-id");
		var data_action = $this.attr('class').split(" ");
			data_action = data_action[1];
			
		data_item = {};
		data_item['id'] = data_id;
		data_item['action'] = data_action;
		
		item.append('<div class="dot-spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
		
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				'action': 'ajax_hide_item',
				'data': data_item
			},
			success: function(data){
				if(data_action == "delete"){
					item.fadeOut('fast',function(){
						item.children(".dot-spinner").remove();
						item.remove();
							$('.socialwall').masonry({
								itemSelector: '.item'
							});
						
					})
				}else if(data_action == "hide"){
					item.addClass("hidden");
					item.children(".dot-spinner").remove();
					$this.removeClass("hide").addClass("show")
				}else if(data_action == "show"){
					item.removeClass("hidden");
					item.children(".dot-spinner").remove();
					$this.removeClass("show").addClass("hide")
				}
					
			}
		});
		
	})
	
	
})
</script>
