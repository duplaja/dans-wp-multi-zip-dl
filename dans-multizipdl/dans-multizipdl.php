<?php 
 /**
* Plugin Name: Dan's WP Multi-Zip Download
* Plugin URI: https://www.convexcode.com
* Description: A plugin to list files for download, in /uploads/zip/your-subfolder-name (multiple allowed) as a zip file.
* Version: 0.1
* Author: Dan Dulaney
* Author URI: https://www.convexcode.com
**/


//creates an entry on the admin menu for dan-gcal-plugin
add_action('admin_menu', 'wpmultizipdl_plugin_menu');

//creates a menu page with the following settings
function wpmultizipdl_plugin_menu() {
	add_menu_page('Dans Multizip Settings', 'Dans Multizip', 'administrator', 'wpmultizipdl-settings', 'wpmultizipdl_display_settings', 'dashicons-admin-generic');
}

//on-load, sets up the following settings for the plugin
add_action( 'admin_init', 'wpmultizipdl_settings' );

function wpmultizipdl_settings() {
	register_setting( 'wpmultizipdl-settings-group', 'multizipdir' );
}




function wpmultizipdl_display_settings() {

	//form to save api key and calendar settings
	echo "<form method='post' action='options.php'>";

	//loads up settings group
	settings_fields( 'wpmultizipdl-settings-group' );
	do_settings_sections( 'wpmultizipdl-settings-group' );



	echo "<script>function addRow(nextnum,nextdisp){


	var toremove = 'addrowbutton';
	var elem = document.getElementById(toremove);
    elem.parentNode.removeChild(elem);

	var table = document.getElementById(\"multizipdirsettings\");
	var row = table.insertRow(-1);
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	c1var = '<b>Folder (#: '+nextdisp+')</b>';
	cell1.innerHTML = c1var;
	var newnextdisp= nextdisp+1;
	c2var = '<input type=\"text\" name=\"multizipdir['+nextnum+']\" size=\"80\"><button type=\"button\" id=\"addrowbutton\" onClick=\"addRow('+nextdisp+','+newnextdisp+')\">Add Row</button>';
	cell2.innerHTML = c2var;
}</script>

";

	echo "<div><h1>Dan's Multi File Zip Settings</h1>

<p>Welcome! This is a file downloader plugin that offers the ability for the user to select which files (from a list) they want to download as a combined zip file.</p><p> Features Include: <ul style=\"list-style-type:square\">
<li>Displays a list of all files within a specified folder.</li>
<li>Lets the user select (checkboxes) which files they want to download.</li>
<li>All options are configured via shortcode</li>
<li>Attempts to create the specified folder (backend) if it does not exist</li>
<li>Disabled download button when no files selected</li> 
</ul>
<br>
<b>Shortcodes:</b>
<ul style=\"list-style-type:square\"><li>Default Display [dans-multizip] (defaults to 1st folder)</li></ul>
Optional Attributes Ex:[dans-multizip dir=1 divid=dllist]
<ul style=\"list-style-type:square\">
<li>dir= (number of the folder you want, defaults to 1 if not entered)</li>
<li>divid= (id of the div your file display list is stored in, for custom theming. Defaults to random string to allow multiple per page)</li>
</ul>";

	$multizipdir = get_option('multizipdir');

	$upload_directory= wp_upload_dir();
	$directory_base = $upload_directory['basedir'].'/zips/';

	echo "<h3>Status of Existing Folder Settings:</h3><ul>";
	
	foreach ($multizipdir as $shortfile) {

		$directory = $directory_base."$shortfile";

		if(file_exists($directory)) {


			echo "<li>Shortfile $shortfile exists</li>";

		} else {

			echo "<li>Shortfile $shortfile does not exist.. creating $directory now.";
			if (wp_mkdir_p($directory)) {
				  echo ' <b>Created!</b></li>';
			} else {

				echo " <b>Failed. (Create manually)</b></li>";
			}

			

		}

	}

	echo "</ul></p>";


	echo "<h3>Saved Directories / Folders</h3>";

	echo "<table id='multizipdirsettings'>";

$num_dir = 0;
$num_dir = count($multizipdir);

if ($num_dir > 1) $showrows=$num_dir; 
else $showrows = 1;

for ($i=0;$i < $showrows; $i++) {
	$nextid = $i+1;
	$nextdisp = $i+2;
	$dirnum = $i+1;
	echo " 
       <tr valign=\"top\">
        <th scope=\"row\">Folder (#: $dirnum)</th>
        <td><input type=\"text\" name=\"multizipdir[$i]\" size=\"80\" value=\"$multizipdir[$i]\"/>
";

if (($showrows -1) == $i) {

echo "<button type=\"button\" id=\"addrowbutton\" onClick=\"addRow($nextid,$nextdisp)\">Add Row</button>";

}
echo "</td></tr>";

}
       
   echo" </table>";


	
    
    submit_button();

	echo '</form>';


}



//this is the function run when the shortcode is called with [dans-multizip]

function wpmultizipdl_shortcode_output( $atts ){

	//generates a random div id to allow multiple on one page, if one isn't specified in shortcode
	$randdiv = 'a'.substr(md5(microtime()),rand(0,26),10);

	//Handles attribures. If none are specified, defaults to no scroll, 1st drive	
	$atts = shortcode_atts(
        array(
            'dir' => 1,
		  'divid' => $randdiv,
        ), $atts, 'dans-multizip' );

	$dir = $atts['dir'];
	$divid = $atts['divid'];

	$multizipdir = get_option('multizipdir');

	$dir_num = $dir-1;

	$dir = $multizipdir[$dir_num];
	
	if ($dir == '') { 
		
		$error = 'You must first enter a valid folder number.';
		return $error;
	}


	$directory_array= wp_upload_dir();
	$directory = $directory_array['basedir'].'/zips/'."$dir";

	if(file_exists($directory)) {

   		 echo "<div id=\"$divid\" style='width:500px'>  


		<form method='POST' action='".plugin_dir_url( __FILE__ ) ."download_zip.php'>

		<table cellpadding=\"5\"><tr><th>DL?</th><th>File Name</th></tr>";
	    // fetches all executable files in that directory and loop over each
	    foreach(glob($directory.'/*') as $file) { 

	  	   $fileshort = str_replace($directory.'/','',$file);
	        echo '<tr><td>

			<input name="filename[]" type="checkbox" value="'.$fileshort.'">
	
			</td><td>'.$fileshort."</td></tr>";

			$i++;
	    }
	    echo "<tr><td colspan='2'><input id='submit' name='submit' type='submit' value='Download Zip'></td></table></form></div>


		<script>
		var submit = jQuery('#submit');
		checkbox = jQuery('input[type=checkbox]');
	
		submit.prop('disabled', true);

		checkbox.on('click', function(){
	    if (jQuery(\"input:checkbox:checked\").length > 0) {
	        submit.removeAttr('disabled');
	    }else{
	        submit.prop('disabled', true);
	    }
		});

		</script>

		";
	} else {

		echo "File does not exist";

	}

}

add_shortcode('dans-multizip', 'wpmultizipdl_shortcode_output');
