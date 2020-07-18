
<?php
session_start();

if (isset($_SESSION['username'])){
destroy_session_and_data();
echo "<script>location.href='authenticate2.php'</script>";
}
else
{
     "<script>location.href='authenticate2.php'</script>";
}

function destroy_session_and_data() {
$_SESSION = array();
setcookie(session_name(), '', time() - 2592000, '/');
session_destroy();
}

?>
