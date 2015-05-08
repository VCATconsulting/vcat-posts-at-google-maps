<div class="vcat-custom-field">
  	<input id="vcat_edit_type" type="hidden" name="_vcat_type" value="custom_fields"/>
    <p><?php _e('Here you can type the address in. The latitude and longitude are added automatically. This post / page is marked at Google map.','vcgmapsatposts')?></p>
    <div class="wrapper">
		<div class="wrap-input">
            <div class="street">
                <span class="text"><?php _e('Street','vcgmapsatposts')?></span>
                <span class="inputField">
                    <input id="str" type="text" name="_vcat_custom_fields[str]" value="<?php if(!empty($post->str)) echo $post->str; ?>" onChange="document.getElementById('latitude').value='';document.getElementById('longitude').value='';" />
                </span>
            </div>

             <div class="plz">
                <span class="text"><?php _e('Zip','vcgmapsatposts')?>:</span>
                <span class="inputField">
                    <input id="plz" type="text" name="_vcat_custom_fields[plz]" value="<?php if(!empty($post->plz)) echo $post->plz; ?>" onChange="document.getElementById('latitude').value='';document.getElementById('longitude').value='';"/>
                </span>
            </div>

             <div class="ort">
                <span class="text"><?php _e('Place','vcgmapsatposts')?>:</span>
                <span class="inputField">
                    <input id="ort" type="text" name="_vcat_custom_fields[ort]" value="<?php if(!empty($post->ort)) echo $post->ort; ?>" onChange="document.getElementById('latitude').value='';document.getElementById('longitude').value='';"/>
                </span>
            </div>

             <div class="lat">
                <span class="text"><?php _e('Latitude','vcgmapsatposts')?>:</span>
                <span class="inputField">
                    <!-- dadz 21042015 add id -->
                    <input id="latitude" readonly type="text" name="_vcat_custom_fields[lat]" value="<?php if(!empty($post->lat)) echo $post->lat; ?>"/>
                </span>
            </div>

            <div class="lng">
                <span class="text"><?php _e('Longitude','vcgmapsatposts')?>:</span>
                <span class="inputField">
                    <!-- dadz 21042015 add id -->
                    <input id="longitude" readonly type="text" name="_vcat_custom_fields[lng]" value="<?php if(!empty($post->lng)) echo $post->lng; ?>"/>
                </span>
            </div>

            <div class="color">
                <span class="text"><?php _e( 'Pin Color' , 'vcgmapsatposts' ); ?>:</span>
                <span class="inputField">
					<select name="_vcat_custom_fields[color]"><!-- <?php var_export( $post ); ?> -->
						<option value=""><?php _e( 'Standard', 'vcgmapsatposts' ); ?></option>
<?php
	global $VCAT_PIN_COLORS;
	foreach( $VCAT_PIN_COLORS as $colkey => $colval ) {
	  echo '<option value="' . $colkey . '"' . selected( $post->color, $colkey, false ) . '>' . $colval . '</option>';
	}
?>                  
					</select>
                </span>
            </div>
        </div>
		<div class="wrap-maps">
			<?php echo do_shortcode('[vcat-dpagm-mini zoom="16" width="50%" height="225px" center="dynamic"]'); ?>
		</div>
    </div>
	<div class="save_button">
		<!-- dadz 21042015 Standort-Button -->
		<input type="button" value="<?php _e("Use current location",'vcgmapsatposts') ?>" onClick="checkGeoApi();document.getElementById('str').value=''; document.getElementById('plz').value=''; document.getElementById('ort').value=''" />
		<!-- dadz 21042015 ende -->
    	<input type="submit" name="save" value="<?php _e("Save",'vcgmapsatposts')?>" />
    	<input type="submit" name="save" value="<?php _e("Delete",'vcgmapsatposts')?>" onclick="document.getElementById('str').value=''; document.getElementById('plz').value=''; document.getElementById('ort').value=''; document.getElementById('latitude').value='';document.getElementById('longitude').value='';"/>
    </div>
</div>