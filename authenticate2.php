<?php
require_once "login.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    mysql_fatal_error(connect_error,$conn);
}

//creat upload box for admin file
echo <<<_END
   <form method="post"  action="$_SERVER[PHP_SELF]"
   enctype="multipart/form-data">
   <pre>
Username <input type="text" name="Username">
password <input type="text" name="password">
   <input type="submit" name="submit"  value="Login">
  </pre></form>
_END;

echo "<br><a href= 'register.php'><input type=button value=signup name=signup></a></p>";
//creat search box for accus without login or sign up
echo <<<_END
   <form method="post"  action="$_SERVER[PHP_SELF]"
   enctype="multipart/form-data">
   <pre>
search   <input type="text" name="searchword">
   <input type="submit" name="submit2"  value="Search">
  </pre></form>
_END;

//get the user wored and find defult transltion in database
if(isset($_POST['submit2']))
{
//get the name of the use wored
$x=get_post($conn,'searchword');

$T=$x[0];
$sql = "SELECT word_translate FROM Lame
 WHERE user_word='$T'";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
echo "<a </a><br />";
        echo "DEFAULT TRANSLATION FOR  ".$x."  IS: " . $row["word_translate"];
echo "<a </a><br />";
}
}

//upload user translation and word file
if(isset($_POST['submit']))
{
if (isset($_POST['Username']) && isset($_POST['password'])){
//fix the user input
$un_temp =mysql_entities_fix_string($conn,$_POST['Username']);
$password =mysql_entities_fix_string($conn,$_POST['password']);
//sql to get the user information from database
$sql = "SELECT username, token, salt1, salt2 FROM authenticate
        WHERE username='$un_temp'";
$result = $conn->query($sql);

//compare the user password with the passowerd in the database
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
       // echo " username: " . $row["username"]."<br>". " token:".
         //     $row["token"]."<br>" . " salt1:". $row["salt1"]."<br>"
           //   . " salt2:". $row["salt2"]."<br>";
$salt1=$row["salt1"];
$salt2=$row["salt2"];
//hasing the user input with salt from database.
$token = hash('ripemd128', "$salt1$password$salt2");
if ( $un_temp ==  $row["username"] &&  $token == $row["token"]){

session_start();
$_SESSION['username'] = $un_temp;
//$_SESSION['password'] = $password;




echo " Hi ".$row["username"] .", you are now logged in";
 die ("<p><a href= mid2.php>Click here to continue</a></p>");
}

else die("Invalid username/password combination");
    }
} else {
    echo die( "Invalid username/password combination");
}
}
}

   //function escapes special characters in a string for use in an SQL statement.
   function get_post($conn, $var)
      {
      return $conn->real_escape_string($_POST[$var]);
      }

   //conecton error desplay
   function mysql_fatal_error($msg, $conn)
   {
   $msg2 = mysqli_error($conn);
   echo <<< _END
   We are sorry, but it was not possible to complete
   the requested task. The error message we got was:
   <p>
   $msg:$msg2
   </p>
   Please click the back button on your browser
   and try again. If you are still having problems,
   please <a href="mailto:admin@server.com">email
   our administrator</a>.<a </a><br/> Thank you.<br />
   _END;
   }


   function mysql_entities_fix_string($conn, $string) {
   return htmlentities(mysql_fix_string($conn, $string));
   }
   function mysql_fix_string($conn, $string) {
   if (get_magic_quotes_gpc()) $string = stripslashes($string);
   return $conn->real_escape_string($string);
   }

   $result->close();
   $conn->close();

   ?>



