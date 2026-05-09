<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discord {
    public function sendsms($text){
        $webhook_url = "https://discordapp.com/api/webhooks/1470706833671847956/wV_Qd3_e2UDEaNkOCS8pBRDttH_4SsloMcQ5axGxnfV952FZcb-42mb9MrId-_ne4_zD";
		
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
            echo "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response";
        }
    }

    public function sendsmsCancel($text){
        $webhook_url = "https://discordapp.com/api/webhooks/1470707879878201544/SAcPyYGH9UM_M8pI80_TUOSrIYDAcXaMSv3QQJH0kK8ZJpIx_diK7HJwzs997fFZjDr4";
		
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
            echo "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response";
        }
    }

}
