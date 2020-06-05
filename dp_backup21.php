<?php
/**
 * Plugin Name: didiplayer
 * Plugin URI: https://chroniclesofdidi.com
 * Description: Display content using a shortcode to insert in a page or post
 * Version: 2.1
 * Text Domain: didiplayer
 * Author: Mraowr
 * Author URI: https://chroniclesofdidi.com
 */
 
 #Contentwill display after the loop
 
 #necessary inputs for shortcode  [didiplayer feedurl=""] mini="y" will give mini player--only plays first ep
 
 function didiplayer_function($atts) {
     
     $atts = shortcode_atts( array(
			'feedurl' => '',
			'quantity' => '10',
			'imgsize' => '200',
			'imgclass' => 'alignleft',
			'itunes' => '',
			'google' => '',
			'soundcloud' => '',
			'icons' => 'true' ,
			'mini' => 'n'
		), $atts, 'podcastfeed' );
		
    $feedurl = $atts['feedurl'];
    $itunes = $atts['itunes'];
    $google = $atts['google'];
    $soundcloud = $atts['soundcloud'];
    $quantity = $atts['quantity'];
    $imgsize = $atts['imgsize'];
    $imgclass = $atts['imgclass'];
    $showicons = $atts['icons'];
    $mini = $atts['mini']; 
     

     
    $xml=simplexml_load_file($feedurl, SimpleXMLIterator) or die("Error: Cannot create object, double check your feed url");


    #Switch for mini vs. full player
    switch ($mini) {
    case "y" : 
    $Content .='<div class=dp_mini_player>';
        $Content .= '<p class="dp_mini_title">' . 'Episode ';
                    $Content .= $xml->channel->item->children('itunes', true)->episode . ': ';
                $Content .= $xml->channel->item->title . '</p>';
        
        #wordpress player  
        $Content .= '<div class="dp_wp_player">';
            $Content .= '<p class="dp_wp_player">';
	            $attr = array( 'src' => $xml->channel->item->enclosure['url'], 'loop' => '', 'autoplay' => '', 'preload' => 'none' ); 
	            $Content .= wp_audio_shortcode( $attr );  
            $Content .='</p>';	            
        $Content .= '</div>';   
    $Content .='</div>';    
        break;
   default:     
	 
        #Channel Title    
        # $Content .= '<p class="dp_cht">' . $xml->channel->title . '</p>';


        $Content .= '<div class="dp_player_full">';
            #Loop starts here
            $limited = new LimitIterator($xml->channel->item, 0 , $quantity);
            foreach ( $limited as $feed)
                {
                #Episode Number and title
                $Content .= '<p class="dp_ept">' . 'Episode ';
                    $Content .= $feed->children('itunes', true)->episode . ': ';
                $Content .= $feed->title . '</p>';
                    #Image Div
                    $Content .= '<div class="dp_imgsum">';
                        #Episode image    
                            $Content .= '<p class="dp_epimage"><img src="' . $feed->children('itunes', true)->image->attributes() . '" height="'$imgsize'" width="'$imgsize'" </p><br>';
                    $Content .= '</div>';
    
                    #Episode summary
                    $Content .= '<p class="dp_epsummary">' . $feed->children('itunes', true)->summary . '</p>';
    
                #wordpress player  
                $Content .= '<div class="dp_wp_player">';
                    $Content .= '<p class="dp_wp_player">';
	                    $attr = array( 'src' => $feed->enclosure['url'], 'loop' => '', 'autoplay' => '', 'preload' => 'none' ); 
	                    $Content .= wp_audio_shortcode( $attr );   
	                    $Content .= '</div>';
                }
                #Loop ends div below closes episodes of full player
$Content .= '</div>';   

$Content .= '<div>Above are our most recent episodes. For continuous playback or older episodes, subscribe via one of these fine platforms: (links coming soon…)</div>';


#button to copy RSS
$Content .='<input type="text" value="';
$Content .=$feedurl;
$Content .='" id="myInput" style="display:none">
<button id="dp_rss_button" onclick="myFunction()">Click here to copy feed url to clipboard</button>

<script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Copied the url to your clipboard: " + copyText.value);
}
</script>';

}
#Switch ends with above bracket
return $Content;
    
}

add_shortcode('didiplayer', 'didiplayer_function');
?>
