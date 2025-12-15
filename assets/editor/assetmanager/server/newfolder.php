<?php

$root = str_replace('\\', '/',substr(__FILE__,0,-48));
$newfolder = $root . $_POST["folder"];

$parent = dirname($newfolder);

if(!is_writable($parent)) {
	echo "Write permission required";
	exit();
}

if(!file_exists ($newfolder)) {
	//create the folder
	mkdir($newfolder);
} else {
	echo "Folder already exists.";
}
?>