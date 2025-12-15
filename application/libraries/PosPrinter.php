<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/escpos-php/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class PosPrinter {

    private $printer;
    private $connector;

    public function __construct($params = array()) 
    {
        $ip = isset($params['ip']) ? $params['ip'] : "192.168.1.200";
        $port = isset($params['port']) ? $params['port'] : 9100;

        // chỉ mở ONE connection
        $this->connector = new NetworkPrintConnector($ip, $port);
        $this->printer = new Printer($this->connector);
    }

    public function print_text($text)
    {
        try {
            $text_ascii = vn_to_ascii($text); // bỏ dấu
            // $this->printer->setLineSpacing(5);
            $this->printer->text($text_ascii . "\n\n");
            $this->printer->cut();
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }


    public function close()
    {
        if ($this->printer) {
            $this->printer->close();
        }
    }
}
