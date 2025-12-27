<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/escpos-php/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class PosPrinter {

    private $printer;
    private $connector;
    private $width = 576; // pixel width of printable area
    private $fontFile   = APPPATH . 'fonts/NotoSans-Regular.ttf';
    private $fontBold   = APPPATH . 'fonts/NotoSans-BlackItalic.ttf';
    private $fontSize   = 16;
    private $padding    = 15;
    private $lineHeight = 28; // khoảng cách dòng

    public function __construct($params = array()) 
    {
        $ip = isset($params['ip']) ? $params['ip'] : "192.168.1.200";
        $port = isset($params['port']) ? $params['port'] : 9100;
        // chỉ mở khi in connection
        $this->connector = new NetworkPrintConnector($ip, $port);
        $this->printer = new Printer($this->connector);
    }


    public function print(array $receipt)
    {
        try {
            $result = $this->renderReceiptToImage($receipt);

            // ====== DÙNG ĐỂ IN ======
            $img = EscposImage::load($result['path'], false);
            $this->printer->bitImage($img);
            $this->printer->feed(3);
            $this->printer->cut();
            // unlink($result['path']);

        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public function renderReceiptToImage(array $receipt)
    {
        // 1️⃣ Canvas lớn (cắt sau)
        $canvasHeight = 3000;

        $im = imagecreatetruecolor($this->width, $canvasHeight);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, $this->width, $canvasHeight, $white);

        // 2️⃣ Render nội dung
        $y = 25;
        foreach ($receipt as $row) {
            switch ($row['type']) {
                case 'center':
                    $y = $this->drawCenter($im, $y, $row['text'], $row['size'] ?? 18);
                    break;
                case 'line':
                    $y = $this->drawLine($im, $y);
                    break;
                case '2col':
                    $y = $this->drawTwoCols($im, $y, $row['a'], $row['b']);
                    break;
                case '3col':
                    $y = $this->drawThreeCols($im, $y, $row['a'], $row['b'], $row['c'], $row['indent'] ?? 0, $row['bold'] ?? false);
                    break;
            }
        }

        // 3️⃣ Cắt ảnh theo nội dung
        $finalHeight = $y + 10;
        $final = imagecreatetruecolor($this->width, $finalHeight);
        imagecopy($final, $im, 0, 0, 0, 0, $this->width, $finalHeight);
        imagedestroy($im);

        // 4️⃣ Lưu PNG
        $baseDir = FCPATH . 'receipts/';
        
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }
        $filename = 'receipt_' . date('Ymd_His') . '_' . uniqid() . '.png';
        $path = $baseDir . $filename;
        imagepng($final, $path);
        imagedestroy($final);

        return [
            'path' => $path,
            'url'  => rtrim((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'], '/') . "/receipts/$filename",
        ];
    }

    /* ==========================================================
     * DRAW FUNCTIONS
     * ========================================================== */

    private function drawCenter($im, $y, $text, $size)
    {
        $black = imagecolorallocate($im, 0, 0, 0);
        $fontSize = $size;
        $box = imagettfbbox($fontSize, 0, $this->fontFile, $text);
        $textWidth = abs($box[2] - $box[0]);
        $x = ($this->width - $textWidth) / 2;
    
        imagettftext(
            $im,
            $fontSize,
            0,
            $x,
            $y,
            $black,
            $this->fontFile,
            $text
        );
    
        return $y + ($fontSize ? $this->lineHeight + 6 : $this->lineHeight);
    }

    private function drawLine($im, $y, $style = 'solid')
    {
        $black = imagecolorallocate($im, 0, 0, 0);

        // Đẩy line lên gần top dòng (an toàn)
        $lineY = $y - 0;
    
        if ($style === 'dotted') {
            $step = 6;
            $dash = 3;
            for ($x = $this->padding; $x < $this->width - $this->padding; $x += $step) {
                imageline(
                    $im,
                    $x,
                    $lineY,
                    min($x + $dash, $this->width - $this->padding),
                    $lineY,
                    $black
                );
            }
        } else {
            imageline(
                $im,
                $this->padding,
                $lineY,
                $this->width - $this->padding,
                $lineY,
                $black
            );
        }
    
        // Nhảy đúng 1 dòng
        return $y + $this->lineHeight;
    }

    private function drawTwoCols($im, $y, $left, $right)
    {
        $black = imagecolorallocate($im, 0, 0, 0);

        // Left
        imagettftext(
            $im,
            $this->fontSize,
            0,
            $this->padding,
            $y,
            $black,
            $this->fontFile,
            $left
        );

        // Right (canh phải)
        $box = imagettfbbox($this->fontSize, 0, $this->fontFile, $right);
        $textWidth = abs($box[2] - $box[0]);
        $x = $this->width - $this->padding - $textWidth;

        imagettftext(
            $im,
            $this->fontSize,
            0,
            $x,
            $y,
            $black,
            $this->fontFile,
            $right
        );

        return $y + $this->lineHeight;
    }

    private function drawThreeCols($im, $y, $a, $b, $c, $indent = 0, $bold)
    {
        $black = imagecolorallocate($im, 0, 0, 0);
        $font = $bold ? $this->fontBold : $this->fontFile;

        $colA = (int)($this->width * 0.65);
        $colB = (int)($this->width * 0.10);
        $colC = (int)($this->width * 0.25);

        // A – tên món (wrap)
        $lines = $this->wrapTextByPixel(
            $a,
            $this->fontFile,
            $this->fontSize,
            $colA - $this->padding
        );

        $startY = $y;
        foreach ($lines as $line) {
            imagettftext($im, $this->fontSize, 0,
                $this->padding + $indent,
                $y,
                $black,
                $font,
                $line
            );
            $y += $this->lineHeight;
        }

        // B – số lượng (center)
        $bx = $colA + ($colB / 2);
        $box = imagettfbbox($this->fontSize, 0, $this->fontFile, $b);
        $bw = abs($box[2] - $box[0]);

        imagettftext(
            $im,
            $this->fontSize,
            0,
            $bx - ($bw / 2),
            $startY,
            $black,
            $this->fontFile,
            $b
        );

        // C – giá (right)
        $box = imagettfbbox($this->fontSize, 0, $this->fontFile, $c);
        $cw = abs($box[2] - $box[0]);
        $cx = $this->width - $this->padding - $cw;

        imagettftext(
            $im,
            $this->fontSize,
            0,
            $cx,
            $startY,
            $black,
            $this->fontFile,
            $c
        );

        return max($y, $startY + $this->lineHeight);
    }

    /* ==========================================================
     * TEXT WRAP
     * ========================================================== */
    private function wrapTextByPixel($text, $fontFile, $fontSize, $maxWidth)
    {
        $lines = [];
        $words = preg_split('/\s+/', trim($text));
        $line = '';

        foreach ($words as $word) {
            $test = $line === '' ? $word : "$line $word";
            $box  = imagettfbbox($fontSize, 0, $fontFile, $test);
            $w    = abs($box[2] - $box[0]);

            if ($w > $maxWidth) {
                if ($line !== '') {
                    $lines[] = $line;
                }
                $line = $word;
            } else {
                $line = $test;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return $lines;
    }


    public function close()
    {
        if ($this->printer) {
            $this->printer->close();
        }
    }
}
