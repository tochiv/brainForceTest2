<?php

$host = 'localhost';
$dbname = 'brainjson';
$user = 'root';
$password = 'root';

$db = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $password);

$json = file_get_contents('https://bfdev.ru/test/json.txt');

$array = json_decode($json, true);

$storageArray = $array['NA_SKLADE'];

//Загрузка в базу данных

//foreach ($storageArray as $key => $item) {
//    foreach ($item as $itemInfo) {
//        $statement = $db->prepare('INSERT INTO json_data (code, storage_id, quantity) VALUES (:key, :storage_id, :quantity)');
//
//        $statement->bindParam(':key', $key);
//        $statement->bindParam(':storage_id', $itemInfo['SKLAD_ID']);
//        $statement->bindParam(':quantity', $itemInfo['QUANTITY']);
//
//        $statement->execute();
//    }
//}

$statement = $db->prepare('SELECT id, code, storage_id, quantity FROM json_data');
$statement->execute();

$storageArray = $statement->fetchAll(PDO::FETCH_ASSOC);

$quantityStorageFirst = 0;
$quantityStorageSecond = 0;
$codeArray = [];

foreach ($storageArray as $item) {
    if ($item['storage_id'] == 1 && $item['quantity'] != 0) {
        $code = explode('-', $item['code'])[0] . '-' . explode('-', $item['code'])[1];
        $quantityStorageFirst += $item['quantity'];

        echo 'ID Склада: ' . $item['storage_id'] . ' Код номенклатуры: ' . $code . ' Остаток: ' . $item['quantity'];
        echo '</br>';

        $codeArray[] = [
            $item['code'] => [
                'PRODUCT_CODE' => $code,
                'SKLAD_ID' => $item['storage_id'],
                'QUANTITY' => $item['quantity']
            ]
        ];
    }
    if ($item['storage_id'] == 2 && $item['quantity'] != 0) {
        $quantityStorageSecond += $item['quantity'];
        $code = explode('-', $item['code'])[0] . '-' . explode('-', $item['code'])[1];

        echo 'ID Склада: ' . $item['storage_id'] . ' Код номенклатуры: ' . $code . ' Остаток:' . $item['quantity'];
        echo '</br>';

        $codeArray[] = [
            $item['code'] => [
                'PRODUCT_CODE' => $code,
                'SKLAD_ID' => $item['storage_id'],
                'QUANTITY' => $item['quantity']
            ]
        ];
    }
}

echo '</br>';
echo 'Остаток с первого склада: ' . $quantityStorageFirst;
echo '</br>';
echo 'Остаток со второго склада: ' . $quantityStorageSecond;
echo '</br>';
echo '</br>';

echo json_encode($codeArray);
