<?php 
/*
   Plugin Name: FacebooK Popup Share Like ViralShare
   Plugin URI: http://wptit.com/portfolio/viralshare-facebook-popup-like-share-wordpress
   Description: Viral Plus Plugin ask your users to share your website article or blog on facebook without annoying them in friendly popup style, Just like other viral website you can also make your article or website or video go viral .
   Version: 1.0
   Author: Wptit
   Author URI: http://wptit.com/portfolio/viralplus-viral-wordpress-plugin-to-make-your-content-go-viral
   Copyright: 2015, 
*/
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require(dirname(__FILE__) .'/includes/class-moscow.php');
require(dirname(__FILE__) .'/class-activate.php');


if (is_admin() ){

	require(dirname(__FILE__) .'/admin.php');
	$viralAdmin 		= new viralAdmin();

	add_action( 'add_meta_boxes', 'viral_add_meta_box' );
	add_action( 'save_post', 'viral_save_meta_box_data' );
	add_action( 'wp_ajax_viralplus_video_list', 'viralplus_video_list_callback' );
	add_action( 'wp_ajax_nopriv_viralplus_video_list', 'viralplus_video_list' );

} else {
  
  $arvlbGASuite = new viralFPPL();  
  	
}

/**
 * Main plugin class. 
 */

class viralFPPL {

    private $options = null;

    /**
     * Constructor, initializes options
     */

    function __construct(){
		$this->options = get_option('arv_fb24_opt',viralSHARED::getDefaults() );
		add_action('wp_enqueue_scripts', array($this,'addScript'));
		add_action( 'wp_head', 'viralplus_feed_script' );
		add_action('wp_footer', 'fb_viral_share');
		add_action('wp_head', 'viral_js');
		add_filter('the_content','replace_content');	

    }


    /**
     * Add all scripts required on the front-end
     */

    public function addScript(){

    	$o  = $this->options;
		$post_id = get_the_ID();
		$hide = get_post_meta( $post_id, '_viral_meta_value' );

		if (((is_front_page()  && !empty($o['display_on_homepage']) )	
		  || (is_archive()  && !empty($o['display_on_archive']))
		  || (is_single()   && !empty($o['display_on_post']))
		  || (is_page()     && !empty($o['display_on_page']))
			) && !$hide[0]){

	
	
        wp_register_script('viralPlus_jquery_library', 'http://code.jquery.com/jquery-1.11.3.min.js',array('jquery'));
        wp_enqueue_script( 'viralPlus_jquery_library');
	wp_register_script('viralPlus_fblib', plugins_url( 'includes/front/js/fb-lib.js',__FILE__),array('jquery'));
	wp_enqueue_script ('viralPlus_fblib');
	wp_register_script('viralPlus_sabox', plugins_url( 'includes/front/scs/sweetalert.min.js',__FILE__),array('jquery'));
	wp_enqueue_script ('viralPlus_sabox');
	wp_register_style('viralPlus_sacss', plugins_url( 'includes/front/scs/sweetalert.css',__FILE__));
	wp_enqueue_style( 'viralPlus_sacss');
	
    
	}
  }
 } // end of main plugin class

	 /**
	 * Function called on activation of the plugin
	 */
	function viral_plus_activate() {
	  viralActivate::on_activate();
	}

    /**
     * Function called on de-activation of the plugin
     */    
    function viral_plus_deactivate() {
      viralActivate::on_deactivate();
    }

  register_activation_hook( __FILE__, 'viral_plus_activate' );
  register_uninstall_hook(__FILE__, 'viral_plus_deactivate' );
    
/**
 * This class contains shared common properties and/or methods
 */

class viralSHARED{

  //Defaults for the option table of this plugin

  public static $defaults = array (
							  'delay'         => '2000',
							  'display_on_page'   => '1',
							  'display_on_post'   => '1',
							  'display_video_end'   => '1',
							  'video_height'   => '350',
							  'video_width'   => '500',
							  'share_text'   => 'Share on Facebook',	
						  );

