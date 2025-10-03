<?php
// src/ApplianceController.php

require_once 'Database.php';
require_once 'Response.php';

class ApplianceController {
    private $conn;
    private $table_name = "appliances";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    // --- 4.1 GET /api/appliances (อ่าน/ค้นหาทั้งหมด) ---
    public function index() {
        // การจัดการ Query Parameters เช่น category, min_price, max_price, sort, page, per_page
        $where_clauses = [];
        $params = [];
        $query_params = $_GET;

        // ตัวอย่างการกรองตาม category
        if (isset($query_params['category']) && !empty($query_params['category'])) {
            $where_clauses[] = "category = :category";
            $params[':category'] = $query_params['category'];
        }
        
        // ตัวอย่างการกรองตามช่วงราคา
        if (isset($query_params['min_price']) && is_numeric($query_params['min_price'])) {
            $where_clauses[] = "price >= :min_price";
            $params[':min_price'] = $query_params['min_price'];
        }
        if (isset($query_params['max_price']) && is_numeric($query_params['max_price'])) {
            $where_clauses[] = "price <= :max_price";
            $params[':max_price'] = $query_params['max_price'];
        }

        $where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";

        // การจัดการ Pagination และ Sorting
        $page = isset($query_params['page']) && is_numeric($query_params['page']) ? (int)$query_params['page'] : 1;
        $per_page = isset($query_params['per_page']) && is_numeric($query_params['per_page']) ? (int)$query_params['per_page'] : 10;
        $offset = ($page - 1) * $per_page;
        $sort = isset($query_params['sort']) ? $query_params['sort'] : 'id_asc';

        $order_by = "id ASC";
        if ($sort == 'price_desc') {
            $order_by = "price DESC";
        } elseif ($sort == 'price_asc') {
            $order_by = "price ASC";
        }
        // ... เพิ่มเงื่อนไข sort อื่นๆ ตามต้องการ

        // ดึงข้อมูลทั้งหมด
        $query = "SELECT * FROM " . $this->table_name . $where_sql . " ORDER BY " . $order_by . " LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $appliances = $stmt->fetchAll();
            
            // ในการใช้งานจริง ควรมีการหา Total Count ด้วย
            
            Response::json(['data' => $appliances], 200); // 200 OK

        } catch (PDOException $e) {
            // ในงานจริง ควร log ข้อผิดพลาด
            Response::error("Internal Server Error", 500);
        }
    }

    // --- 4.1 GET /api/appliances/{id} (อ่านรายการเดียว) ---
    public function show($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $appliance = $stmt->fetch();

            if ($appliance) {
                Response::success("Success", $appliance, 200); // 200 OK
            } else {
                Response::error("Not found", 404); // 404 Not Found
            }
        } catch (PDOException $e) {
            Response::error("Internal Server Error", 500);
        }
    }

    // --- 4.2 POST /api/appliances (สร้างสินค้าใหม่) ---
    public function store() {
        // รับข้อมูล JSON จาก Request Body
        $data = json_decode(file_get_contents("php://input"), true); 

        if (empty($data)) {
            Response::error("Bad Request: No JSON data provided", 400); // 400 Bad Request
        }

        // 1. Validation (ตรวจสอบความถูกต้องของข้อมูล)
        $required_fields = ['sku', 'name', 'brand', 'category', 'price', 'stock', 'warranty_months'];
        $validation_errors = [];

        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field]) && $data[$field] !== 0) {
                $validation_errors[$field] = "is required";
            }
        }
        if (isset($data['price']) && $data['price'] < 0) {
            $validation_errors['price'] = "must be >= 0";
        }
        // ... เพิ่มการตรวจสอบความถูกต้องของข้อมูลอื่นๆ เช่น stock, energy_rating
        
        if (!empty($validation_errors)) {
            Response::error("Validation failed", 400, $validation_errors); // 400 Bad Request
        }
        
        // 2. ตรวจสอบ SKU ซ้ำ
        $check_sku_query = "SELECT id FROM " . $this->table_name . " WHERE sku = :sku LIMIT 1";
        try {
            $stmt_check = $this->conn->prepare($check_sku_query);
            $stmt_check->bindParam(':sku', $data['sku']);
            $stmt_check->execute();
            if ($stmt_check->rowCount() > 0) {
                Response::error("SKU already exists", 409); // 409 Conflict
            }
        } catch (PDOException $e) {
            Response::error("Internal Server Error", 500);
        }

        // 3. INSERT ข้อมูล
        $query = "INSERT INTO " . $this->table_name . " (sku, name, brand, category, price, stock, warranty_months, energy_rating) VALUES (:sku, :name, :brand, :category, :price, :stock, :warranty_months, :energy_rating)";

        try {
            $stmt = $this->conn->prepare($query);
            // Binding parameters
            $stmt->bindParam(':sku', $data['sku']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':brand', $data['brand']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':stock', $data['stock']);
            $stmt->bindParam(':warranty_months', $data['warranty_months']);
            // energy_rating เป็น optional
            $energy_rating = isset($data['energy_rating']) ? $data['energy_rating'] : null;
            $stmt->bindParam(':energy_rating', $energy_rating);
            
            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
                // ดึงข้อมูลสินค้าที่เพิ่งสร้างมาเพื่อส่งกลับ
                $new_appliance = $this->getApplianceById($last_id); 
                Response::success("Created", $new_appliance, 201); // 201 Created
            } else {
                Response::error("Could not create appliance", 500);
            }

        } catch (PDOException $e) {
            // ในกรณีที่เกิดข้อผิดพลาดอื่นๆ (เช่น การตั้งค่า DB)
            Response::error("Database Error: " . $e->getMessage(), 500);
        }
    }

    // --- 4.3 PUT/PATCH /api/appliances/{id} (แก้ไข) ---
    public function update($id) {
        // ... โค้ดสำหรับ Update (คล้ายกับ store แต่มี WHERE id และใช้ PUT/PATCH data)
        // ต้องตรวจสอบ 404 Not Found, 400 Bad Request, 409 Conflict (sku ซ้ำกับรายการอื่นที่ไม่ใช่ตัวเอง)
        // Response 200 OK
    }

    // --- 4.4 DELETE /api/appliances/{id} (ลบ) ---
    public function delete($id) {
        // ... โค้ดสำหรับ Delete
        // ต้องตรวจสอบ 404 Not Found
        // Response 200 OK
    }
    
    // ตัวช่วย: ดึงข้อมูลสินค้าจาก ID เพื่อใช้ใน store/update response
    private function getApplianceById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>