<?php
require "EasyWay/API/EWConnector.php";
use EasyWay\API\EWConnector;

//error_reporting(0);

$config = include("config.php");

$ew = new EWConnector($config["url"], $config["user"], $config["pass"]);

//$result = $ew->getTariff("Москва", "Подольск", 12, 0.01);
//$result = $ew->getPickupPoints();

$order = array(
    "id" => "3355665585",
    "locationFrom" => "Москва",
    "locationTo" => "Подольск",
    "weight" => 10,
    "length" => 50,
    "width" => 60,
    "height" => 70,
    "cargoType" => "1",
    "cargoCost" => "300",
    "assessedCost" => "300",
    "paymentMethod" => "1",
    "tariff" => "",
    "deliveryType" => "0",
    "total" => "300"
);

$result = $ew->createOrder($order);

header("Content-Type: application/xml; charset=utf-8");
exit($result);