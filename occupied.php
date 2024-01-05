<?php
include("library.php");

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Occupied</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script> <!-- Tambahkan ini -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

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
                                <input type="text" class="form-control" id="room_id" placeholder="Isikan Room ID">
                            </div>
                            <div class="mb-3">
                                <label for="guest_id" class="form-label">Guest ID</label>
                                <input type="text" class="form-control" id="guest_id" placeholder="Isikan Guest ID">
                            </div>
                            <div class="mb-3">
                                <label for="voucher_id" class="form-label">Voucher ID</label>
                                <input type="text" class="form-control" id="voucher_id" placeholder="Isikan Voucher ID">
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

            <div class="container m-md-auto">
                <p>
                    <button id="add" type="button" class="btn btn-primary btn-sm">Tambah</button> <!-- Ubah teks tombol -->
                </p>
                <table id="example" class="display table table-striped" style="width:100%">
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
                            data: "occupied_id"
                        },
                        {
                            data: "room_id"
                        },
                        {
                            data: "guest_id"
                        },
                        {
                            data: "voucher_id"
                        },
                        {
                            data: "dari_tanggal",
                            render: function(data, type, row) {
                                return moment(data, 'YYYY-MM-DD').format('YYYY-MM-DD');
                            }
                        },
                        {
                            data: "sampai_tanggal",
                            render: function(data, type, row) {
                                return moment(data, 'YYYY-MM-DD').format('YYYY-MM-DD');
                            }
                        },
                        {
                            data: "ready_time"
                        },
                        {
                            data: "checkin_time"
                        },
                        {
                            data: "checkout_time"
                        },
                        {
                            data: "rate"
                        },
                        {
                            data: "group_id"
                        },
                    ],
                });

                var myModal = new bootstrap.Modal(document.getElementById('myForm'), {});

                $('#add').click(function() {
                    flag = "add";
                    $('#myFormTitle').text("Tambah Data");
                    $('#room_id').val("");
                    $('#guest_id').val("");
                    $('#voucher_id').val("");
                    $('#dari_tanggal').val("");
                    $('#sampai_tanggal').val("");
                    $('#ready_time').val("");
                    $('#checkin_time').val("");
                    $('#checkout_time').val("");
                    $('#rate').val("");
                    $('#group_id').val("");
                    $('#feedback').text("");
                    myModal.show();
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


            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
            </script>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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