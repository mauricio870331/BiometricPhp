<?php
session_start();
include_once dirname(__FILE__) . '..\\..\config\Utils.php';
include_once dirname(__FILE__) . '..\\..\app\\Models\\User.php';

$user_id = Utils::get_rootParams(1);

$user = new User();
$user_data = $user->find($user_id);

$query = "SELECT * FROM fingerprints WHERE user_id = " . $user_id;
$finger_list = $user->query($query)->get();
$user->desconectar();
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
        <title>Lista de Huellas</title>
    </head>
    <body>
        <h3>User Fingerprint List : <?= $user_data->name; ?></h3>
        <button style="margin-bottom: 1%;" class="add_finger"  data-id="<?= $user_data->id ?>">Add Fingerprint</button>
        <table border="1">
            <tr>
                <th>id</th>
                <th>fingerprint name</th>
                <th>fingerprint image</th>
                <th></th>
            </tr>
            <tbody>
                <?php
                foreach ($finger_list as $finger) {
                    ?>
                    <tr>
                        <td><?= $finger->id; ?></td>
                        <td><?= $finger->finger_name ?></td>
                        <td style="text-align: center">
                            <img  style="width: 30px;" src="<?= $finger->image ?>"/>
                        </td>
                        <td>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <script src="<?= Utils::get_root() ?>../public/js/jquery-1.7.2.min.js"></script>
        <script src="<?= Utils::get_root() ?>../public/js/SweetAlert2.js"></script>
        <script src="<?= Utils::get_root() ?>../public/js/funciones.js"></script>
    </body>
</html>
