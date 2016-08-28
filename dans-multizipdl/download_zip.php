<?php

require_once('../../../wp-blog-header.php');


if(isset($_POST['filename'])){
  if (is_array($_POST['filename'])) {

    # create new zip opbject
    $zip = new ZipArchive();
    # create a temp file & open it
    $tmp_file = tempnam('.','');
    $zip->open($tmp_file, ZipArchive::CREATE);

    # loop through each file
    foreach($_POST['filename'] as $fileshort){

		$directory_array= wp_upload_dir();

		$file = $directory_array['basedir'].'/zips/'.$fileshort;        # download file

		
        $download_file = file_get_contents($file);

        #add it to the zip
        $zip->addFromString(basename($file),$download_file);

    }

    # close zip
    $zip->close();

	$date = date('m-d-y');

    # send the file to the browser as a download
    header('Content-disposition: attachment; filename=Multidownload-'.$date.'.zip');
    header('Content-type: application/zip');
    readfile($tmp_file);

	unlink($tmp_file);
  } else {

	echo "No files selected";

  }
}

?>


