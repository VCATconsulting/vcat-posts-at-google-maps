<?php
define( 'ENABLE_CACHE', true);

if( !function_exists( 'vcat_core_get_feed' ) ) {
	function vcat_core_get_feed( $url ) {
		$txt = file_get_contents( $url );
		if( ! $txt ) return '';
		$xml = simplexml_load_string( $txt );
		if( ! $xml ) return '';
		$res = $xml->xpath('//item');
		
		$out = '<ul class="feed">';
		while(list( , $node) = each($res))
		{
			$out .= '<li><a href="'.$node->link.'" target="_blank">'.htmlspecialchars( $node->title ).'</a></li>';
		}
		$out .= '</ul>';
		return $out;
	}
}
	
if( !function_exists( 'vcat_core_get_cached_feed' ) ) {
	function vcat_core_get_cached_feed( $url ) {
		$cached = wp_cache_get( $url );
		if( $cached == FALSE )
		{
			$cached = vcat_core_get_feed($url);
			wp_cache_set($url, $cached, '', 30);
			#$cached = 'LIVE:' . $cached;
		}
		return $cached;
	}
}

if( !function_exists( 'vcat_core_get_backend_sidebar' ) ) {
	function vcat_core_get_backend_sidebar() {
?>
		<div id="vc_sidebar">
		    <div id="donate_div">
		    	<a href="http://www.vcat.de/edulabs/ueber-vcat-edulabs/spenden/" target="_blank" >
		    		<img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/donate_small.png" alt="Donate" />
		    	</a>
		    </div>
		    <div id="edulabs_feed">
		    	<h3><a href="http://www.vcat.de/edulabs/feed/" target="_blank">VCAT EDULABS<br />Feed</a></h3>
		    	<?php echo vcat_core_get_cached_feed('http://www.vcat.de/edulabs/feed/'); ?>
		    </div>
		    <div id="vcat_feed">
		    	<h3><a href="http://www.vcat.de/unternehmen/aktuelles.html?type=100" target="_blank">VCAT Consulting<br />Feed</a></h3>
		    	<?php echo vcat_core_get_cached_feed('http://www.vcat.de/unternehmen/aktuelles.html?type=100'); ?>
		    </div>
		    <div id="follow_vcat">
		    	<h3>Folgen Sie uns!</h3>
		    	<ul>
		    		<li><a href="https://www.facebook.com/VCATconsulting" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/social/facebook-24x24.png" alt="VCAT bei Facebook"/></a></li>
		    		<li><a href="https://plus.google.com/111862866638114567280/posts" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/social/google-24x24.png" alt="VCAT bei Google+"/></a></li>
		    		<li><a href="http://www.slideshare.net/VCATconsulting" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/social/slideshare-24x24.png" alt="VCAT bei Slideshare"/></a></li>
		    		<li><a href="https://twitter.com/VCATconsulting" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/social/twitter-24x24.png" alt="VCAT bei Twitter"/></a></li>
		    		<li><a href="http://profiles.wordpress.org/VCATconsulting" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/social/wordpress-24x24.png" alt="VCAT bei WordPress"/></a></li>
		    		<li><a href="http://www.xing.com/companies/vcatconsultinggmbh" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/social/xing-24x24.png" alt="VCAT bei Xing"/></a></li>
		    	</ul>
		    </div>
		</div>
<?php
	}
}
