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
                            <input type="text" class="form-control" id="room_id" placeholder="Isikan ID Room">
                        </div>
                        <div class="mb-3">
                            <label for="guest_id" class="form-label">guest_id</label>
                            <input type="text" class="form-control" id="guest_id" placeholder="Isikan ID Guest">
                        </div>
                        <div class="mb-3">
                            <label for="voucher_id" class="form-label">voucher_id</label>
                            <input type="text" class="form-control" id="voucher_id" placeholder="Isikan ID Voucher">
                        </div>
                        <div class="mb-3">
                            <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="dari_tanggal">
                        </div>
                        <div class="mb-3">
                            <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="sampai_tanggal">
                        </div>
                        <div class="mb-3">
                            <label for="ready_time">Ready Time</label>
                            <input type="datetime-local" class="form-control" id="ready_time" name="ready_time" /> <br>
                        </div>
                        <div class="mb-3">
                            <label for="checkin_time">Check In Time</label>
                            <input type="datetime-local" class="form-control" id="checkin_time" name="checkin_time" /> <br>
                        </div>
                        <div class="mb-3">
                            <label for="checkout_time">Check Out Time</label>
                            <input type="datetime-local" class="form-control" id="checkout_time" name="checkout_time" /> <br>
                        </div>
                        <div class="mb-3">
                            <label for="rate" class="form-label">rate</label>
                            <input type="text" class="form-control" id="rate" placeholder="Isikan Harga">
                        </div>
                        <div class="mb-3">
                            <label for="group_id" class="form-label">Group ID</label>
                            <input type="text" class="form-control" id="group_id">
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
                        <th>guest_id</th>
                        <th>voucher_id</th>
                        <th>dari_tanggal</th>
                        <th>sampai_tanggal</th>
                        <th>ready_time</th>
                        <th>checkin_time</th>
                        <th>checkout_time</th>
                        <th>rate</th>
                        <th>group_id</th>
                    </tr>
                </thead>
            </table>
        </div>
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
                    data: 'guest_id'
                }, {
                    data: 'voucher_id'
                }, {
                    data: 'dari_tanggal'
                }, {
                    data: 'sampai_tanggal'
                }, {
                    data: 'ready_time'
                }, {
                    data: 'checkin_time'
                }, {
                    data: 'checkout_time'
                }, {
                    data: 'rate'
                }, {
                    data: 'group_id'
                }]
            });

            $('#add').click(function() {
                flag = "add";
                $('#myFormTitle').text("Add Data");
                $('#room_id').val("");
                $('#guest_id').val("");
                $('#voucher_id').val("");
                $('#dari_tanggal').val("");
                $('#sampai_tanggal').val("");
                $('#ready_time').val("");
                $('#checkin_time').val("");
                $('#checkout_time').val("");
                $('#rate').val("");
                $('#group_id').val("")
                $('#feedback').text("");
                $('#myForm').modal('show');
            });

            function postToServer(obj, callBack) {
                $.post("?flag=" + flag,
                    JSON.stringify(obj),
                    function(data, status) {
                        if (data["status"] == 1) {
                            callBack();
                        } else {
                            $("#feedback").text(data["message"]);
                        }
                    }
                );
            }

            $('#save').click(function() {
                var formControl = document.getElementsByClassName("form-control");
                var data = {};
                for (var i = 0; i < formControl.length; i++) {
                    data[formControl[i].id] = formControl[i].value;
                }

                postToServer(data, function() {
                    $('#myForm').modal('hide');
                    table.ajax.reload();
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
        $sqlCount = "SELECT count(*) FROM occupied;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT * FROM occupied WHERE room_id LIKE :search OR guest_id LIKE :search OR voucher_id LIKE :search OR dari_tanggal LIKE :search OR sampai_tanggal LIKE :search OR ready_time LIKE :search OR checkin_time LIKE :search OR checkout_time LIKE :search OR rate LIKE :search OR group_id LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT INTO occupied (room_id, guest_id, voucher_id, dari_tanggal, sampai_tanggal, ready_time, checkin_time, checkout_time, rate, group_id) 
VALUES (:room_id, :guest_id, :voucher_id, :dari_tanggal, :sampai_tanggal, :ready_time, :checkin_time, :checkout_time, :rate, :group_id);
";
            createRow($con, $sql, $data);
            $response["status"] = 1;
            $response["message"] = "Ok";
            $response["data"] = $data;
        } catch (Exception $e) {
            $response["status"] = 0;
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