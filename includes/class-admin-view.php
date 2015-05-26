<?php 
/**
 * An instantiation of an AdminView class gives a user the oportunity to configure
 * plugin settings and behaviour.
 * @author Wptit
 */
abstract class viralAdminView 
{

	abstract public function process_request();

	private $textbox 	= '<input type="text" name="%1$s" value="%2$s" %3$s/>';
	private $textarea 	= '<textarea name="%1$s" %3$s/>%2$s</textarea>';
	private $checkbox 	= '<input type="checkbox" name="%1$s" value="1" %2$s %3$s/>';
	private $hidden 	= '<input type="hidden" name="%1$s" value="%2$s" %3$s/>';
	
	private $select 	= '<select name="%1$s" %3$s>%2$s</select>';
	private $option 	= '<option value="%1$s"%3$s>%2$s</option>';

	/** The current state of an admin view. States being, 0:default, 1: saved, 2: updated */
	protected $state		= 0;

	/**
	 * Get the current state of the admin view
	 * @return $this->state
	 */
	public function getState(){
		return $this->state;
	}

	/**
	 * Get hidden element from the database
	 * @param string $name the name of the option element. Always a subarray of o (e.g: o[option-1]) 
	 * @param array $attr Atribute value pairs
	 * @param boolean $echo Output the generate textbox
	 * @return type
	 */
	public function getHidden($name,$attr=null,$echo=true){
		return $this->getGeneric($this->hidden,$name,$attr,$echo);
	}

	/**
	 * Get option element from the database
	 * @param string $name the name of the option element. Always a subarray of o (e.g: o[option-1]) 
	 * @param array $attr Atribute value pairs
	 * @param boolean $echo Output the generate textbox
	 * @return type
	 */
	public function getText($name,$attr=null,$echo=true){
		return $this->getGeneric($this->textbox,$name,$attr,$echo);
	}

	/**
	 * Get option element from the database
	 * @param string $name the name of the option element. Always a subarray of o (e.g: o[option-1]) 
	 * @param array $attr Atribute value pairs
	 * @param boolean $echo Output the generate textbox
	 * @return type
	 */
	public function getTextArea($name,$attr=null,$echo=true){
		return $this->getGeneric($this->textarea,$name,$attr,$echo);
	}

	/**
	 * Get checkbox option element from the database
	 * @param string $name the name of the option element. Always a subarray of o (e.g: o[option-1]) 
	 * @param array $attr Atribute value pairs
	 * @param boolean $echo Output the generate textbox
	 * @return type HTML representation of the return checkbox with value and name included
	 */
	public function getCheckbox($name,$attr=null,$echo=true){
		$value 			= viralplusError::val($name, $this->options, false, true);
		$value			= (empty($value)) ? '' : 'checked="checked" ';
		$attributes  	= is_array($attr) ? $this->attr_to_string($attr) : '';
		$html 			= sprintf($this->checkbox, $name, $value, $attributes);
		
		if ($echo)
			echo $html;

		return $html;
	}
	/**
	 * Get checkbox option element from the database
	 * @param string $name the name of the option element. Always a subarray of o (e.g: o[option-1]) 
	 * @param array $selects Keys are the option values matching the values as labels of the option element
	 * @param array $attr Atribute value pairs
	 * @param boolean $echo Output the generate textbox
	 * @return type HTML representation of the return select with value and name included
	 */
	public function getSelect($name,$selects,$attr=null,$echo=true){
		$value 			= viralplusError::val($name, $this->options, false, true);
		$html 			= $this->select;
		$options 		= '';
		$attributes  	= is_array($attr) ? $this->attr_to_string($attr) : '';
		foreach ($selects as $s_name => $s_label) {
			$selected= (strcasecmp($s_name, $value)===0) ? ' selected="selected"' : '';
			$options .= sprintf($this->option,$s_name,$s_label,$selected);
		}
		$html=  sprintf($html,$name,$options,$attributes);

		if ($echo)
			echo $html;

		return $html;

	}


