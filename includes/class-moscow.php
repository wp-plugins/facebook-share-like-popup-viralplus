<?php 



if (!class_exists('viralplusError')){

class viralplusError

{

	/**

	 * Retrieve the first element of an array

	 * @param array &$arr The array of which the first element is to be retrieved

	 * @return mixed the first element of an array

	 */

	public static function first_key(&$arr){

			reset($arr);

			return key($arr);

	}

	/**

	 * Check if a value is available in the post array And 	

	 * return or output it encoded

	 * @param string $name the name and subname

	 * @param array $arr the array to fetch

	 * @param boolean $echo wether or not to output the current variable

	 * @param boolean $escape wether or not escaping the output is wished

	 * @example val::('option[subarray][value]')

	 * @return string the value						

	 */

	public static function val($name,$arr,$echo=false,$escape=true){

		if (is_null(self::str_arr_val($name,$arr))){

			return "";

		} else{

			$final= ($escape) ?

			htmlentities(self::str_arr_val($name,$arr)) : self::str_arr_val($name,$arr) ;



			if($echo){ 

				echo($final);

			}else{

			 return $final;

			}

		}

	}

	



	/**

	 * Fill an array with an default value if the specified keys don't exists. Especially usefull when

	 * receives a number of checkboxes from a form submit

	 * @param $arr1 array with all the submitted settings

	 * @param $arr2 array with all the default options

	 * @requires $arr1 $arr1 does not contain items not in $arr2 (will not be included) &&

	 *			 $arr1.length<=$arr2.length

	 */

	public static function arr_def_merge($arr1,$arr2,$def_val='0'){

		$a_ret = array();

		foreach ($arr2 as $key => $value) {

			

			if (isset($arr1[$key])){

				$a_ret[$key] = $arr1[$key];

			} else {

				$a_ret[$key]=$def_val;

			}



		}

		return $a_ret;

	}



	/**

	* Remove all elements execpt those specified in $art

	* @param $art the values of this array are the keys to keep in $arp

	* @param $arp the initial array to apply the filter function too

	*/

	public static function get_ins_inc($arp,$art /*array to kep*/,$multiple=false){

		if (!is_array($arp))

			return array(); //return empty array since shit is not valid to begin with



		if ($multiple){ //apply $arp to all elements and then return it

			foreach ($arp as $k => &$v) {

				$v = self::get_ins_inc($v,$art,false);

			}

			return $arp;



		} else {

			return array_intersect_key($arp,array_flip($art));

		}

	}



	/**

	* Remove all elements specified in $art

	* @param $art the values of this array to remove

	* @param $arp the initial array to apply the filter function too

	*/

	public static function get_ins_ex($arp,$arr_to_rem,$multiple=false){



		if ($multiple){ //apply $arp to all elements and then return it

			foreach ($arp as $k => &$v) {

				$v = self::get_ins_ex($v,$arr_to_rem,false);

			}

			return $arp;



		} else {

			return array_diff_key($arp, array_flip($arr_to_rem));

		}

	}

	/**

	 * Return a value of a subarray by supplying

	 * @param name the subarray e.g(username->0->)

	 * @param arr the array to 

	 */

	public static function str_arr_val($name,$arr=null){

		if ($arr===null)

			$arr=$_POST;





		preg_match_all('/\[(.*?)\]/', $name, $matches,  PREG_SET_ORDER );



		if (!empty($matches)){

			preg_match('/(.*?)\[/', $name,$base);



			if (!isset($arr[$base[1]]))

				return "";



			$a = $arr[$base[1]];

			foreach ($matches as $key => $value) {

				if (!isset($a[$value[1]]))

					return ""; //requested value is not set



				$a=$a[$value[1]];

			}

			return $a;

		} else {

			return isset($arr[$name]) ? $arr[$name] : "";

		}

	}



	



	/**

	 * This function converts an associative array to a single string with all elements

	 * delimited

	 * @param assoc the associated array

	 * @param delim the delimiter to seperate the values

	 */

	public static function assoc_to_str($assoc,$delim,$escape=true){

		global $wpdb;

		$arr_ret = array();

		foreach ($assoc as $key => $value) {



			if ($escape)

				$value=$wpdb->escape($value);

			$arr_ret[]="{$key}=\"{$value}\"";

		}

		return implode($delim,$arr_ret);

	}

	/**

	 * DELETE from the table if it not exists, but if it exists it might need to be update

	 * @param table the table to be updated

	 * @param current, the array containing all rows

	 * @param new, the new array of al rows

	 * @param check_if_changed 

	 * 

	 */





	/**

	 * Get all elements that are present in array1 and not present in array2.

	 * the check is to be done on all keys defined in keys

	 * @param $arr1 the old array with elements

	 * @param $arr2 the new array with elements	

	 * @return $return.length=$arr1.length-$arr2.length

	 */

	public static function get_removed($arr1,$arr2,$key){

		$a_ret =array();

		if ($arr2==null)

			$arr2=array();

		foreach ($arr1 as $old) {

			$key_check=self::get_ins_inc($old,$key,false);

			$rem=true; //remove if not found



			foreach ($arr2 as $new) {//search for it and if found set remove to false

				$m=array_intersect_assoc($new, $key_check);

				if (!empty($m)){

					$rem=false;break;//breakie breakie

				}

			}

			if ($rem)

				array_push($a_ret, $old);

		}

		return $a_ret;

	}



	/**

	  * Get inserted item.

	  */

	public static function get_inserted($arr1,$arr2,$key){

		//very simple, if it's inserted it is also removed if we switch arguments

		return self::get_removed($arr2,$arr1,$key);

	}





	public static function is_post(){

		return 	$_SERVER['REQUEST_METHOD']==="POST";

	}





	public static function array_rename_key($array,$keys,$subarray=false){

		if ($subarray){

			foreach ($array as $k => &$v) {

				$v=self::array_rename_key($v,$keys,false);

			}

		return $array;



		} else {

		

		foreach ($keys as $old => $new) {

			if (isset($array[$old])){

				$array[$new]=$array[$old];

				unset($array[$old]);

			}

		}



		return $array;

		}

	}



	/**

	 * @param $arr the array container a sql result set, make sure that it is sorted

	 *			on FIXED keys , so f.e (a1;b1;b3),(a1;b2;b4),(a2;b1;b3)

	 */

	public function arr_common_to_sub($arr,$fixed,$set_name="data"){

		if (!isset($arr[0]))//no elements

			return array();



		$a_ret=array();





		$current_arr = array(); 



		foreach ($arr as $k => $v) {

			$key_check=array_intersect_assoc(self::get_ins_inc($v,$fixed), 

				$current_arr);



			if (empty($key_check)){ //start of a new combination of fixed with the rest of the array

				

				if (!empty($current_arr))//if it's not the first item ever, push!

					array_push($a_ret, $current_arr); 



				$current_arr=self::get_ins_inc($v,$fixed);

				$current_arr[$set_name]=array();

			}



			array_push($current_arr[$set_name],self::get_ins_ex($v,$fixed) );

		}

		if (!empty($current_arr))//push the last item if not empty (TODO: check if 'check' is necessary)

					array_push($a_ret, $current_arr); 



		return $a_ret;

	}



	/**

	 * Assemble a repetive pattern with changing variables

	 * @param 	to_rep the pattern which should be replicated

	 * 			the variable elements are marked with {$q[123][abc][etc]},

	 * 			make sure to escape it properly so that PHP doesn't try to parse it itself

	 * 			the variable {$i} can be used to output the current number we are at

	 * @param 	arr an array containing subarrays with the different values.

	 * 

	 */

	public static function arr_val_map($to_rep,$arrs,$echo=true,$escape=true){

		$lret 	= "";

		$i 		= 0;

		if (is_null($arrs))

			return;



		foreach ($arrs as $current) {

			$m = "";

			$i++;

			$temp_ret = $to_rep;

			$current['i']=$i;

			if (preg_match_all('/\{\$(.*?)\}/', $to_rep, $m,PREG_SET_ORDER)!==false){



			foreach ($m as $key => $v) {





			$ins_val=self::str_arr_val($v[1],$current);



			if ($escape)

				$ins_val=htmlentities($ins_val);



					$temp_ret = str_replace($v[0],$ins_val, $temp_ret);

				}

			}

			$lret .= $temp_ret;

		} //end one repetition

		//return or output something

		if ($echo)

			echo($lret);

		return $lret;

	}//end arr_val_map



	/**

	 * Returns the full current url being displayed

	 * @pure

	 */

	public static function getCurrentURL(){

		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') 

                === FALSE ? 'http' : 'https';

		$host     = $_SERVER['HTTP_HOST'];

		$script   = $_SERVER['SCRIPT_NAME'];

		$params   = $_SERVER['QUERY_STRING'];

		 

		$currentUrl = $protocol . '://' . $host . $script . '?' . $params;

		 

		return $currentUrl;

	}

}

}

//end class



?>