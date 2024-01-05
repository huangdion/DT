<?php
include("library.php");

if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"] == "jenis_kamar") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT jenis_kamar as `key`, jenis_kamar as `value` FROM rate;";
		$param = array();
		$data = queryArrayRowsValues($con, $sqlData, $param);

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	} else if ($_REQUEST["flag"] == "room_id") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT room_id as `key`, concat(room_id, '-', jenis_kamar, '(', CASE WHEN status_kamar=0 Then 'Aktif' Else 'Non-Aktif' END,')->') as `value` FROM room Order By room_id;";
		$param = array();
		$data = queryArrayRowsValues($con, $sqlData, $param);

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	} else if ($_REQUEST["flag"] == "guest_id") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT guest_id as `key`, concat(guest_id, '-', nama, '(', jenis_id,' ', nomor_id, ')->', contact_number) as `value` FROM Guest Order By guest_id;";
		$param = array();
		$data = queryArrayRowsValues($con, $sqlData, $param);

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	} else if ($_REQUEST["flag"] == "group_id") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT group_id as `key`, group_id as `value` FROM occupied;";
		$param = array();
		$data = queryArrayRowsValues($con, $sqlData, $param);

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	} else {
		echo json_encode(array(array("key" => "error", "value" => "Invalid Parameter")));
	}
}