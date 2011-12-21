<?php 

session_start();
      
// Include class & create
require_once("config.php");
require_once("twitteroauth/twitteroauth.php");
 
//setup database credentials
$dbhost = 'localhost';
$dbuser = 'xxx';
$dbpass = 'xxxxxxxxx';

// Check we have active session with access tokens from Twitter
if(!empty($_SESSION["access_token"]) && !empty($_SESSION["access_token"]["oauth_token"])) {

  // Create new TwitterOAuth object with access tokens
  $tOAuth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION["access_token"]["oauth_token"], $_SESSION["access_token"]["oauth_token_secret"]);

  // Perform an API request
  $credentials = $tOAuth->get('account/verify_credentials');
 
  $rawname = $credentials->screen_name;
  $screenname = strtolower($rawname);

  $ot = $_SESSION["access_token"]["oauth_token"];
  $ots = $_SESSION["access_token"]["oauth_token_secret"];

  //insert into db
  $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_error());
  mysql_select_db("xxxxxxxx") or die(mysql_error());

  $result = mysql_query('select screenname from userauth where screenname=\'' . $screenname . '\'');
  $i = mysql_num_rows($result);
  if($i == 1){ //already have this user, no need to update hasNewUsers

  }
  else{ //this screenname doesnt exist in table so we can insert and update hasNewUsers. set flag=1.
    $result1 = mysql_query("insert ignore into userauth (screenname, userid, ot, ots) values('$screenname', '$credentials->id', '$ot', '$ots')") or die(mysql_error());
    mysql_query('update hasNewUsers set flag=1 where flag=0'); //check race condition
  }
  mysql_close();

  $screenname = $credentials->screen_name;
  $userid = $credentials->id;


  include('welcomepage.inc');

}


?>