	/**
	 * Fill in a generic option template, template to be supplied
	 * @param string $template The template to be filled in 
	 * @param string $name The name of the associated option
	 * @param array $attr 
	 * @param type $echo 
	 * @return type
	 */
	private function getGeneric($template, $name,$attr=null,$echo=true){
		$value 		= viralplusError::val($name, $this->options, false, true);
		$attributes  = is_array($attr) ? $this->attr_to_string($attr) : '';
		$html 		= sprintf($template, $name, $value, $attributes);
		
		if ($echo)
			echo $html;

		return $html;

	}

	/**
	 * Generate an html representation of attribute value pairs
	 * @param array $arr_attr an array of attributeswith key containing the attribute name and the value a string of the attribute value 
	 * @return string a HTML representation of a value attribute pair.
	 */
	private function attr_to_string($arr_attr){
		$html = '';
		foreach ($arr_attr as $a_name => $a_val) {
			$html .= " {$a_name}=\"{$a_val}\" ";
		}
		return $html;
	}
	
}

/**
 * An AdminViewSimple class is often used to represent option tables not requiring
 * relational data, or simple relations which can be defined in an array 
 * @author Wptit
 */
abstract class  viralAdminViewSimple   extends viralAdminView{

	/** An error object containing various methods to generate messages and chec input*/	
	protected $err 			= null;
	protected $options 	 	= array();
	protected $option_name  = '';
	protected $default 		= array();

	/**
	 *  This function processes an request to a specific admin page. It differentations
	 * between an update request (post) and retrieval of the admin page
	 */
	public function process_request(){
		if (viralplusError::is_POST()){
			$this->options = $_POST;

		} elseif (!viralplusError::is_POST()){
			$this->options = $this->fetch();
		}
	}

	/**
	 * Fetches the options associated with this specific admin page
	 */
	private function fetch(){
		return array('o'=>get_option($this->option_name,$this->default));
	}

	/**
	 * Write the options to the database
	 */
	public function save(){
		if (isset($_POST['o']) && (!empty($this->option_name)) && (!$this->err->has_error()) && wp_verify_nonce($_POST['arvlb-update-forms']) ){
			update_option($this->option_name,$_POST['o']);	
			$this->state = 2;
		}
	}

	/**
	 * Make sure the option page is setup nice and neat
	 * @param string $option_name the name of the option page
	 * @param string $default an array of default option to be used when no database entry is present
	 */
	function __construct($option_name='',$default=array()){
		$this->option_name 	= $option_name;
		$this->err 		 	= new viralErrorClass();
		$this->default 		= $default;
	}

}


/**
 * An more complex representation of relational data.
 * Instantiations must implement both process_save and process_edit
 * @author Wptit
 */
abstract class  viralAdminViewDB extends viralAdminView{

	protected $err 		= null;
	protected $options 	= array();
	protected $is_edit 	= null;
	protected $model 	= null;

	/* if disabled, fetching and saving wont be automatically*/
	private $process 	= true;

	public function save(){
		if (isset($_POST['o']) && (!$this->err->has_error())  && (wp_verify_nonce($_POST['arvlb-update-forms'])!==false)){
			$save = $this->is_edit() ? $this->process_edit : $this->process_save;
			$this->state = 2;
		}
	}


	public abstract function process_save();
	public abstract function process_edit();

	public function get_id(){
		return $this->is_edit;
	}

	public function set_edit($id){
		$this->is_edit = $id;
	}

	protected function is_edit(){
		return (!is_null($this->is_edit));
	}

	public function render_id_field(){
		if ($this->is_edit() )
			echo "<input type=\"hidden\" name=\"id\" value=\"{$this->is_edit}\" />";
	}

	public function render_id_action(){
		if ($this->is_edit() )
			echo "&id={$this->is_edit}";
	}

	function __construct($process=true){
		$this->err 		= new viralErrorClass();
	}

	public function process_request(){
		if (isset($_REQUEST['id'])){
			$this->is_edit = $_REQUEST['id'];
		}

		if (viralplusError::is_POST()){
			$data 			= (isset($_POST['o'])) ? $_POST['o'] : array();
			$this->options 	= array("o"=> $data);

		} elseif (!viralplusError::is_POST()){
			$this->options = array("o" => $this->model->fetch($this->get_id()) );
		}
	}

}
 ?>