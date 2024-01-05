<?php
include("library.php");

if (isset($_REQUEST["flag"])) {
    $con = openConnection();

    if ($_REQUEST["flag"] == "room_id") {
        $sqlData = "SELECT room_id as `key`, room_id as `value` FROM room;";
    } else if ($_REQUEST["flag"] == "guest_id") {
        $sqlData = "SELECT guest_id as `key`, guest_id as `value` FROM guest;";
    } else if ($_REQUEST["flag"] == "voucher_id") {
        $sqlData = "SELECT voucher_id as `key`, voucher_id as `value` FROM payment;";
    } else if ($_REQUEST["flag"] == "group_id") {
        $sqlData = "SELECT group_id as `key`, group_id as `value` FROM occupied;";
    } else {
        echo json_encode(array(array("key" => "error", "value" => "Unknowned Flag")));
        return;
    }

    $param = array();
    $data = queryArrayRowsValues($con, $sqlData, $param);

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($data);
} else {
    echo json_encode(array(array("key" => "error", "value" => "Invalid Parameter")));
}
