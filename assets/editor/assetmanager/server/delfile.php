<?php

$root = str_replace('\\', '/',substr(__FILE__,0,-46));
$file = $root . str_replace('../../..','',$_POST["file"]);

if(file_exists ($file)) {
	unlink($file);
} else {

}

echo $_POST["file"];

?>