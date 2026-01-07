<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discord {
    public function sendsms($text){
        $webhook_url = "https://discord.com/api/webhooks/1458312737451937854/bh4dM2wPR3KtQw7MMqZoDNTrG7JnnY1Sx457Qo5HnkvgA25EQznE9YNSogMLsbe-jYeU";
		
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
