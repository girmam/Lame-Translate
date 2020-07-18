<?php
require_once "login.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    mysql_fatal_error(connect_error,$conn);
}

$username;
$password;


if(isset($_POST['submit'])){
//fix the user input
$username =mysql_entities_fix_string($conn,$_POST['username']);
$password =mysql_entities_fix_string($conn,$_POST['password']);
//echo "Hello, ".$username." ".$password.".<br />";
$salt1= rand(0,1000);
$salt2 = rand(0,1000);
$token = hash('ripemd128', "$salt1$password$salt2");
$sql = "INSERT INTO authenticate (username,token,salt1,salt2)
VALUES('$username','$token','$salt1','$salt2')";
if ($conn->query($sql) === TRUE) {
    echo "sigened up successfully";
} else {
    echo "user name is used";
}

}

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

$conn->close();

?>
<html>
<head>
<title>Personal INFO</title>
</head>
<script>
function validate(form) {
fail = validateUsername(form.username.value)
fail += validatePassword(form.password.value)
if (fail == "") return true
else {
alert(fail);
 return false
 }
}

function validateUsername(field)
{
if (field == "") return "No Username was entered.\n"
else if (field.length < 5)
return "Usernames must be at least 5 characters.\n"
else if (/[^a-zA-Z0-9_-]/.test(field))
return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n"
return ""
}

function validatePassword(field)
{
if (field == "") return "No Password was entered.\n"
else if (field.length < 6)
return "Passwords must be at least 6 characters.\n"
else if (!/[a-z]/.test(field) || ! /[A-Z]/.test(field) ||!/[0-9]/.test(field))
return "Passwords require one each of a-z, A-Z and 0-9.\n"
return ""
}

</script>
<body>
<form method="post" action="<?php echo $PHP_SELF;?>" onSubmit="return validate(this)">
<tr><td>Username</td>
<td><input type="text" maxlength="16" name="username" ></td></tr>
<tr><td>Password</td>
<td><input type="text" maxlength="12" name="password" ></td></tr>

<tr><td colspan="2" align="center"><input type="submit"  name="submit"
value="Signup"></td></tr>
<br><a href='authenticate2.php'><input type=button value=login name=login></a></p>
</form>
<?

