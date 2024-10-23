<?php 

$mantenimiento = false;

if($mantenimiento != true){
    header('location:/home/dashboard.php');
}else{
    header('location:/mantenimiento.php');
    exit;
}

?>