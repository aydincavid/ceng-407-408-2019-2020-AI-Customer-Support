<?php
include_once("inc/Conn.php");
include_once("helpers/helperDev.php");
session_start();

$conne = new Mysql();
$conn = $conne->dbConnect();

$action = $_POST["action"];
$Email = $_POST['email'];
$Password = trim($_POST['password']);
$options = array("cost"=>4);
$hashPassword = password_hash($Password,PASSWORD_BCRYPT,$options);

if($action == "login") {
  $query = $conn->query("SELECT * FROM users WHERE email='$Email'",PDO::FETCH_ASSOC)->fetch();
  if (isset($query)){
      if(password_verify($Password,$query['password_'])){
          $_SESSION["user_UserID"]=$query['user_id'];
          $_SESSION["user_Username"]=$query['username'];
          $_SESSION["user_Firstname"]=$query['firstname'];
          $_SESSION["user_Surname"]=$query['surname'];
          $_SESSION["user_isAdmin"]=$query['is_admin'];
          $_SESSION["user_isVerified"]=$query['is_verified'];
          $_SESSION["user_Email"]=$Email;
          return 1;
    }else {
        // Email and password does not match
        return -1;
    }
  }
}

if ($action == "register") {
  if($Email == NULL || $Email == ''){
    header('Location: index.php');
  }

  $Username = $_POST['username'];
  $Firstname = $_POST['firstname'];
  $Lastname = $_POST['lastname']; 
  $ConfirmPassword = $_POST['confirmpass'];
  $UserIp = helperDev::get_client_ip();

  if($ConfirmPassword == $Password){
    $sqlAddUser = "INSERT IGNORE INTO users(firstname,surname,email,username,password_,ip_address,is_verified,is_admin)
    VALUES ('$Firstname','$Lastname','$Email','$Username','$hashPassword','$UserIp',0,0);";
    $conn->exec($sqlAddUser);

    $query = $conn->query("SELECT * FROM users WHERE email='$Email' && password_='$Password'",PDO::FETCH_ASSOC);

    if ($count = $query -> rowCount()){
      if($count > 0){
        // Email and password match
        $sql = "SELECT user_id,firstname,surname,username,is_verified,is_admin 
                FROM users WHERE email='$Email' && password_='$Password'";
        $q = $conn->query($sql);
        $q->setFetchMode(PDO::FETCH_ASSOC);
        while($r=$q->fetch()){
          $_SESSION["user_UserID"]=$r['user_id'];
          $_SESSION["user_Username"]=$r['username'];
          $_SESSION["user_Firstname"]=$r['firstname'];
          $_SESSION["user_Surname"]=$r['surname'];
          $_SESSION["user_isAdmin"]=$r['is_admin'];
          $_SESSION["user_isVerified"]=$r['is_verified'];
        }
        $_SESSION["user_Email"]=$email;
        return 1;
      }
    }
  }
}
?>