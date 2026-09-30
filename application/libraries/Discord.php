<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function load_env($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Bỏ qua dòng trống hoặc dòng comment (bắt đầu bằng #)
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Tách khóa và giá trị theo dấu '='
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Bỏ dấu ngoặc kép hoặc ngoặc đơn ở hai đầu giá trị (nếu có)
            $value = trim($value, '"\'');

            // Gán biến vào môi trường
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Gọi hàm nạp file .env ở thư mục gốc
load_env(FCPATH . '.env');   

class Discord {
    
    public function sendsms($text){
        // $webhook_url = getenv('DIS_SMS');
        
        $webhook_url = getenv('DIS_LINK');
		
        $data = [
            "username" => "Report Bot",
            "content"  => $text
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Khởi tạo cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Kiểm tra kết quả
        if ($http_status == 204) {
            return true;
        } else {
            log_message('error', "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response");
        }
    }

    public function sendsmsCancel($text){
        // $webhook_url = getenv('DIS_CANCEL');
        $webhook_url = getenv('DIS_LINK');
		
        $data = [
            "username" => "Report Bot",
            "content"  => $text
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Khởi tạo cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Kiểm tra kết quả
        if ($http_status == 204) {
            return true;
        } else {
            log_message('error', "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response");
        }
    }

    public function sendLinkReport($text){
        $webhook_url = getenv('DIS_LINK');
        $data = [
            "username" => "Report Bot",
            "content"  => $text
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Khởi tạo cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Kiểm tra kết quả
        if ($http_status == 204) {
            return true;
        } else {
            log_message('error', "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response");
        }
    }

    public function sendDiffSizeinShift($text){
        // $webhook_url = getenv('DIS_ALERT');
        $webhook_url = getenv('DIS_LINK');
		
        $data = [
            "username" => "Report Bot",
            "content"  => $text
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Khởi tạo cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Kiểm tra kết quả
        if ($http_status == 204) {
            return true;
        } else {
            log_message('error', "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response");
        }
    }

}
