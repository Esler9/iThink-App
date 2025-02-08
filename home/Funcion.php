


<?php
function detectar_dispositivo() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    
    if (strpos($userAgent, 'mobile') !== false) {
        if (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'Tablet';
        }
        return 'Celular';
    }
    return 'PC';
}

$dispositivo = detectar_dispositivo();


function widthdispositivo($dis){
    if ($dis=="Tablet"){
        return "650";
    }elseif($dis=="Celular"){
        return "360";
    }else return "750";
}

function error_loguear(){
    if(isset($_GET["error"])){
        if($_GET['error']==1){
            return true;
        }else return false;
    }
}


?>
