<?php
   try{
      $pdo=new PDO("mysql:host=localhost;dbname=ex_score","root","");
   }
   catch(PDOException $e){
      echo $e->getMessage();
   }
?> 