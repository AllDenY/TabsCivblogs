<?php 
/**
 * Plugin Name: Tabs CivBlogs
 * Plugin URI: http://localhost:81/Plugins
 * Description: Tabs CivBlogs est un widget Wordpress permettant d'afficher les articles récents de civblogs, le top 5 des articles ainsi que les meilleurs blogueurs.
 * Version: 1.0
 * Author: AllDenY
 * Author URI: http://alldeny.net
 */

 
//Execute la fonction socialcard_init pour activer le widget
add_action('widgets_init','tabscivblogs_init');

function tabscivblogs_init(){

	//Enregistrer le wigdet dans le panneau de Wordpress
	register_widget("tabscivblogs_widget");
}

function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}
	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

//Charge les données de API REST via URL
function ApiRest($url){
	$table=objectToArray(json_decode(file_get_contents($url,NULL,NULL,0,200000)));
	return($table);
}

//
class tabscivblogs_widget extends WP_widget{
	private $pluginDir = "";

	//Definition des propriétés du widget socialcard...
	function tabscivblogs_widget(){
		
		$options = array(
			"classname" => "tabscivsocial",
			"description" => "Tabs CivBlogs est un widget Wordpress permettant d'afficher les articles récents de civblogs, le top 5 des articles ainsi que les meilleurs blogueurs",
		);

		if(empty($this->pluginDir)){
			$this->pluginDir = WP_PLUGIN_URL . '/TabsCivblogs';
		}
			
		$this->WP_widget("tabscivblogs","Tabs CivBlogs",$options);

	}

	function widget($args,$d){

		extract($args); 
		try{
			//Liste des articles populaires
			$top = ApiRest("http://civblogs.akendewa.org/posts.json?s=popular"); 
			
			//Liste des articles recents
			$recent = ApiRest("http://civblogs.akendewa.org/posts.json?s=recent"); 
			
			//Liste des tags
			$tags = ApiRest("http://civblogs.akendewa.org/tags.json"); 
			wp_enqueue_style('tabscivblogs-style',$this->pluginDir.'/stylesheet/default.css');
			wp_enqueue_script('tabscivblogs-style',$this->pluginDir.'/scripts/Idtabs.js');
			?>
			
			<!-- Titre pour les onglets -->
				<ul id="civblogs">
					<li class="st1" onMouseOver="hideciv(2);hideciv(3);showciv(1);">RECENTS</li>
					<li class="st2" onMouseOver="hideciv(1);hideciv(3);showciv(2);">TOP 5</li>
					<li class="st3" onMouseOver="hideciv(1);hideciv(2);showciv(3);">TAGS</li>
				</ul>
			<div>
				<div class="civblogscontent" style="width:auto;background-color:#fff;margin-bottom:10px;">

					<!-- Contenu premier onglet -->
					<div class="civblogstab" id="civ1">
						<?php
							for($i=0;$i<=4;$i++){
						?>
							<div class="civblogs-art" style="margin:5px;">
								<img src="<?php if($recent['posts'][$i]['Post']['image_url']){echo $recent['posts'][$i]['Post']['image_url'];}else{ echo $this->pluginDir.'/stylesheet/logo.jpg';} ?>" style="box-shadow: 0px 1px 10px #aaa;border: 5px solid #fff; margin:5px;" width="50" height="50" align="left">
								<p>
									<b><a href="<?php echo $recent['posts'][$i]['Post']['url']; ?>"><?php echo $recent['posts'][$i]['Post']['title']; ?></a></b><br>
										
										<?php echo substr($recent['posts'][$i]['Post']['description'],0,100)."..."; ?>
								</p>
							</div>
						<?php } ?>
					</div>

					<!-- Contenu deuxième onglet -->
					<div class="civblogstab" id="civ2" style="display:none;visibility:hidden;">
						<?php
							for($i=0;$i<=4;$i++){
						?>
							<div class="civblogs-art" style="margin:5px;">
								<img src="<?php if($recent['posts'][$i]['Post']['image_url']){echo $recent['posts'][$i]['Post']['image_url'];}else{ echo $this->pluginDir.'/stylesheet/logo.jpg';} ?>" style="box-shadow: 0px 1px 10px #aaa;border: 5px solid #fff; margin:5px;" width="50" height="50" align="left">
								<p>
									<b>
										<a href="<?php echo $top['posts'][$i]['Post']['url']; ?>"><?php echo $top['posts'][$i]['Post']['title']; ?></a>
									</b>
									<br>
										<?php echo substr($top['posts'][$i]['Post']['description'],0,100)."..."; ?>
								</p>
							</div>
						<?php } ?>
					</div>


					<!-- Contenu troisième onglet -->
					<div class="civblogstab" id="civ3" style="display:none;visibility:hidden;">
						<?php
						for($i=0;$i<=(count($tags['trends'])/3);$i++){
							$taille = $tags['trends'][$i]['Tag']['weight']+3;
							echo " <a target='_blank' href='http://civblogs.akendewa.org/posts/index/t:".$tags['trends'][$i]['Tag']['keyname']."'> <i style='font-size:".$taille."px; text-decoration:underline;'>".$tags['trends'][$i]['Tag']['name']."</i></a>";
						} 
						?>
					</div>
					<div>
						<i style='font-size:10px;'>Propulsé par AllDenY</i>
					</div>
				</div>
				
			</div>		

		<?php
		}catch(Exception $e){
			Echo "Merci de bien vouloir mettre à jour votre plugin";
		}
	}

	function update($new,$old){
		return $new;
	}

	function form($d){
		?>
			<i style='font-size:10px;'>Propulsé par AllDenY</i>
		<?php
	}

}

?>