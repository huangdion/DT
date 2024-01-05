<?php
include("library.php");

function processBiayaRoom($con, $occupied_id) {
	$sqlData = "SELECT a.room_id, a.dari_tanggal, a.sampai_tanggal, a.checkin_time, a.checkout_time, a.rate, a.group_id FROM occupied a WHERE a.occupied_id=:occupied_id And checkout_time is not Null";
	$data=queryArrayValue($con, $sqlData, array("occupied_id"=>$occupied_id));	
	$start = $date = new DateTime($data["dari_tanggal"]);	
	$sqlCheck = "SELECT dk_id FROM DK Where group_id=:group_id and room_id=:room_id and tanggal=:tanggal and jenis='Room';";	
	while ($start <= new DateTime($data["sampai_tanggal"])) {
		$dk_id = querySingleValue($con, $sqlCheck, array("group_id"=>$data["group_id"], "room_id"=>$data["room_id"], "tanggal"=>$start->format("Y-m-d")));
		//Kalau sudah ada
		if ($dk_id) {
			$sqlUpdateDK = "UPDATE DK Set Amount=:Amount WHERE dk_id=:dk_id";
			updateRow($con, $sqlUpdateDK, array("Amount"=>$data["rate"], "dk_id"=>$dk_id));			
		}
		else {
			$sqlInsertDK = "INSERT INTO DK (group_id, room_id, tanggal, jenis, Keterangan, Amount) VALUES(:group_id, :room_id, :tanggal, 'Room', :Keterangan, :Amount);";
			createRow($con, $sqlInsertDK, array("group_id"=>$data["group_id"], "room_id"=>$data["room_id"], "tanggal"=>$start->format("Y-m-d"),
			"Keterangan"=>"Room Charge " . $data["room_id"] . "@" . $start->format("Y-m-d"), "Amount"=>$data["rate"]));			
		}
		$start->modify('+1 day');
	}	
}

