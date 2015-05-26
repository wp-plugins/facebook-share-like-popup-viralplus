<?php 
class viralActivate
{
	
	/**
	 *  This function is called when the plugin gets activated. Sets default
	 *  Options to the database
	 */
	public static function on_activate(){
	    $o 		= get_option('arv_fb24_opt',array());

	    $def 	= viralSHARED::getDefaults();
	    if (empty($o)){
	    	update_option('arv_fb24_opt', $def);  
	    }


		return;
	}

	public static function on_deactivate(){

		return;
	}
} 


?>