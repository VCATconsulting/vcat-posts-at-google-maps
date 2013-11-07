<div class="vcat-custom-field">
  <form>
    <p>Hier können sie eine Adresse eingeben, die Longitude und Latitude werden dann automatisch ergänzt, 
    	und der Beitrag wird auf der Geomap markiert.</p>
    
    <div class="wrapper">
	    <div class="street">
   		 	<span class="text">Stra&szlig;e:</span>
   		 	<span class="inputField">
	    		<input id="str" type="text" name="_vcat_custom_fields[str]" value="<?php if(!empty($post->str)) echo $post->str; ?>"/>
	    	</span>
 	  	</div>
     
	     <div class="plz">
	    	<span class="text">PLZ:</span>
	    	<span class="inputField">
	    		<input id="plz" type="text" name="_vcat_custom_fields[plz]" value="<?php if(!empty($post->plz)) echo $post->plz; ?>"/>
	    	</span>
	    </div>
	     
	     <div class="ort">
	    	<span class="text">Ort:</span>
	    	<span class="inputField">
	    		<input id="ort" type="text" name="_vcat_custom_fields[ort]" value="<?php if(!empty($post->ort)) echo $post->ort; ?>"/>
	    	</span>
	    </div>
    
	     <div class="lat">
	    	<span class="text">Latitude:</span>
	    	<span class="inputField">
	    		<input readonly type="text" name="latitude" value="<?php if(!empty($post->lat)) echo $post->lat; ?>"/>
	    	</span>
	    </div>     
	    
	    <div class="lng">
	    	<span class="text">Longitude:</span>
	    	<span class="inputField">
	    		<input readonly type="text" name="longitude" value="<?php if(!empty($post->lng)) echo $post->lng; ?>"/>
	    	</span>
	    </div>
    </div>
	<div class="save_button">
    	<input type="submit" value="Speichern" />
    	<input type="submit" value="Löschen"  onclick="document.getElementById('str').value=''; document.getElementById('plz').value=''; document.getElementById('ort').value=''"/>
    </div>
    
  </form>
</div>