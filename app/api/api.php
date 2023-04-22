<?php
//Ruta api: http://localhost/MVCBiometric/api/xxx
include 'ApiUtils.php';
header('Content-Type: application/json');
$urlComponents = parse_url($_SERVER['REQUEST_URI']);
$route = str_replace('/BiometricPhp', '', $urlComponents['path']);
$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$requestBody = file_get_contents('php://input');
$array_route = explode("/", $route);
$route = implode("/", array_slice($array_route, 0, 3));

switch ($route) {
    case '/api/sse':
        ApiUtils::sse($params, $method);
        break;
    case '/api/save_finger':
        $requestBody = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        ApiUtils::save_finger($requestBody, $method);
        break;
    case '/api/list_finger':
        $requestBody = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        ApiUtils::list_finger($requestBody, $method);
        break;
    case '/api/update_finger':
        $requestBody = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        ApiUtils::update_finger($requestBody, $method);
        break;
    case '/api/sensor_close':
        $requestBody = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        ApiUtils::sensor_close($requestBody, $method);
        break;
    case '/api/sincronizar':
        $requestBody = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        ApiUtils::sincronizar($requestBody, $method);
        break;
    default:
        // Devolver un error 404 si la ruta solicitada no existe
        header("HTTP/1.1 404 Not Found");
        $headers = apache_request_headers();
        $header = str_replace("Basic ", "", $headers["Authorization"]);
        echo json_encode(array("error" => "Ruta no encontrada", "headers" => $header));
        break;
}