  /**
   * Normalize settings to prevent undefined errors on the front-end
   */

  public static function normalize($o){

    $checks = array(
      'width'		=> '400',
      'height'		=> '255',
      'delay'		=> '0',
      'coc'         => '0',
	  'display_on_page'   => '1',
	  'display_on_post'   => '1',
	  'display_video_end'   => '1',
	  'video_height'   => '350',
	  'video_width'   => '500',
	  'share_text'   => 'Share on Facebook',	
    );

    return array_merge($checks,$o);

  }

  public static function getDefaults(){
    $o = self::$defaults;
    if (empty($o['install_date']))
      $o['install_date'] = time();
	  
    return $o;
  }
}

function viral_js() {

	$content = '';
	$title = '';
	$thumb = plugins_url( 'default.jpg',__FILE__);
	$post_id = get_the_ID();
	$hide = get_post_meta( $post_id, '_viral_meta_value', true );
	$options = get_option('arv_fb24_opt',viralSHARED::getDefaults() );


	if (((is_front_page()  && !empty($options['display_on_homepage']) )
      || (is_archive()  && !empty($options['display_on_archive']))
      || (is_single()   && !empty($options['display_on_post']))
      || (is_page()     && !empty($options['display_on_page']))
       ) && !$hide){
		

		if(is_single()){
			$post = get_post($post_id);
		}else{
			$post = get_page( $post_id );
		}
		
		$content = substr(strip_tags($post->post_content,'') , 0, 200);
		$title = $post->post_title;
		if(has_post_thumbnail( $post_id )){
			
			$thumb_img = get_post_thumbnail_id( $post_id );
			$src = wp_get_attachment_image_src($thumb_img, 'thumbnail_size');	
		
		}

		if(is_array($src) && !empty($src)){
			$thumb = $src[0];
		}

		$blog = get_bloginfo(); 
		$clink = get_permalink();


		$meta = '';
		$meta = '<meta content="'.$title.'" property="og:title">'.PHP_EOL;
		$meta .= '<meta content="article " property="og:type">'.PHP_EOL;
		$meta .= '<meta content="'.$clink.'" property="og:url">'.PHP_EOL;
		$meta .= '<meta content="'.$thumb.'" property="og:image">'.PHP_EOL;
		$meta .= '<meta content="'.$blog.'" property="og:site_name">'.PHP_EOL;
		$meta .= '<meta content="'.$content.'" property="og:description">'.PHP_EOL;

		echo $meta;
		echo $sty;		

	}
}



function viral_current_page_url() {

	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}

	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}



