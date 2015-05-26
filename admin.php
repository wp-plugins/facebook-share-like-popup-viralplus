<?php

require(dirname(__FILE__) .'/includes/class-error.php');

require(dirname(__FILE__) .'/includes/class-admin-view.php');



require(dirname(__FILE__) .'/views/class-admin-top.php');







/**

 * This admin class intilizes all the admin pages and

 * their required functionality

 *

 */

class viralAdmin

{



	/* Hold the an instance of the page to be shown (or null if none) */

	protected $admin_view 	= null;





	function __construct(){

		add_action( 'admin_menu' 		, array($this,'add_menus') );

		add_action( 'current_screen'	, array($this,'process_request'));

		

		$plugin = plugin_basename(dirname(__FILE__) . '/index.php');

		add_filter("plugin_action_links_{$plugin}", array($this,'add_settings_link' ));



	}



	/**

	 * Process the request of an admin page

	 * 

	*/

	public function process_request(){



		/* make sure that we only execute our code if one of our registered page is loaded */

		if ( $this->determine_page() && !empty($_GET['page'])){	



			/* Remove all (in this case: unwanted) quotes */

			$_POST 	  	= stripslashes_deep($_POST);

			$_REQUEST 	= stripslashes_deep($_REQUEST);

			$_GET  		= stripslashes_deep($_GET);

			

			add_action( 'admin_enqueue_scripts', array($this,'load_assets'));



			$this->admin_view->process_request();



			/* If current request is an post request, the user intends to

			   update or save any admin form */

			if (viralplusError::is_post())

				$this->admin_view->save();

			

		}



	}

	



	public function load_assets(){

		wp_enqueue_style( 'arv-admin-css'		, plugins_url('includes/admin-style/admin.css',__FILE__) );

   		wp_enqueue_script( 'arevico-tab-js'		, plugins_url('includes/admin-style/tab-simple.js',__FILE__) , array('jquery') );

	}



/**

 * Determines which page we need to load

 * @param $_GET['page'] in array('arvlb-tld')

 * 

 */

	private function determine_page(){

		if (!isset($_GET['page']))

			return;



		switch ($_GET['page']) {



			case 'viralplus':

				$this->admin_view = new viralAdminTop();

			break;



		}



		return !is_null($this->admin_view);

	}	



	/**

	 * Render the requested page

	 */

	public function render_page(){

		/* AdminView is guaranteed to be set at this point */

		$this->admin_view->render_page();

	}



	/**

	 * Add all the menus associated with the main plugin

	 */

	public function add_menus(){

		$o = get_option('arv_fb24_opt');



		if (isset($o['submenu']) && $o['submenu']==1){

	      	add_submenu_page('options-general.php','Viral Plus', 'Viral Plus', 'manage_options', 'viralplus', array($this,'render_page'));

		} else{

	      	add_menu_page('Viral Plus', 'Viral Plus', 'manage_options', 'viralplus', array($this,'render_page'));

	      }

	}

	/**

	* Add an link to the options page in the plugin overview

	* @param string $links  an array of links associated with the current plugin

	*/

	public function add_settings_link($links) { 

  		$settings_link = '<a href="options-general.php?page=viralplus">Settings</a>'; 

  		array_unshift($links, $settings_link); 

 	 	return $links; 

	}

 

}	



?>