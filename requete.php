<?php 
    session_start();
    include("connexion.php");
    $sel=$pdo->prepare("insert into score (score, date, id) value (?,now(),?)");
    $sel->execute(array($_GET["s"],$_SESSION["id"]));
    $tab=$sel->fetchAll();
      //echo $_GET["s"];
?>