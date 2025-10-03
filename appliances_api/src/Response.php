<?php
// src/Response.php

class Response {
    public static function setHeaders() {
        // อนุญาตให้เข้าถึง API จากทุกโดเมน (CORS)
        header("Access-Control-Allow-Origin: *"); 
        // กำหนด method ที่อนุญาต
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        // กำหนด header ที่อนุญาตให้ใช้ในการร้องขอ
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        // กำหนด Content-Type ของ Response เป็น JSON 
        header("Content-Type: application/json; charset=utf-8");

        // ถ้าเป็นการร้องขอ OPTIONS (preflight request ของ CORS) ให้จบการทำงาน
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    public static function json($data, $status_code = 200) {
        http_response_code($status_code);
        echo json_encode($data);
        exit();
    }

    // Response มาตรฐานสำหรับ Success/Created
    public static function success($message = "Success", $data = null, $status_code = 200) {
        self::json(['message' => $message, 'data' => $data], $status_code);
    }
    
    // Response สำหรับ Error/Not Found/Bad Request
    public static function error($message, $status_code, $details = null) {
        $response = ['error' => $message];
        if ($details) {
            $response['details'] = $details;
        }
        self::json($response, $status_code);
    }
}
?>