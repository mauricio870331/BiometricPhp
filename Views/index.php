<?php
session_start();
include_once dirname(__FILE__) . '..\\..\app\\Models\\User.php';
$User = new User();
$usuaios = $User->all();
//print_r($usuaios);
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
        <title>Lista de usuarios</title>
    </head>
    <body>
        <h2>Lista de Usuarios</h2>  
        <?php
        if (isset($_SESSION["success"])) {
            echo "<h3>" . $_SESSION["success"] . "</h3>";
            unset($_SESSION["success"]);
        }
        ?>
        <a href="create.php">Nuevo Usuario</a>
        <br>
        <a  href="javascript:void(0)" class="create_token">Create Token</a>
        <br>
        <a  href="verify-users.php" class="create_token">Check Users</a>
        <br> <br>
        <table border="1">
            <thead>
            <th>Id</th>
            <th>Documento</th> 
            <th>Nombre</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </thead>
        <tbody>
            <?php foreach ($usuaios as $user) { ?>
                <tr>
                    <td><?php echo $user->id; ?></td>
                    <td><?php echo $user->name; ?></td>
                    <td><?php echo $user->email; ?></td>  
                    <td style="width: 50px;"><img style="width: 100%;" src="<?php echo $user->image; ?>"></td>  
                    <td>
                        <a href="finger-list.php/<?php echo $user->id; ?>">Agregar Huellas</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script src="../public/js/jquery-1.7.2.min.js"></script>
    <script src="../public/js/SweetAlert2.js"></script>
    <script src="../public/js/funciones.js"></script>
</body>
</html>
