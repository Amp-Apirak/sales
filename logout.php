<!-- sweetalert -->
<?php
echo '
<script src="../ino/code/plugins/jquery/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"></script>
';
?>

<?php
session_start();
session_unset();
session_destroy();
// <!-- sweetalert -->
echo '<script>
setTimeout(function(){
    swal({
        title: "Logout Successfully!",
        text: "Thank YOU. ",
        type:"success"
    }, function(){
        window.location = "login.php";
    })
},1000);
</script>';
exit;
?>