<?php
include("library.php");

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>AR</title>
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
            <h1 class="text-center">AR</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Tambah Data AR</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ar_id" class="form-label">AR ID</label>
                                <input type="text" class="form-control" id="ar_id" placeholder="Isikan AR ID">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" placeholder="Isikan tanggal">
                            </div>
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-control" id="jenis">
                                    <option value="Room">Room</option>
                                    <option value="Food&Beverages">Food&Beverages</option>
                                    <option value="Laundry">Laundry</option>
                                    <option value="Minibar">Minibar</option>
                                    <option value="Penalty">Penalty</option>
                                    <option value="Payment">Payment</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" placeholder="Isikan Keterangan">
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" placeholder="Isikan Amount">
                            </div>
                            <div class="mb-3">
                                <label for="dk_id" class="form-label">DK ID</label>
                                <input type="text" class="form-control" id="dk_id" placeholder="Isikan DK ID">
                            </div>
                            <div class="mb-3">
                                <label for="payment_id" class="form-label">Payment ID</label>
                                <input type="text" class="form-control" id="payment_id" placeholder="Isikan Payment ID">
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
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>AR ID</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Amount</th>
                            <th>DK ID</th>
                            <th>Payment ID</th>
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
                        data: "ar_id"
                    }, {
                        data: "tanggal"
                    }, {
                        data: "jenis"
                    }, {
                        data: "keterangan"
                    }, {
                        data: "amount"
                    }, {
                        data: "dk_id"
                    }, {
                        data: "payment_id"
                    }],
                });

                var myModal = new bootstrap.Modal(document.getElementById('myForm'), {})

                $('#add').click(function() {
                    flag = "add";
                    $('#myFormTitle').text("Tambah Data");
                    $('#ar_id').val("");
                    $('#tanggal').val("");
                    $('#jenis').val("");
                    $('#keterangan').val("");
                    $('#amount').val("");
                    $('#type').val("");
                    $('#card_number').val("");
                    $('#payment_id').val("");
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


            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </body>

    </html>
<?php
}
if (isset($_REQUEST["flag"])) {
    if ($_REQUEST["flag"] == "show") {
        $con = openConnection();

        //untuk hitung total baris
        $sqlCount = "SELECT count(*) FROM ar;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT ar_id, tanggal, jenis,keterangan, amount, dk_id, payment_id  FROM ar WHERE ar_id LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into ar(ar_id, tanggal, jenis, keterangan, amount, dk_id,  payment_id) VALUES (:ar_id, :tanggal, :jenis, :keterangan, :amount, :dk_id, :payment_id);";
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