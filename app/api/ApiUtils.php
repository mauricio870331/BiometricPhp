<?php

set_time_limit(0);
include_once '../../config/Utils.php';
require_once Utils::get_path() . "/app/Models/Model.php";
require_once Utils::get_path() . "/app/Models/TempFingerprint.php";
require_once Utils::get_path() . "/app/Models/Fingerprint.php";

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiUtils
 *
 * @author Maurcio Herrera
 */
class ApiUtils {

    public static function sse($param, $method, $body = []) {
        if ($method != "GET") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        $model = new Model();
        $token = $param['token'];
        $array = array('option' => null, 'pc_serial' => $token);
        while (true) {
            $query = "Select created_at, option, updated_at from temp_fingerprint where token_pc = '" . $token . "' and option is not null ORDER BY id DESC LIMIT 1";
            $rs = $model->query($query)->get();
            if (count($rs) > 0) {
                $array['option'] = $rs[0]->option;
                $array['pc_serial'] = $token;
            }
            $response = json_encode($array);        
            echo "{$response}" . "\n\n";
            ob_flush();
            flush();
            sleep(1);
        }
        $model->desconectar();
    }

    public static function save_finger($data, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $temp = new TempFingerprint();
        $temp_result = $temp->where("token_pc", $data["token_pc"])->first();
        $temp->update(["option" => $data["option"]], $temp_result->id);
        $datos = array();
        $dedo = explode("_", $temp_result->finger_name);
        $datos["finger_name"] = $dedo[0] . " " . $dedo[1];
        $datos["image"] = ApiUtils::saveImage($data["image"], $temp_result->user_id . $temp_result->finger_name);
        $datos["fingerprint"] = $data["fingerprint"];
        $datos["user_id"] = $temp_result->user_id;
        $datos["notified"] = 0;
        $fingerprint = new Fingerprint();
        $response = $fingerprint->create($datos);
        echo json_encode(array("response" => $response));
    }

    public static function saveImage($image, $user_id) {
        $image = base64_decode($image);
        $imageName = $user_id . ".png"; //
        $url = str_replace("\\", "/", Utils::get_root()) . '/public/images/fingers/' . $imageName;
        $rutaArchivo = Utils::get_path() . '/public/images/fingers/' . $imageName;
        file_put_contents($rutaArchivo, $image);
        return $url;
    }

    public static function update_finger($data, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $TempFingerprint = new TempFingerprint();
        $response = $TempFingerprint->where("token_pc", $data["token_pc"])->get();
        $response_ = $TempFingerprint->update([
            "fingerprint" => null,
            "image" => $data["image"],
            "user_id" => ((int) $data["user_id"] > 0) ? $data["user_id"] : null,
            "name" => $data["name"],
            "text" => $data["text"]
                ], $response[0]->id);
        $TempFingerprint->desconectar();
        echo json_encode(array("response" => $response_));
    }

    public static function list_finger($data, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $model = new Model();
        $usuarios = array();
        $from = $data["from"];
        $query = "SELECT count(*) total FROM users u INNER JOIN fingerprints f on u.id = f.user_id";
        $rs = $model->query($query)->get();
        $count = $rs[0]->total;
        $query2 = "SELECT u.id, f.fingerprint, u.name "
                . " FROM users u INNER JOIN fingerprints f on u.id = f.user_id "
                . "limit " . $from . ", 10";
        $usuarios = $model->query($query2)->get();
        $model->desconectar();
        $array = array("usuarios" => $usuarios, "total" => $count);
        echo json_encode($array);
    }

    public static function sincronizar($data, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $model = new Model();
        $query = "SELECT u.id user_id, f.fingerprint, f.id finger_id,"
                . " u.name "
                . "FROM users u INNER JOIN fingerprints f on u.id = f.user_id "
                . "WHERE f.id > " . $data["finger_id"];
        $rs = $model->query($query)->get();
        $model->desconectar();
        echo json_encode($rs);
    }

    public static function sensor_close($data, $method) {
        if ($method != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(array("error" => "Método no permitido"));
            exit();
        }
        $TempFingerprint = new TempFingerprint();
        $response = $TempFingerprint->where("token_pc", $data["token_pc"])->first();
        $TempFingerprint->update(["option" => "close"], $response->id);
        $arrayResponse = array("code" => $response, "message" => "Ok");
        $TempFingerprint->desconectar();
        echo json_encode($arrayResponse);
    }

}
