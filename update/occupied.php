<?php
include "../library.php";

function showUI()
{
?>
    <?php
    // Database connection
    $con = mysqli_connect("localhost", "root", "", "hotel");

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // SQL query
    $sql = "SELECT * FROM `rate`";
    $result = $con->query($sql);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Update Occupied</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="bootstrap-5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="bootstrap-5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="jquery/jquery-3.7.1.min.js"></script>
        <link href="DataTables/datatables.min.css" rel="stylesheet">
        <script src="DataTables/datatables.min.js"></script>
    </head>

    <body>
        <div class="container m-md-auto">
            <h1 class="text-center">Occupied</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Tambah Data Occupied</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="room_id" class="form-label">Room ID</label>
                                <!-- <input type="text" class="form-control" id="room_id" placeholder="Isikan Room ID"> -->
                                <select name="room_id" id="room_id" class="form-select">
                                    <option value="">Pilih Satu</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="guest_id" class="form-label">Guest ID</label>
                                <!-- <input type="text" class="form-control" id="guest_id" placeholder="Isikan Guest ID"> -->
                                <select name="guest_id" id="guest_id" class="form-select">
                                    <option value="">Pilih Satu</option>
                                </select>

                            </div>
                            <div class="mb-3">
                                <label for="voucher_id" class="form-label">Voucher ID</label>
                                <!-- <input type="text" class="form-control" id="voucher_id" placeholder="Isikan Voucher ID"> -->
                                <select name="voucher_id" id="voucher_id" class="form-select">
                                    <option value="">Pilih Satu</option>
                                </select>

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
                                <label for="ready_time" class="form-label">Ready Time</label>
                                <input type="datetime-local" class="form-control" id="ready_time">
                            </div>
                            <div class="mb-3">
                                <label for="checkin_time" class="form-label">Checkin Time</label>
                                <input type="datetime-local" class="form-control" id="checkin_time">
                            </div>
                            <div class="mb-3">
                                <label for="checkout_time" class="form-label">Checkout Time</label>
                                <input type="datetime-local" class="form-control" id="checkout_time">
                            </div>
                            <div class="mb-3">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="number" step="0.01" class="form-control" id="rate" placeholder="Isikan Rate">
                            </div>
                            <div class="mb-3">
                                <label for="group_id" class="form-label">Group ID</label>
                                <input type="text" class="form-control" id="group_id" placeholder="Isikan Group ID">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <p id="feedback"></p>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button id="save" type="button" class="btn btn-primary">Simpan Perubahan</button>
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
                            <th>Occupied ID</th>
                            <th>Room ID</th>
                            <th>Guest ID</th>
                            <th>Voucher ID</th>
                            <th>Dari Tanggal</th>
                            <th>Sampai Tanggal</th>
                            <th>Ready Time</th>
                            <th>Checkin Time</th>
                            <th>Checkout Time</th>
                            <th>Rate</th>
                            <th>Group ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $.get("optionsocc.php?flag=room_id",
                    function(data, room_id) {
                        for (i = 0; i <= data.length; i++) {
                            $('#room_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
                        }
                    }
                );
                $.get("optionsocc.php?flag=guest_id",
                    function(data, guest_id) {
                        for (i = 0; i <= data.length; i++) {
                            $('#guest_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
                        }
                    }
                );
                $.get("optionsocc.php?flag=voucher_id",
                    function(data, voucher_id) {
                        for (i = 0; i <= data.length; i++) {
                            $('#voucher_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
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
                    data: "occupied_id"
                }, {
                    data: "room_id"
                }, {
                    data: "guest_id"
                }, {
                    data: "voucher_id"
                }, {
                    data: "dari_tanggal"
                }, {
                    data: "sampai_tanggal"
                }, {
                    data: "ready_time"
                }, {
                    data: "checkin_time"
                }, {
                    data: "checkout_time"
                }, {
                    data: "rate"
                }, {
                    data: "group_id"
                }, {
                    "orderable": false,
                    "data": null,
                    "defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>"
                }]
            });

            var myModal = new bootstrap.Modal(document.getElementById('myForm'), {})

            $('#add').click(function() {
                flag = "add";
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
                    function(data, status) {
                        if (data["status"] == 1) {
                            callBack();
                        } else {
                            $("#feedback").text(data["message"]);
                        }
                    }
                );
            }

            //klik pada button save
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

            function readFromServer(obj, callBack) {
                $.post("?flag=read",
                    JSON.stringify(obj),
                    function(data, status) {
                        if (data["status"] == 1) {
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
                var occupied_id = row['occupied_id'];
                readFromServer({
                    "occupied_id": occupied_id
                }, function(data) {
                    flag = "edit";
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
        $sqlData = "SELECT occupied_id, room_id, guest_id, voucher_id, dari_tanggal, sampai_tanggal, ready_time, checkin_time, checkout_time, rate, group_id FROM occupied WHERE occupied_id LIKE :search OR room_id LIKE :search OR guest_id LIKE :search OR voucher_id LIKE :search OR dari_tanggal LIKE :search OR sampai_tanggal LIKE :search OR ready_time LIKE :search OR checkin_time LIKE :search OR checkout_time LIKE :search OR rate LIKE :search OR group_id LIKE :search LIMIT $length OFFSET $start";

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
            $sql =
                "INSERT INTO occupied (room_id, guest_id, voucher_id, dari_tanggal, sampai_tanggal, ready_time, checkin_time, checkout_time, rate, group_id) 
VALUES (:room_id, :guest_id, :voucher_id, :dari_tanggal, :sampai_tanggal, :ready_time, :checkin_time, :checkout_time, :rate, :group_id);
";
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
    } else if ($_REQUEST["flag"] == "read") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $param = json_decode($body, true);
            $sql = "SELECT occupied_id, room_id, guest_id, voucher_id, dari_tanggal, sampai_tanggal, ready_time, checkin_time, checkout_time, rate, group_id FROM occupied WHERE occupied_id=:occupied_id;";
            $data = queryArrayValue($con, $sql, $param);
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
    } else if ($_REQUEST["flag"] == "edit") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
            $sql = "UPDATE occupied SET room_id=:room_id, guest_id=:guest_id, voucher_id=:voucher_id, dari_tanggal=:dari_tanggal, sampai_tanggal=:sampai_tanggal, rate=:rate, group_id=:group_id WHERE occupied_id=:occupied_id and checkout_time is Null;";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(
                ':occupied_id' => $data['occupied_id'],
                ':room_id' => $data['room_id'],
                ':guest_id' => $data['guest_id'],
                ':voucher_id' => $data['voucher_id'],
                ':dari_tanggal' => $data['dari_tanggal'],
                ':sampai_tanggal' => $data['sampai_tanggal'],
                ':rate' => $data['rate'],
                ':group_id' => $data['group_id'],
            ));

            updateRow($con, $sql, $data);
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