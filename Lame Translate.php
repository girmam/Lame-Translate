<?php
/*

CREATE DATABASE admin;

USE admin;

$sql = "CREATE TABLE Lame
(
user_name varchar(50) NOT NULL,
user_word varchar(50) NOT NULL,
word_translate varchar(50) NOT NULL
)";

$sql = "CREATE TABLE authenticate
(
username varchar(50) NOT NULL UNIQUE,
token varchar(128) NOT NULL,
salt1 int NOT NULL,
salt2 int NOT NULL
)";

GRANT ALL PRIVILEGES ON *.* TO
 'username'@'localhost' IDENTIFIED BY 'password';
 */

/*
 The translation designs as a single table. when each user logged in and
 upload a file which contains the word followed by  its translation
 e.g., if the word is "dmet" and the English translation is "cat"
 The user writes it like "dmet cat " on the file, then
 the program separates the two words by checking the space between them and
 store it as an array, then the SQL query
 sends it to the database by relating to the user
 name from the session variable. For example, if the user
 name for the use, when loged in, is "girma"
 the database stores the word
 "girma dmet cat" in three columns.
*/
require_once "login.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    mysql_fatal_error(connect_error,$conn);
}
session_start();
if (isset($_SESSION['username'])){
$username = $_SESSION['username'];
//create upload box for user file
echo <<<_END
   <form method="post"  action="$_SERVER[PHP_SELF]"
   enctype="multipart/form-data">
   <input  type="hidden"  name="flag" value="1" />
   <input  type="file"    name="user_file" />
   <input  type="submit"
   action="mid2.php"  method="post"><pre>
   search <input type="text" name="searchword">
   <input type="submit" name="submit"  value="Continue">
   </pre></form>
_END;
echo "<br><a href= 'logout.php'><input type=button value=logout name=logout></a></p>";
//upload user file
if(isset($_POST['submit'])){
//get the user word to translate
$x=get_post($conn,'searchword');
//echo ($x);
$sql = "SELECT word_translate FROM Lame
 WHERE user_name='$username' AND user_word='$x'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
echo "<a </a><br />";
        echo "TRANSLATION FOR  ".$x."  IS: " . $row["word_translate"];
echo "<a </a><br />";
}
} else {
//for defult translation checking the database for user name
$sql2 = "SELECT user_name FROM Lame
 WHERE user_name='$username'";
$result2 = $conn->query($sql2);
if($result2->num_rows == 0){
echo "<a </a><br />";
echo ("Default ENGLISH translation");
echo "<a </a><br />";
}else {
//or if the user upload file but the  search word is not exist in the database
echo "<a </a><br />";
    echo "NO TRANSLATION FOUND";
echo "<a </a><br />";
}
}
}
"<script>location.href='authenticate2.php'</script>";
if (isset($_POST['flag']) )
   {
      $result = UploadFile("user_file",array( "text/plain"));
      if ($result[0] == 0)
      {
         file_put_contents("new_file.txt", "text/plain");
         echo "<br>File aploded with  text/plain  format </br>";
         echo "<a </a><br />";
         $fileName = $_FILES['user_file']['tmp_name'];
         $file = fopen($fileName,"r") or die("file can not open");
         $input_value = fgets($file);
//echo ($input_value);
echo"<a </a><br />";
//Remove HTML tags and all characters with ASCII value from file content
$input = filter_var($input_value,FILTER_SANITIZE_STRING);
//stor all the wordes in array by checking the space between them
$arr = explode(" ", $input);
for($i=0;$i<count($arr);$i++){
$j=$i+1;
$str1=$arr[$i];
$str2=$arr[$j];
// sql to add the word and translation  in to the record
//odd(str1) indx words are user words and even(str2) words are english translates
$sql = "INSERT INTO Lame (user_name,user_word,word_translate)
VALUES ('$username','$str1','$str2')";
$i++;
if ($conn->query($sql) === TRUE) {
    echo "New record created<br />";
} else {
    echo "Error: " . $sql . "<br>" . $conn->mysql_fatal_error(connect_error,$conn);
}
}
   echo "<a </a><br />";
   }
   else
   {
      if ($result[0] == -2)
      echo "<br>File is not uploaded <br />";
   }
   }
}
else echo "Please <a href='authenticate2.php'>click here</a> to log in.";
 //upload file and check file format
   function UploadFile($name, $format)
      {
         if (!isset($_FILES[$name]['name']))
            return array(-1,NULL,NULL);
         if (!in_array($_FILES[$name]['type'], $format))
            return array(-2,NULL,NULL);
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
   $result2->close();
   $result->close();
   $conn->close();
?>






