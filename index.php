<?php

error_reporting(E_ERROR | E_PARSE);

require_once('./vendor/autoload.php');

use QrCode\QrWriter;
use Endroid\QrCode\QrCode;

$qrCode = new QrCode('https://ficohsa.pixelpay.app/pixel-pruebas/LtCZnnMjE3NTE=/checkout?no_redirect');
$writer = new QrWriter(QrWriter::FRAME_ES);
$qrCode->setWriter($writer);

$qrCode->setMargin(30);
$qrCode->setSize(500);

echo $qrCode->writeString();