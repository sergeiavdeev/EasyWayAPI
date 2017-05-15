<?php
require "EasyWay/API/EWConnector.php";
use EasyWay\API\EWConnector;


$config = include("config.php");

$ew = new EWConnector($config["url"], $config["user"], $config["pass"]);

//Создать заявку
$order = array(
    "id" => "3355665586",
    "locationFrom" => "г Москва, ул Ляпидевского, 18, кв. 1",
    "locationTo" => "Подольск, Рабочая 32/24",
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
    "total" => "300",
    "sender" => array(
        "name" => "ООО \"ОСА индастриз\"",
        "phones" => array(
            "phoneNumber" => "905 5089784",
            "additional" => ""
        )
    ),
    "recipient" => array(
        "name" => "Иванов И.И.",
        "phones" => array(
            "phoneNumber" => "905 5089784",
            "additional" => ""
        )
    ),
    "items" => array()
);

$result = $ew->createOrder($order);

if(!$result["isError"]){
    echo "Заявка создана";    
}
else{
    echo $result["errors"];
}

//Список ПВЗ
$result = $ew->getPickupPoints();

echo $result[0]["city"];

//Запрос статусов
$result = $ew->getStatus(array("27724-YD1846665", "1092105-YD1854018"));

echo $result[0]["status"];
echo $result[1]["status"];

//Предварительный расчет доставки
$result = $ew->getTariff("Москва", "Серпухов", 2, 0.1);

echo $result[0]["total"];
