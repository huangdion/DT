<?php
include("library.php");

function showUI()
{
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
					<div class="modal-body">
						<div class="mb-3">
							<label for="room_id" class="form-label">room_id</label>
							<input type="text" class="form-control" id="room_id" placeholder="Isikan Nomor Kamar" name="input-element">
						</div>
						<div class="mb-3">
							<label for="lantai" class="form-label">lantai</label>
							<input type="number" min="1" max="10" class="form-control" id="lantai" placeholder="Isikan Nomor lantai" name="input-element">
						</div>
						<div class="mb-3">
							<label for="jenis_kamar" class="form-label">jenis_kamar</label>
							<select class="form-select" id="jenis_kamar" name="input-element">
							</select>
						</div>
						<div class="mb-3">
							<input class="form-check-input" type="checkbox" value="" id="status_kamar" name="input-element">
							<label for="status_kamar" class="form-check-label">
								Aktif
							</label>
						</div>
						<div class="mb-3">
							<label for="keterangan" class="form-label">keterangan</label>
							<textarea class="form-control" id="keterangan" rows="3" name="input-element"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<p id="feedback">
						<p>
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
						<th>room_id</th>
						<th>lantai</th>
						<th>jenis_kamar</th>
						<th>status_kamar</th>
						<th>keterangan</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
		</div>
		<script>
			$(document).ready(function() {
				$.get("SelectOptions.php?flag=jenis_kamar",
					function(data, status_kamar) {
						for (i = 0; i <= data.length; i++) {
							$('#jenis_kamar').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
						}
					}
				);
			});
		</script>
		<script>
			var flag = "none";

			var table = $('#example').DataTable({
				serverSide: true,
				ajax: {
					url: '?flag=show',
					type: 'POST'
				},
				columns: [{
						data: 'room_id'
					}, {
						data: 'lantai'
					}, {
						data: 'jenis_kamar'
					}, {
						data: 'status_kamar'
					}, {
						data: 'keterangan'
					},
					{
						"orderable": false,
						"data": null,
						"defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>"
					}
				]
			});

			$('#add').click(function() {
				flag = "add";
				$('#myFormTitle').text("Add Data");
				$('#room_id').val("");
				$('#room_id').prop("disabled", false);
				$('#lantai').val("");
				$('#jenis_kamar').val("");
				$('#status_kamar').val("0");
				$('#keterangan').val("");
				$('#feedback').text("");
				$('#myForm').modal('show');
			});

			function postToServer(obj, callBack) {
				$.post("?flag=" + flag,
					JSON.stringify(obj),
					function(data, status_kamar) {
						if (data["status_kamar"] == 1) {
							callBack();
						} else {
							$("#feedback").text(data["message"]);
						}
					}
				);
			}

			//klik pada button save
			$('#save').click(function() {
				var formControl = document.getElementsByName("input-element");
				var data = {};
				for (var i = 0; i < formControl.length; i++) {
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
					function(data, status_kamar) {
						if (data["status_kamar"] == 1) {
							callBack(data["data"]);
						} else {
							$("#feedback").text(data["message"]);
						}
					}
				);
			}

			//klik pada button edit
			table.on('click', '#edit', function(e) {
				//ambil data dari baris yang diklik
				var row = table.row(e.target.closest('tr')).data();
				var room_id = row[0];
				readFromServer({
					"room_id": room_id
				}, function(data) {
					flag = "edit";
					$('#myFormTitle').text("Edit Data");
					$('#room_id').val(data["room_id"]);
					$('#room_id').prop("disabled", true);
					$('#lantai').val(data["lantai"]);
					$('#jenis_kamar').val(data["jenis_kamar"]);
					$('#status_kamar').val(data["status_kamar"]);
					$('#keterangan').val(data["keterangan"]);
					$('#feedback').text("");
					$('#myForm').modal('show');
				});
			});
		</script>
	</body>

	</html>
<?php
}
if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"] == "show") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlCount = "SELECT count(*) FROM room;";

		//untuk mengembalikan data
		$length = intval($_REQUEST["length"]);
		$start = intval($_REQUEST["start"]);
		$sqlData = "SELECT room_id, lantai, jenis_kamar, case when status_kamar=1 Then 'Aktif' else 'Non-Aktif' end as status_kamar, keterangan FROM room WHERE (room_id LIKE :search or jenis_kamar Like :search) LIMIT $length OFFSET $start";
		$data = array();
		$data["draw"] = intval($_REQUEST["draw"]);
		$data["recordsTotal"] = querySingleValue($con, $sqlCount, array());
		$param = array("search" => $_REQUEST["search"]["value"] . "%");
		$data["data"] = queryArrayRowsValues($con, $sqlData, $param);
		$data["recordsFiltered"] = sizeof($data["data"]);

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	} else if ($_REQUEST["flag"] == "add") {
		$response = array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);
			$sql = "INSERT into room(room_id, lantai, jenis_kamar, status_kamar, keterangan) VALUES (:room_id, :lantai, :jenis_kamar, :status_kamar, :keterangan);";
			createRow($con, $sql, $data);
			$response["status_kamar"] = 1;
			$response["message"] = "Ok";
			$response["data"] = $data;
		} catch (Exception $e) {
			$response["status_kamar"] = 0;
			$response["message"] = $e->getMessage();
			$response["data"] = null;
		}

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	} else if ($_REQUEST["flag"] == "read") {
		$response = array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$param = json_decode($body, true);
			$sql = "SELECT room_id, lantai, jenis_kamar, status_kamar, keterangan FROM room WHERE room_id=:room_id;";
			$data = queryArrayValue($con, $sql, $param);
			$response["status_kamar"] = 1;
			$response["message"] = "Ok";
			$response["data"] = $data;
		} catch (Exception $e) {
			$response["status_kamar"] = 0;
			$response["message"] = $e->getMessage();
			$response["data"] = null;
		}

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	} else if ($_REQUEST["flag"] == "edit") {
		$response = array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);
			$sql = "UPDATE room SET lantai=:lantai, jenis_kamar=:jenis_kamar, status_kamar=:status_kamar, keterangan=:keterangan WHERE room_id=:room_id;";
			updateRow($con, $sql, $data);
			$response["status_kamar"] = 1;
			$response["message"] = "Ok";
			$response["data"] = $data;
		} catch (Exception $e) {
			$response["status_kamar"] = 0;
			$response["message"] = $e->getMessage();
			$response["data"] = null;
		}

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
} else {
	showUI();
}
?>