
<?php
session_start();
include_once '../config/Utils.php';
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php        
          if(isset($_SESSION["validate"])){
              echo "Algunos campos son requeridos";
          }
        ?>
        <form method="post" action="../web/register_users" enctype="multipart/form-data">
            <label>Name</label>
            <input type="text" name="name" id="name">
            <br><br>
            <label>Email</label>
            <input type="text" name="email" id="email">
            <br><br>
            <label>Image</label>
            <input type="file" name="image" id="image">
            <br><br>
            <hr>
            <input type="submit" value="Save">
        </form>
    </body>
</html>
