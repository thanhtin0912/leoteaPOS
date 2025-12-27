<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/escpos-php/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class TestPrinter {

    private $printer;
    private $connector;

    public function __construct($params = array()) 
    {
        // chỉ mở ONE connection
        $this->connector = new WindowsPrintConnector("smb://ADMIN/XP356B");
        $this->printer = new Printer($this->connector);
    }

    public function print_text($text)
    {
        try {
            $text_ascii = vn_to_ascii($text); // bỏ dấu
            $this->printer->text($text_ascii);
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
