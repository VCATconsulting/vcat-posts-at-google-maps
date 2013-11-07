<?php
define ( 'ENABLE_CACHE', true);

function get_feed($url)
{
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
};

function get_cached_feed($url)
{
	$cached = wp_cache_get( $url );
	if( $cached == FALSE )
	{
		$cached = get_feed($url);
		wp_cache_set($url, $cached, '', 30);
		#$cached = 'LIVE:' . $cached;
	}
	return $cached;
};


?>

<div id="vc_sidebar">
    <div id="donate_div">
    	<a href="http://www.vcat.de/edulabs/ueber-vcat-edulabs/spenden/" target="_blank" >
    		<img src="<?php echo PLUGIN_PATH; ?>/backend/donate_small.png" alt="Donate" />
    	</a>
    </div>
    <div id="edulabs_feed">
    	<h3>EDULABS FEED</h3>
    	<?php echo get_cached_feed('http://www.vcat.de/edulabs/feed/'); ?>
    </div>
    <div id="vcat_feed">
    	<h3>VCAT FEED</h3>
    	<?php echo get_cached_feed('http://www.vcat.de/unternehmen/aktuelles.html?type=100'); ?>
    </div>
</div>