<?php
   session_start();
   if($_SESSION["autoriser"]!="oui"){
      header("location:login.php");
      exit();
   }
      $bienvenue="Bonjour ".
      $_SESSION["pseudo"];
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <style>
         *{
            font-family:arial;
         }
         body{
            margin:20px;
         }
         a{
            color:#EE6600;
            text-decoration:none;
         }
         a:hover{
            text-decoration:underline;
         }
      </style>
   </head>
   <body onLoad="document.fo.login.focus()">
      <h1><?php echo $bienvenue?></h1>
      <table>
         <thead>
            <th>Vos scores </th>
         </thead>
         <tbody>
            <?php 
               include("connexion.php");
               $sel=$pdo->prepare("select score, date from score where id=? order by score desc");
               $sel->execute(array($_SESSION["id"]));
               $tab=$sel->fetchAll();
               foreach($tab as $score => $date){
                  $i=0;
                  echo "<tr>";
                  foreach($date as $clef => $valeur){
                     if($i%2){
                        echo "<td>".$valeur." "."</td>";
                     }
                     $i = $i+1;
                  }
                  echo "</tr>";
               }

            ?>
         </tbody>
      </table>
      <a href="jeu.php">jouer</a>
      [ <a href="deconnexion.php">Se déconnecter</a> ]
   </body>
</html> 