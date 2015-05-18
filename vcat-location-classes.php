<?php
/** 
 * The main-class and father off all other classes
 */
class vcat_magic_methods{
	
	function __get($what)
	{
		return $this->{$what};
	}
	
	function __set($what, $value)
	{
		if(property_exists($this,$what))
			$this->{$what}=$value;
	}
	
	function __isset($what)
	{
		return isset($this->{$what});
	}
	
	function __unset($what)
	{
		unset($this->{$what});
	}
}


/**
 * the standard location class
 */
class vcat_geo_location extends vcat_magic_methods{
	protected $lat;
	protected $lng;
	protected $str;
	protected $twn;
	protected $zip;
	protected $pin;
	
	private function vcat_geo_get_lat_lng_by_address() {
		$address = $this->zip." ".$this->twn." ".$this->str;
		
		if( $address == "" || !isset( $address ) || $address == null )
			$this->lat = $this->lng = ""; 
					
		$req = 'http://maps.googleapis.com/maps/api/geocode/xml?sensor=false&address=' . urlencode( $address );
		
		$str = file_get_contents( $req );
		
		$xml = new SimpleXMLElement( $str );
		
		$this->lat= floatval( $xml->result[0]->geometry->location->lat );
		$this->lng = floatval( $xml->result[0]->geometry->location->lng );
	}
	
	public function update(){
		$this->vcat_geo_get_lat_lng_by_address();
	}
}

/**
 * the post location class
 */
class vcat_post_location extends vcat_geo_location {
	protected $pid;
}

/**
 * the pin class
 */
class pin extends vcat_magic_methods {
	protected $color;	
}
?>