function showUI() {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>DataTables Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link href="bootstrap-5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="bootstrap-5.3.2/dist/js/bootstrap.bundle.min.js"></script>	
  <script src="jquery/jquery-3.7.1.min.js"></script>  
  <link href="DataTables/datatables.min.css" rel="stylesheet"> 
  <script src="DataTables/datatables.min.js"></script>
</head>
<body>

<!-- Modal -->
<div class="modal" id="myForm" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myFormTitle">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
	  <input type="hidden" class="form-control" id="occupied_id" name="input-element" value="0">
      <div class="modal-body">
        <div class="mb-3">
           <label for="room_id" class="form-label">room_id</label>
		   <select class="form-select" id="room_id" name="input-element">
           </select>           
        </div>
        <div class="mb-3">
           <label for="guest_id" class="form-label">guest_id</label>
           <select class="form-select" id="guest_id" name="input-element">
           </select>
        </div>		
        <div class="mb-3">
           <label for="voucher_id" class="form-label">voucher_id</label>
           <input type="text" class="form-control" id="voucher_id" placeholder="Isikan voucher_id" name="input-element">
        </div>
		<div class="mb-3">
		   <label for="dari_tanggal" class="form-label">Dari tanggal</label>
		   <input type="date" class="form-control" id="dari_tanggal" placeholder="Isikan Dari tanggal" name="input-element">
        </div>
		<div class="mb-3">
		   <label for="dari_tanggal" class="form-label">Sampai tanggal</label>
		   <input type="date" class="form-control" id="sampai_tanggal" placeholder="Isikan Sampai tanggal" name="input-element">
           </select>
        </div>
		<div class="mb-3">
		   <label for="rate" class="form-label">rate</label>
		   <input type="number" class="form-control" id="rate" placeholder="Isikan rate" name="input-element">
           </select>
        </div>						
		<div class="mb-3">
		   <label for="group_id" class="form-label">group_id</label>
		   <input type="text" class="form-control" id="group_id" placeholder="Isikan Group Id" name="input-element">
           </select>
        </div>
      </div>		
      <div class="modal-footer">
		<p id="feedback"><p>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="save" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
<p>
<button id="add" type="button" class="btn btn-primary btn-sm">Add</button>
</p>
<table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Room ID</th><th>Guest ID</th><th>Nama</th><th>Voucher ID</th><th>Dari Tanggal</th><th>Sampai Tanggal</th><th>Checkin Time</th><th>Checkout Time</th><th>Rate</th><th>Group ID</th><th>Occupied ID</th><th>Action</th>
            </tr>
        </thead>
</table>
</div>
<script>
$(document).ready(function () {
	$.get("SelectOptions.php?flag=room_id",
	    	function(data,status) {	
				for (i=0; i<data.length; i++) {
					$('#room_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
				}	
		}
	);
	$.get("SelectOptions.php?flag=guest_id",
	    	function(data,status) {	
				for (i=0; i<data.length; i++) {
					$('#guest_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
				}	
		}
	);
});
</script>
<script>
var flag="none";

var table = $('#example').DataTable( {
    serverSide: true,
    ajax: {
        url: '?flag=show',
		type: 'POST'
    },
	columns: [
        { data: 'room_id' }, { data: 'guest_id' },  { data: 'Nama' },  { data: 'voucher_id' }, { data: 'dari_tanggal' },  { data: 'sampai_tanggal' }, { data: 'checkin_time' }, { data: 'checkout_time' }, { data: 'rate' }, { data: 'group_id' }, { data: 'occupied_id' },
		{ "orderable": false, "data": null,"defaultContent":
			"<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>&nbsp;<button type=\"button\" class=\"btn btn-dark btn-sm\" id=\"checkin\">Check-In</button>&nbsp;<button type=\"button\" class=\"btn btn-dark btn-sm\" id=\"checkout\">Check-Out</button>"}
    ]
} );

$('#add').click(function() {
	flag="add";
	$('#myFormTitle').text("Add Data");
	$('#room_id').val("");
	$('#guest_id').val("");
	$('#voucher_id').val("");
	$('#dari_tanggal').val(new Date().toISOString().slice(0, 10));
	$('#sampai_tanggal').val("");
	$('#rate').val("0");
	$('#group_id').val("");
	$('#save').text("Save change");
	$('#feedback').text("");
	$('#myForm').modal('show');
});

function postToServer(obj, callBack) {
	$.post("?flag=" + flag,
		JSON.stringify(obj), 
	    	function(data,status) {
				if (data["status"]==1) {
					callBack();
				}
				else {
					$("#feedback").text(data["message"]);
				}
		}
	);
}

//klik pada button save
$('#save').click(function() {
	var formControl = document.getElementsByName("input-element");
	var data = {};
	for (var i=0;i<formControl.length;i++) {
		data[formControl[i].id] = formControl[i].value;
	}
	
	postToServer(data, function() {
		$('#myForm').modal('hide');	
		table.ajax.reload();
	});
});

function readFromServer(obj, callBack) {
	$.post("?flag=read",
		JSON.stringify(obj), 
	    	function(data,status) {
				if (data["status"]==1) {
					callBack(data["data"]);
				}
				else {
					$("#feedback").text(data["message"]);
				}
		}
	);
}

//klik pada button edit
table.on('click', '#edit', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var occupied_id = row['occupied_id'];
	readFromServer({"occupied_id":occupied_id}, function(data) {
		flag="edit";
		$('#myFormTitle').text("Edit Data");
		$('#occupied_id').val(data["occupied_id"]);
		$('#room_id').val(data["room_id"]);
		$('#guest_id').val(data["guest_id"]);
		$('#voucher_id').val(data["voucher_id"]);
		$('#dari_tanggal').val(data["dari_tanggal"]);
		$('#sampai_tanggal').val(data["sampai_tanggal"]);
		$('#rate').val(data["rate"]);
		$('#group_id').val(data["group_id"]);
		$('#save').text("Save change");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});
});

//klik pada button delete
table.on('click', '#delete', function (e) {
	//ambil data dari baris yang diklik
var row = table.row(e.target.closest('tr')).data();
    var occupied_id = row['occupied_id'];
	readFromServer({"occupied_id":occupied_id}, function(data) {
		flag="delete";
		$('#myFormTitle').text("Edit Data");
		$('#occupied_id').val(data["occupied_id"]);
		$('#room_id').val(data["room_id"]);
		$('#guest_id').val(data["guest_id"]);
		$('#voucher_id').val(data["voucher_id"]);
		$('#dari_tanggal').val(data["dari_tanggal"]);
		$('#sampai_tanggal').val(data["sampai_tanggal"]);
		$('#rate').val(data["rate"]);
		$('#group_id').val(data["group_id"]);
		$('#save').text("Delete record");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});	
});

//klik pada button checkin
table.on('click', '#checkin', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var occupied_id = row['occupied_id'];
	var obj = {"occupied_id":occupied_id};
	$.post("?flag=checkin", JSON.stringify(obj), 
	    function(data,status) {
			if (data["status"]==1) {
				table.ajax.reload();
			}
		}
	);
});

//klik pada button checkin
table.on('click', '#checkout', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var occupied_id = row['occupied_id'];
	var obj = {"occupied_id":occupied_id};
	$.post("?flag=checkout", JSON.stringify(obj), 
	    function(data,status) {
			if (data["status"]==1) {
				table.ajax.reload();
			}
		}
	);
});
</script>
</body>
</html>
<?php
}
if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"]=="show") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlCount = "SELECT count(*) FROM occupied a WHERE a.checkin_time is Not Null and a.checkout_time is Null;";

		//untuk mengembalikan data
		$length = intval($_REQUEST["length"]);
		$start = intval($_REQUEST["start"]);
		//Data yang belum checkout atau checkout_time - checkin_time < 1 jam
		$sqlData = "SELECT a.room_id, a.guest_id, b.Nama, a.voucher_id, a.dari_tanggal, a.sampai_tanggal, a.checkin_time, a.checkout_time, a.rate, a.group_id, a.occupied_id FROM occupied a Inner Join Guest b On a.guest_id=b.guest_id WHERE (room_id LIKE :search or Nama Like :search) and (a.checkout_time is Null or HOUR(TIMEDIFF(now(), a.checkout_time)) < 1) LIMIT $length OFFSET $start";
		$data = array();
		$data["draw"]=intval($_REQUEST["draw"]);
		$data["recordsTotal"]=querySingleValue($con, $sqlCount, array());
		$param = array("search"=>$_REQUEST["search"]["value"]."%");
		$data["data"]=queryArrayRowsValues($con, $sqlData, $param);
		$data["recordsFiltered"]=sizeof($data["data"]);
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	}
	else if($_REQUEST["flag"]=="add") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "INSERT into occupied(room_id, guest_id, voucher_id, dari_tanggal, sampai_tanggal, rate, group_id, occupied_id) VALUES (:room_id, :guest_id, :voucher_id, :dari_tanggal, :sampai_tanggal, :rate, Case When :group_id='' Then CONCAT(:guest_id, DATE_FORMAT(:dari_tanggal, '%y%m')) else :group_id end, :occupied_id);";		
			createRow($con, $sql, $data);
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="read") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$param = json_decode($body, true);		
			$sql = "SELECT a.room_id, a.guest_id, b.Nama, a.voucher_id, a.dari_tanggal, a.sampai_tanggal, a.rate, a.group_id, a.occupied_id FROM occupied a Inner Join Guest b On a.guest_id=b.guest_id WHERE occupied_id=:occupied_id;";		
			$data = queryArrayValue($con, $sql, $param);
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="edit") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "UPDATE occupied SET room_id=:room_id, guest_id=:guest_id, voucher_id=:voucher_id, dari_tanggal=:dari_tanggal, sampai_tanggal=:sampai_tanggal, rate=:rate, group_id=:group_id WHERE occupied_id=:occupied_id and checkout_time is Null;";		
			updateRow($con, $sql, $data);
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="delete") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "DELETE FROM occupied WHERE occupied_id=:occupied_id and checkin_time is Null;";
			deleteRow($con, $sql, array("occupied_id"=>$data['occupied_id']));
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}	
	else if($_REQUEST["flag"]=="checkin") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "UPDATE Occupied SET checkin_time=now() WHERE occupied_id=:occupied_id and checkin_time is Null;";
			updateRow($con, $sql, array("occupied_id"=>$data['occupied_id']));
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="checkout") {
    $response=array();
    try {
    $con = openConnection();
    $con->BeginTransaction();
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $sql = "UPDATE Occupied SET checkout_time=now() WHERE checkin_time is not Null and 
    occupied_id=:occupied_id;";
    updateRow($con, $sql, array("occupied_id"=>$data['occupied_id']));
    processBiayaRoom($con, $data['occupied_id']);
    $con->Commit();
    $response["status"]=1;
    $response["message"]="Ok";
    $response["data"]=$data;
    }
    catch(Exception $e) {
    $response["status"]=0;
    $response["message"]=$e->getMessage();
    $response["data"]=null;
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($response);
    }
    
}
else {
	showUI();
}
?>