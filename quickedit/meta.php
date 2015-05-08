<!-- dAd 09.09.2013 -->
<!-- Robin 17.09.2013 Entfernt was unnötig war, sowie Änderung der Namen damit es nich mit der normalen Box Probleme macht-->
<fieldset class="inline-edit-col">
	<div class="vcat-quick-edit-field inline-edit-col">
	  	<input id="vcat_edit_type" type="hidden" name="_vcat_type" value="quick_edit"/>
	    <div class="wrapper">
	    	<div class="headline">
	    		<h4><?php _e('VCAT Geo Plugin','vcgmapsatposts')?></h4>
	    	</div>
		    <label>
	   		 	<span class="title"><?php _e('Street','vcgmapsatposts')?></span>
	   		 	<span class="input-title-wrap">
		    		<input id="quick_edit_str" type="text" name="_vcat_quick_edit[str]" value=""/>
		    	</span>
	 	  	</label>
	     
		     <label>
		    	<span class="title"><?php _e('Zip','vcgmapsatposts')?></span>
		    	<span class="input-title-wrap">
		    		<input id="quick_edit_plz" type="text" name="_vcat_quick_edit[plz]" value=""/>
		    	</span>
		    </label>
		     
		     <label>
		    	<span class="title"><?php _e('Place','vcgmapsatposts')?></span>
		    	<span class="input-title-wrap">
		    		<input id="quick_edit_ort" type="text" name="_vcat_quick_edit[ort]" value=""/>
		    	</span>
		    </label>
		    
		    <label>
	    	<span class="title"><?php _e('Pin Color','vcgmapsatposts')?>:</span>
	    	<span class="input-title-wrap">
    			<select id="quick_edit_color" name='_vcat_quick_edit[color]'>
				  <option value=''><?php _e( 'Standard', 'vcgmapsatposts' ); ?></option>		
<?php
	global $VCAT_PIN_COLORS;
	foreach( $VCAT_PIN_COLORS as $colkey => $colval ) {
	  echo '<option value="' . $colkey . '">' . $colval . '</option>';
	}
?>
			    </select>
	    	</span>
	    </label>
	    </div>
		<input type="hidden" id="vcat_quickedit_field_nonce" name="vcat_quickedit_field_nonce" value="" />
	</div>	
</fieldset>