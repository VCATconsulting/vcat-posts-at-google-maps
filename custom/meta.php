<div class="vcat-custom-field">
  <form>
  	<input id="vcat_edit_type" type="hidden" name="_vcat_type" value="custom_fields"/>
    <p><?php _e('Hier können sie eine Adresse eingeben, die Longitude und Latitude werden dann automatisch ergänzt, 
    	und der Beitrag wird auf der Geomap markiert.</p>','vcgmapsatposts')?>
    
    <div class="wrapper">
	    <div class="street">
   		 	<span class="text"><?php _e('Stra&szlig;e:','vcgmapsatposts')?></span>
   		 	<span class="inputField">
	    		<input id="str" type="text" name="_vcat_custom_fields[str]" value="<?php if(!empty($post->str)) echo $post->str; ?>"/>
	    	</span>
 	  	</div>
     
	     <div class="plz">
	    	<span class="text"><?php _e('PLZ','vcgmapsatposts')?>:</span>
	    	<span class="inputField">
	    		<input id="plz" type="text" name="_vcat_custom_fields[plz]" value="<?php if(!empty($post->plz)) echo $post->plz; ?>"/>
	    	</span>
	    </div>
	     
	     <div class="ort">
	    	<span class="text"><?php _e('Ort','vcgmapsatposts')?>:</span>
	    	<span class="inputField">
	    		<input id="ort" type="text" name="_vcat_custom_fields[ort]" value="<?php if(!empty($post->ort)) echo $post->ort; ?>"/>
	    	</span>
	    </div>
    
	     <div class="lat">
	    	<span class="text"><?php _e('Latitude','vcgmapsatposts')?>:</span>
	    	<span class="inputField">
	    		<input readonly type="text" name="latitude" value="<?php if(!empty($post->lat)) echo $post->lat; ?>"/>
	    	</span>
	    </div>     
	    
	    <div class="lng">
	    	<span class="text"><?php _e('Longitude','vcgmapsatposts')?>:</span>
	    	<span class="inputField">
	    		<input readonly type="text" name="longitude" value="<?php if(!empty($post->lng)) echo $post->lng; ?>"/>
	    	</span>
	    </div>
    </div>
	<div class="save_button">
    	<input type="submit" value=<?php _e("Speichern",'vcgmapsatposts')?> />
    	<input type="submit" value=<?php _e("Löschen",'vcgmapsatposts')?>  onclick="document.getElementById('str').value=''; document.getElementById('plz').value=''; document.getElementById('ort').value=''"/>
    </div>
    
  </form>
</div>