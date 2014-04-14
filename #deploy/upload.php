<?php

set_time_limit(300);

// the file name that should be uploaded
$filep = 'src.zip'; 

$ftp_server = '69.27.46.100';
$ftp_user_name = 'apples';
$ftp_user_pass = 'Katanabutovo';

//path to the folder on which you wanna upload the file
$paths = 'public_html/offers/';
//the name of the file on the server after you upload the file
$name = $filep;

$conn_id=ftp_connect($ftp_server);


// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
// check connection

if ((!$conn_id) || (!$login_result)) {
       echo "FTP connection has failed!";
       echo "Attempted to connect to $ftp_server for user $ftp_user_name....";
       exit;
   } else {
       echo "OK:Connected to $ftp_server, for user $ftp_user_name".".....\n";
   }

// upload the file
$upload = ftp_put($conn_id, $paths.$name, $filep, FTP_BINARY);
 
// check upload status
if (!$upload) {
       echo "FTP upload has failed!";
   } else {
       echo "OK:Uploaded $name to $ftp_server \n";
   }


ftp_close($conn_id);

?>