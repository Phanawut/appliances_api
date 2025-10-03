<?php
// public/index.php

require_once '../src/Response.php';
require_once '../src/ApplianceController.php';

// ตั้งค่า Header และจัดการ CORS Preflight
Response::setHeaders();

$method = $_SERVER['REQUEST_METHOD'];
// ดึง URI path และแยกส่วน
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// กำหนด base path จาก .htaccess
$base_path = '/appliances_api/public'; // หรือ '/appliances_api/public/api' ถ้าใช้ .htaccess อีกแบบ
// ลบ base path ออก และแยก path segments
$path = substr($request_uri, strlen($base_path));
$segments = array_filter(explode('/', $path)); // กรองค่าว่างและแยกส่วน

// URL Structure: /api/appliances[/id]
$resource = isset($segments[1]) ? $segments[1] : null; // 'appliances'
$id = isset($segments[2]) ? (int)$segments[2] : null;  // ID ถ้ามี

$controller = new ApplianceController();

// 3.1 Routing Logic
if ($resource === 'api' && isset($segments[2]) && $segments[2] === 'appliances') {
    $resource_id = isset($segments[3]) ? (int)$segments[3] : null;

    switch ($method) {
        case 'GET':
            if ($resource_id) {
                // GET /api/appliances/{id}
                $controller->show($resource_id);
            } else {
                // GET /api/appliances
                $controller->index();
            }
            break;

        case 'POST':
            // POST /api/appliances
            $controller->store();
            break;

        case 'PUT':
        case 'PATCH':
            if ($resource_id) {
                // PUT/PATCH /api/appliances/{id}
                $controller->update($resource_id);
            } else {
                Response::error("Method Not Allowed", 405);
            }
            break;

        case 'DELETE':
            if ($resource_id) {
                // DELETE /api/appliances/{id}
                $controller->delete($resource_id);
            } else {
                Response::error("Method Not Allowed", 405);
            }
            break;

        default:
            Response::error("Method Not Allowed", 405); // 405 Method Not Allowed
            break;
    }
} else {
    Response::error("Not Found", 404); // 404 Not Found
}
?>