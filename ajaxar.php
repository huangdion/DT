<?php
include("library.php");
$con = openConnection();

// Get total number of rows
$sqlCount = "SELECT count(*) FROM ar;";
$totalRows = querySingleValue($con, $sqlCount, array());

// Get request parameters
$length = intval($_REQUEST["length"]);
$start = intval($_REQUEST["start"]);
$search = $_REQUEST["search"]["value"];

// Construct SQL query
$sqlData = "SELECT ar_id, tanggal, jenis, keterangan, amount, dk_id, payment_id 
             FROM ar 
             WHERE tanggal LIKE :search 
             LIMIT $length OFFSET $start";

// Prepare and execute query
$stmt = $con->prepare($sqlData);
$stmt->bindValue(":search", "%" . $search . "%");
$stmt->execute();

// Fetch data
$data = array();
$data["draw"] = intval($_REQUEST["draw"]); // Unique number
$data["recordsTotal"] = $totalRows; // Total record
$data["recordsFiltered"] = $stmt->rowCount(); // Filtered record
$data["data"] = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $data["data"][] = $row;
}

// Encode data to JSON format and return it
echo json_encode($data);
?>
