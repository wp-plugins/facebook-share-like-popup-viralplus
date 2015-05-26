<?php 

/**

 * @author H.F. Kluitenberg

 * class to easily faciliate error messaging and alerting

 */

class viralErrorClass{

	protected $messages;



	function __construct(){

		$this->messages=array();

	}



	public function add($key,$value){

		$this->messages[$key]=$value;

	}





	public function ifempty($key,$message,$arr=null,$bind_key=null){

		if (is_null($arr))

			$arr=$_POST;



		if (is_null($bind_key))

			$bind_key = $key;



//		if (!isset($_POST[$key]) || $_POST[$key] === ""  || (is_array($_POST[$key]) && count($_POST[$key])==0)  ){

		$val_ex	 = viralplusError::val($key,$arr,false,false);		



		if (empty($val_ex)){

			$this->add($bind_key,$message);

			return true;

		}

		return false;

	}









	public function has_error(){

		return !empty($this->messages);

	}



	public function gen_message($sucess=null,$failure=null){



		if (viralplusError::is_post() || isset($_GET['settings-updated'] )){



			if (is_null($sucess))

				$sucess="Successfully saved!";

			if (is_null($failure))

				$failure="Not saved, correct the errors!";



			$class 	= ($this->has_error() ) ? "updateerror" : "updatesuccess";

			$msg 	= ($this->has_error() ) ?   $failure : $sucess;

			echo("<div class=\"{$class}\"><strong>{$msg}</strong></div>");

		}

	}



	public function gen_js_feedback($include_script=false){

		if (!$this->has_error())

			return;

		$i=0;

		$lret = "";

		foreach ($this->messages as $key => $value) {

			$i++;

			$value=htmlentities($value);

			$lret .= "\$(\"*[name='$key']\").before(\"<span id='ar-error-$i' class='error-label'>$value</span>\");\r\n";

			$lret .= "\$(\"*[name='$key']\").addClass('error-elem').blur(function(){\$(this).removeClass(\"error-elem\");$('#ar-error-$i').remove();});\r\n";

		}

		if ($include_script){

			echo "<script>(function (\$) {\$(document).ready(function() {{$lret}});})(jQuery);</script>";

		} else {

			echo "(function (\$) {\$(document).ready(function() {{$lret}});})(jQuery);";	

		}

	}





	}



 ?>