function fb_viral_share() {



	$post_id = get_the_ID();
	$hide = get_post_meta( $post_id, '_viral_meta_value', true );
	$options = get_option('arv_fb24_opt',viralSHARED::getDefaults() );

	if (((is_front_page()  && !empty($options['display_on_homepage']) )
      || (is_archive()  && !empty($options['display_on_archive']))
      || (is_single()   && !empty($options['display_on_post']))
      || (is_page()     && !empty($options['display_on_page']))
        ) && !$hide){
		
		if(is_single()){
			$post = get_post($post_id);
		}else{
			$post = get_page( $post_id );
		}

		$content = substr(strip_tags($post->post_content,'') , 0, 200);
		$title = $post->post_title;
		$thumb = plugins_url( 'default.jpg',__FILE__);
		
		if(has_post_thumbnail( $post_id )){
			$thumb_img = get_post_thumbnail_id( $post_id );
			$src = wp_get_attachment_image_src($thumb_img, 'thumbnail_size');	
		}

		if(is_array($src) && !empty($src)){
			$thumb = $src[0];
		}
		
		
		$clink = get_permalink();		
		$content = '<div id="inline1" style="overflow: auto;display:none;">
					  <a href="#fbshare" class="fbshare">&nbsp;</a>
					  <div id="fbshare">
						<div class="inner-fbshare">';
							if($options['display_toptitle'] != ""){
		$content .= '			<h3>'.$options['display_toptitle'].'</h3>';
							}

		$content .= '		<img src="'.$thumb.'" />';
							if($options['display_title']){
		$content .= '					<p  style="text-align: center;margin:0;">'.$title.'</p>';
							}

		if($options['share_text'] == '') $options['share_text'] = "Share on Facebook";					
			
			$content .= '	  </div>
							<div class="outer-fbshare">
								<a href="javascript:;" style="" class="fbshare_bt" onclick="fb_share(\''.$clink.'\')">'.$options['share_text'].'</a>
							</div>
						  </div>
						</div>';
			echo $content;
		
	
	
		}
	}
	
	function replace_content($content){	
		
		$viral_shared_video = array();
		if(isset($_SESSION['viral_shared_video'])){$viral_shared_video = $_SESSION['viral_shared_video']; }
		$post_id = get_the_ID();
		$hide = get_post_meta( $post_id, '_viral_meta_value', true );
		$options = get_option('arv_fb24_opt',viralSHARED::getDefaults() );
		if($options['display_video_end'] && !$hide){
			libxml_use_internal_errors(true);
			$doc = new DOMDocument();
			$doc->loadHTML($content);
			$iframes = $doc->getElementsByTagName('iframe');
			foreach($iframes as $iframe){
				$needle='https://www.youtube.com/embed/';
				$haystack = $iframe->getAttribute('src');

				if(strpos($haystack,$needle)!==false){
					$src = explode('/',$haystack);
					$src = $src[4];
				}
				$src = str_replace('https://www.youtube.com/embed/', '', $src);
				$content = preg_replace('/<iframe.*?\/iframe>/i','', $content);
				$content .= '<div><input type="hidden" id="shared_video" value="'.implode(',',$viral_shared_video).'"/><div id="player" data-videoid="'.$src.'" data-pageurl="" data-featuredimg=""></div></div>';
			}
			return $content;
		}
		return $content;
	}
	function viral_add_meta_box() {
		$screens = array( 'post', 'page' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'viral_sectionid',
				__( 'Viral Popup', 'viral_popup' ),
				'viral_meta_box_callback',
				$screen
			);
		}
	}
	function viral_meta_box_callback( $post ) {
		wp_nonce_field( 'viral_meta_box', 'viral_meta_box_nonce' );
		$value = get_post_meta( $post->ID, '_viral_meta_value', true );
		echo '<label for="viral_hide_popup">';
		_e( 'Hide Popup', 'viral_popup' );
		echo '</label> ';
		echo '<select id="viral_hide_popup" name="viral_hide_popup">';
		if($value == '1'){
			echo '<option value="1" selected="selected">Yes</option>';
			echo '<option value="0">No</option>';
		}else{
			echo '<option value="1">Yes</option>';
			echo '<option value="0" selected="selected">No</option>';
		}
		echo '</select>';
	}
	function viral_save_meta_box_data( $post_id ) {
		if ( ! isset( $_POST['viral_meta_box_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['viral_meta_box_nonce'], 'viral_meta_box' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		if ( ! isset( $_POST['viral_hide_popup'] ) ) {
			return;
		}
		update_post_meta( $post_id, '_viral_meta_value', $_POST['viral_hide_popup']  );

	}


	function viral_video_ajax() {
		$video_id = $_REQUEST['video_id'];
		//global $wp_session;
		//array_push($wp_session['viral_shared_video'], array($video_id));	
		echo $video_id;
	}
	add_action( 'wp_ajax_my_ajax', 'viral_video_ajax' );

	
	
	function viralplus_video_list() {
		$video_id = $_REQUEST['video_id'];
		echo $video_id;
		wp_die(); 
	}
	
function viralplus_feed_script(){
	
	$thumb = plugins_url( 'default.jpg',__FILE__);
	$post_id = get_the_ID();
	$hide = get_post_meta( $post_id, '_viral_meta_value', true );
	$options = get_option('arv_fb24_opt',viralSHARED::getDefaults() );
	if (((is_front_page()  && !empty($options['display_on_homepage']) )
      || (is_archive()  && !empty($options['display_on_archive']))
      || (is_single()   && !empty($options['display_on_post']))
      || (is_page()     && !empty($options['display_on_page']))
       ) && !$hide){
		

			if(is_single()){
				$post = get_post($post_id);
			}else{
				$post = get_page( $post_id );
			}
			$content = substr(strip_tags($post->post_content,'') , 0, 200);
			$title = $post->post_title;
			
			if(has_post_thumbnail( $post_id )){
			
				$thumb_img = get_post_thumbnail_id( $post_id );
				$src = wp_get_attachment_image_src($thumb_img, 'thumbnail_size');	
		
			}

			if(is_array($src) && !empty($src)){
				$thumb = $src[0];
			}
	
			$blog = get_bloginfo(); 
			$clink = get_permalink();
	
		$sct = '<script type="text/javascript">'.PHP_EOL ;
		if($options['app_id'] != ''){			
			$sct .= '	FB.init({
						appId      : "'.$options['app_id'].'",
						status     : true,
						xfbml      : true,
					  });';
		}
		
		if($options['display_video_end']){
	
		$sct .= '	// create youtube player
					var tag = document.createElement("script");
					tag.src = "https://www.youtube.com/player_api";
					var firstScriptTag = document.getElementsByTagName("script")[0];
					firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
					var player;
	
					function onYouTubePlayerAPIReady() {
						var myElem = document.getElementById("player");
						videoId = "";
						if (myElem != null) {
							videoId = document.getElementById("player").getAttribute("data-videoid");
						}
						player = new YT.Player("player", {
						  wmode: "transparent",
						  videoId: videoId,
						  events: {
							  "onStateChange": onPlayerStateChange
						 }
						});
					}
	
					function stopVideo(event) {
						event.target.stopVideo();
					  }
	
					// when video ends
					function onPlayerStateChange(event) {
	
						if(event.data == 1) {
							player.playVideo();
						}
						if(event.data == 0) { jQuery(".fbshare").click(); }
					}';
			}
			
			if($options['share_text'] == '') $options['share_text'] = "Share on Facebook";	
		
		$sct .= '	jQuery(function($){ ';
		
		
		
		
		
		$sct .= '		$(document).on("click", ".fbshare", function(){
						
						swal({
							title:  "'.$title.'",
							showCancelButton: true,
							imageUrl : "'.$thumb.'",
							confirmButtonColor: "rgb(64, 94, 159)",
							imageSize : "200x200",
							confirmButtonText: "'.$options['share_text'].'",
							closeOnConfirm: true
						},
						function(isConfirm){   
							if (isConfirm) {     
								fb_share(\''.$clink.'\');   
							} 
						});';
						
		$sct .= 'function fb_share(url){
					';
					if($options['app_id'] == ''){
		$sct .= ' 		window.open("https://www.facebook.com/sharer/sharer.php?u="+url,"_blank","width=400, height=300");';
					} else {
		$sct .= '		
						var playerSize = $("#player").size();
						if(playerSize > 0){			
							
							FB.ui({
									 method: "share",
									 href: "'.viral_current_page_url().'",
								  });
						  }';
						}
		$sct .= '  } ';
			
		$sct .= '		});	';
		
		if($options['delay'] > 0){
			$sct .= '	setTimeout(function(){
							$(".fbshare").click();
						},'.$options['delay'].');';
		}else{
			$sct .= '	$(".fbshare").click();';
		}				
		$sct .= '});'.PHP_EOL.'
		</script>'.PHP_EOL;
	
		echo $sct;
	}
}
	

	



 ?>