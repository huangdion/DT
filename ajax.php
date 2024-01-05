<?php
include("library.php");
$con = openConnection();
//untuk hitung total baris
$sqlCount = "SELECT count(*) FROM rate;";
//untuk mengembalikan data
$length = intval($_REQUEST["length"]);
$start = intval($_REQUEST["start"]);
$sqlData = "SELECT jenis_kamar, rate FROM rate WHERE jenis_kamar LIKE :search LIMIT $length OFFSET $start";
$data = array();
$data["draw"] = intval($_REQUEST["draw"]); //unik number
$data["recordsTotal"] = querySingleValue($con, $sqlCount, array()); //total record
$param = array("search" => $_REQUEST["search"]["value"] . "%%");
$data["data"] = queryArrayRowsValues($con, $sqlData, $param);
$data["recordsFiltered"] = sizeof($data["data"]); //total record terfilter
echo json_encode($data); //konversi ke JSON
?>