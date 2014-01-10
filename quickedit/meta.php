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
	   		 	<span class="title"><?php _e('Stra&szlig;e','vcgmapsatposts')?></span>
	   		 	<span class="input-title-wrap">
		    		<input id="quick_edit_str" type="text" name="_vcat_quick_edit[str]" value=""/>
		    	</span>
	 	  	</label>
	     
		     <label>
		    	<span class="title"><?php _e('PLZ','vcgmapsatposts')?></span>
		    	<span class="input-title-wrap">
		    		<input id="quick_edit_plz" type="text" name="_vcat_quick_edit[plz]" value=""/>
		    	</span>
		    </label>
		     
		     <label>
		    	<span class="title"><?php _e('Ort','vcgmapsatposts')?></span>
		    	<span class="input-title-wrap">
		    		<input id="quick_edit_ort" type="text" name="_vcat_quick_edit[ort]" value=""/>
		    	</span>
		    </label>
		    
		    <label>
	    	<span class="title"><?php _e('Pin-Farbe','vcgmapsatposts')?>:</span>
	    	<span class="input-title-wrap">
    			<select id="quick_edit_color" name='_vcat_quick_edit[color]'>
				  <option value=''><?php _e("Standard",'vcgmapsatposts')?></option>
				  <option value='blue'><?php _e("Blau",'vcgmapsatposts')?></option>
			      <option value='red'><?php _e("Rot",'vcgmapsatposts')?></option>
			      <option value='yellow'><?php _e("Gelb",'vcgmapsatposts')?></option>
			      <option value='green'><?php _e("Grün",'vcgmapsatposts')?></option>
			      <option value='orange'><?php _e("Orange",'vcgmapsatposts')?></option>
			      <option value='purple'><?php _e("Lila",'vcgmapsatposts')?></option>
			      <option value='magenta'><?php _e("Magenta",'vcgmapsatposts')?></option>
			      <option value='cyan'><?php _e("Cyan",'vcgmapsatposts')?></option>
			      <option value='pink'><?php _e("Rosa",'vcgmapsatposts')?></option>
			      <option value='brown'><?php _e("Braun",'vcgmapsatposts')?></option>
			      <option value='beige'><?php _e("Beige",'vcgmapsatposts')?></option>
			      <option value='gray'><?php _e("Grau",'vcgmapsatposts')?></option>
			    </select>
	    	</span>
	    </label>
	    </div>
		<input type="hidden" id="vcat_quickedit_field_nonce" name="vcat_quickedit_field_nonce" value="" />
	</div>	
</fieldset>