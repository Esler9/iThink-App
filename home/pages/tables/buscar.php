<?php
if (isset($_GET['search'])) {
    $search = urlencode(trim($_GET['search']));
    header("Location: /home/pages/liberaciones/buscar?search=" . $search);
} else {
    header("Location: /home/pages/liberaciones/buscar");
}
exit();
?>