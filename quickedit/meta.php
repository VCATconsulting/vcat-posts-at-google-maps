<!-- dAd 09.09.2013 -->
<!-- Robin 17.09.2013 Entfernt was unnötig war, sowie Änderung der Namen damit es nich mit der normalen Box Probleme macht-->
<fieldset class="inline-edit-col">
	<div class="vcat-quick-edit-field">
	  <form>
	  	<input id="vcat_edit_type" type="hidden" name="_vcat_type" value="quick_edit"/>
	    <div class="wrapper">
	    	<div class="headline">
	    		<h4><?php _e('VCAT Geo Plugin','vcgmapsatposts')?></h4>
	    	</div>
		    <div class="street">
	   		 	<span class="text"><?php _e('Stra&szlig;e:','vcgmapsatposts')?></span>
	   		 	<span class="inputField">
		    		<input id="quick_edit_str" type="text" name="_vcat_quick_edit[str]" value=""/>
		    	</span>
	 	  	</div>
	     
		     <div class="plz">
		    	<span class="text"><?php _e('PLZ:','vcgmapsatposts')?></span>
		    	<span class="inputField">
		    		<input id="quick_edit_plz" type="text" name="_vcat_quick_edit[plz]" value=""/>
		    	</span>
		    </div>
		     
		     <div class="ort">
		    	<span class="text"><?php _e('Ort:','vcgmapsatposts')?></span>
		    	<span class="inputField">
		    		<input id="quick_edit_ort" type="text" name="_vcat_quick_edit[ort]" value=""/>
		    	</span>
		    </div>
	    </div>
		<input type="hidden" id="vcat_quickedit_field_nonce" name="vcat_quickedit_field_nonce" value="" />
	  </form>
	</div>	
</fieldset>