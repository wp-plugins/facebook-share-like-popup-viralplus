<?php

/*

 * This class enables configuration of all front-end options of

 * google analytics and specific back-end options.

 * @author Wptit

 */

class viralAdminTop extends viralAdminViewSimple { 



	/*

 	* Initilizes a simple option view, with option arvlb-global

 	*/

	function __construct(){

		parent::__construct('arv_fb24_opt',viralSHARED::getDefaults() );

		

	}



	

	/*

	 * Renders a page

	 */

	public function render_page(){

		$o = $this->options;

			

?>	

<div class="wrap arv-opt">

	



<?php

 if ($this->getState()) { ?>

	<div class="updated"> Saved Successfully !</div>

<?php } ?>

<form method="POST">

<?php  wp_nonce_field(-1,'arvlb-update-forms');

	$this->getHidden('o[install_date]');

 ?>

 

<div id="tabs">

  <nav class="navbar nav-fullwidth">

   <ul>

     <li><a href="#tab-general">General</a> </li>

     

   </ul>

  </nav>

  <!-- General -->

   <div class="arv-tab" id="tab-general">

    <div class="onepcssgrid-1000">





		<div class="col3 formlabel">APP ID</div>

		<div class="col6 formselect">

		<?php	$this->getText('o[app_id]'); ?> <br />

		</div>

        <div class="col3 formlabel">Show On</div>

		<div class="col3 formselect">

		<?php $this->getCheckbox('o[display_on_page]');  ?> Pages <br />

		<?php	$this->getCheckbox('o[display_on_post]'); ?> Posts <br />

		</div>

		<div class="col3 formselect">

		<?php	$this->getCheckbox('o[display_on_homepage]');  ?> Homepage <br />

		<?php	$this->getCheckbox('o[display_on_archive]'); ?> Archives <br />

		</div>

		
		<div class="col3 last">&nbsp;</div>

		

		<div class="col3 formlabel">Show Post / Page Title</div>

		<div class="col6 formselect">

		<?php	$this->getCheckbox('o[display_title]'); ?> <br />

		</div>

		<div class="col3 last">&nbsp;</div>

		

		<div class="col3 formlabel">Share Button Text</div>

		<div class="col6 formselect">

		<?php	$this->getText('o[share_text]'); ?> <br />

		</div>

		<div class="col3 last">&nbsp;</div>

		<div class="col3 last">&nbsp;</div>

		<div class="col3 formlabel">Title</div>

		<div class="col6 formselect">

		<?php	$this->getText('o[display_toptitle]'); ?> <br />

		</div>

		<div class="col3 last">&nbsp;</div>

		

		

    	<div class="col3 formlabel">

    	Delay

		</div>

		<div class="col6 explain formselect">

			Delay in ms for the lightbox to appear (e.g &gt; 4000)	<br />

			<?php $this->getText('o[delay]'); ?>

		</div>

		<div class="col3 last">&nbsp;</div>

		

		<div class="col3 formlabel">Video</div>

		<div class="col3 formselect">

			<label>Height </label><?php $this->getText('o[video_height]'); ?>

		</div>

		<div class="col3 formselect">

			<label>Width <?php $this->getText('o[video_width]'); ?>

		</div>

		<div class="col3">&nbsp;</div>

        <div class="col3">&nbsp;</div>

		<!--<div class="col3 formlabel">Youtube Video Share Overlay</div>

		<div class="col3 formselect">

		<?php //$selects = array('0' => 'No','1' => 'Yes');$this->getSelect('o[display_fb_overlay]',$selects,$attr=null,$echo=true); ?>

		</div>-->

        

        <div class="col3 last">&nbsp;</div>

		<div class="col6 formlabel"></div>

		

		<div class="col3 last">&nbsp;</div>



		<div class="col12 last">&nbsp;</div>

    

	</div>

	</div>

	<!-- /general-->



	



</div>



<div class="onepcssgrid-1000">

	<div class="col4 last">
      <input type="hidden" name="o[display_fb_overlay]" value="0" />
      <input class="add-new-h2" style="width:100%;height:35px;" type="submit" value="  Save  "/>

</div>

 

 </div>



</form>







</div>

<?php



} //end do_page





}

 ?>