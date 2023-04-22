<?php 
include 'WebUtils.php';
header('Content-Type: application/json');
$urlComponents = parse_url($_SERVER['REQUEST_URI']);
$route = str_replace('/BiometricPhp', '', $urlComponents['path']);
$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$requestBody = file_get_contents('php://input');
$array_route = explode("/", $route);
$route = implode("/", array_slice($array_route, 0, 3));

switch ($route) {
    case '/web/ssejs':   
        WebUtils::ssejs($params, $method);
        break;
    case '/web/active_sensor_enroll':
        $data = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        WebUtils::store_enroll($data, $method);
        break;
    case '/web/active_sensor_read':
        $data = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        WebUtils::store_read($data, $method);
        break;
    case '/web/register_users':
        $body = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        WebUtils::register_users($params, $method, $body);
        break;
    case '/web/get-finger':
        $requestBody = file_get_contents('php://input');
        $body = (json_decode($requestBody, true) != null) ? json_decode($requestBody, true) : $_POST;
        WebUtils::get_finger($params, $method, $body);
        break;
    default:
        // Devolver un error 404 si la ruta solicitada no existe
        header("HTTP/1.1 404 Not Found");
        echo json_encode(array("error" => "Ruta no encontrada"));
        break;
}

