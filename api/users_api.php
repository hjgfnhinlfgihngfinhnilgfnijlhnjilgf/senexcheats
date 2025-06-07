<?php
  $con = mysqli_connect("localhost", "gamesens_forumadmin", "logan1591", "gamesens_forum");
  if(!$con) {
    die("noconn");
  }
  $username = "";
  $nopass = "";
  if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['nopass']) && !empty($_POST['nopass'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $user_password = mysqli_real_escape_string($con, $_POST['nopass']);
    $nopass = md5($nopass);
  }
  elseif(isset($_GET['username']) && !empty($_GET['username']) && isset($_GET['nopass']) && !empty($_GET['nopass'])) {
    $username = mysqli_real_escape_string($con, $_GET['username']);
    $nopass = mysqli_real_escape_string($con, $_GET['nopass']);
    $nopass = md5($nopass);
  } else {
    die("nodata");
  }
  $query_login = $con->Query("SELECT nopass FROM users WHERE username = '".$username."' LIMIT 1");
  if(!$query_login) {
    die("dataerror");
  }
  elseif($query_login->num_rows != 1) {
    die("usernotfound");
  } else {
    $query_result = $query_login->Fetch_assoc();
    if($nopass != $query_result['nopass']) {
      die("userwrongpassword");
    }
  }
?>