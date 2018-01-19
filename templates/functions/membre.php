<?php
if (isset($_SESSION["membre"])) {
    echo "je suis co";
}

else {
    header ('location : login.html');
}
?>