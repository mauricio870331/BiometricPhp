<?php

session_start();
set_time_limit(0);
date_default_timezone_set("America/Bogota"); //Modificar a la zona hioraria adecuada, solo para guardar la fecha hora en mysql
include_once '../../config/Utils.php';
require_once Utils::get_path() . "/app/Models/User.php";
require_once Utils::get_path() . "/app/Models/Fingerprint.php";
require_once Utils::get_path() . "/app/Models/TempFingerprint.php";

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WebUtils
 *
 * @author Maurcio Herrera
 */
class WebUtils {

    public static function api_users($param, $method) {
        if ($method != "GET") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        header("HTTP/1.1 200 OK");
        $getParams = Utils::getUriParams($param);
        echo json_encode(array("success" => "Todo Ok", "params" => $getParams));
    }

    public static function ssejs($param, $method, $body = []) {
        if ($method != "GET") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        $token = $param['token'];
        $response = array();
        $response["id"] = $token;
        $response["name"] = null;
        $response["image"] = null;
        $response["user_id"] = null;
        $response["option"] = null;
        $TempFingerprint = new TempFingerprint();
        $query = "SELECT id,token_pc,image,updated_at,user_id,name, option FROM temp_fingerprint "
                . " where token_pc = '" . $token . "' and option = 'read' ORDER BY updated_at DESC LIMIT 1";
        $rows = $TempFingerprint->query($query)->get();
        if (count($rows) > 0) {
            $response["id"] = $rows[0]->id;
            $response["name"] = $rows[0]->name;
            $response["user_id"] = $rows[0]->user_id;
            $response["image"] = $rows[0]->image;
            $response["option"] = $rows[0]->option;
        }
        header("HTTP/1.1 200 OK");
        echo 'data: ' . json_encode($response) . "\n\n";
        flush();
        if (!empty($response["image"])) {
            $query = "update temp_fingerprint set image = NULL where token_pc = '" . $token . "'";
            $TempFingerprint->exec($query);
            $response["id"] = $token;
            $response["name"] = null;
            $response["image"] = null;
            $response["user_id"] = null;
            $response["option"] = null;
        }
        $TempFingerprint->desconectar();
    }

    public static function store_enroll($body, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $model = new Model();
        $delete = "delete from  temp_fingerprint where token_pc = '" . $body['token_pc'] . "'";
        $model->exec($delete);
        $insert = "insert into temp_fingerprint (id, finger_name, token_pc, text, option, user_id) "
                . "values ('" . strtotime("now") . "','" . $body['finger_name'] . "','" . $body['token_pc'] . "', "
                . "'El sensor de huella dactilar esta activado', 'enroll','" . $body['user_id'] . "')";
        $row = $model->exec($insert);
        $model->desconectar();
        header("HTTP/1.1 200 OK");
        echo json_encode(array("success" => "Todo Ok", "code" => $row));
    }

    public static function store_read($params, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $temp = new TempFingerprint();
        $temp->delete("token_pc", $params['token_pc']);
        $id = strtotime("now");
        $result = $temp->create(array(
            "id" => $id,
            "option" => "read",
            "token_pc" => $params['token_pc'],
            "created_at" => date("Y-m-d H:i:s")
        ));
        $temp->desconectar();
        $arrayResponse = array("code" => $result, "message" => "Ok");
        echo json_encode($arrayResponse);
    }

    public static function register_users($params, $method, $body) {
        unset($_SESSION["validate"]);
        unset($_SESSION["success"]);
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        if (!Utils::validate($body)) {
            $_SESSION["validate"] = false;
            header("Location: " . Utils::get_root() . "/Views/create.php");
            exit();
        }
        $user = new User();
        $image = null;
        $url = null;
        $rowUser = $user->create($body);
        if (isset($_FILES['image']) && !empty($_FILES['image'])) {
            $archivo = $_FILES['image']["tmp_name"];
            $nombre_archivo = $_FILES['image']["name"];
            $size = $_FILES['image']["size"];
            $ext_pat = pathinfo($nombre_archivo);
            $ext = $ext_pat['extension'];
            $nombreArchivo = basename($_FILES['image']['name']);
            $url = str_replace("\\", "/", Utils::get_root()) . '/public/images/users/' . $rowUser->id . "." . $ext;
            $rutaArchivo = Utils::get_path() . '/public/images/users/' . $rowUser->id . "." . $ext;
            move_uploaded_file($archivo, $rutaArchivo);
            $body["image"] = $url;
        }
        $rowUser = $user->update($body, $rowUser->id);
        $user->desconectar();
        $_SESSION["success"] = "Usuario Creado con exito";
        header("Location: " . Utils::get_root() . "/Views/index.php");
    }

    public static function fingerList($param, $method, $body = []) {
        if ($method != "GET") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $model = new Model();
        $user_id = $param['user'];
        $query = "SELECT * FROM fingerprints WHERE user_id = " . $user_id;
        $finger_list = $model->query($query)->get();
        $model->desconectar();
        echo json_encode(array("success" => "Todo Ok", "User" => $finger_list));
    }

    public static function get_finger($params, $method, $body) {
        if ($method != "GET") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $finger = new Fingerprint();
        $user_id = $params['user_id'];
        $fingerlist = $finger->where("user_id", $user_id)->where("notified", 0)->get();
        if (count($fingerlist) > 0) {
            $finger->update(array("notified" => 1), $fingerlist[0]->id);
        }
        $finger->desconectar();
        echo json_encode($fingerlist);
    }

}
