<?php
include "../library.php";

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Update AR</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../bootstrap-5.3.2-dist/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="../bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script> <!-- Tambahkan ini -->
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



            <div class="container-fluid">
                <p>
                    <button id="add" type="button" class="btn btn-primary btn-sm">Add</button>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                }, {
                    "orderable": false,
                    "data": null,
                    "defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>"
                }]
            });

            var myModal = new bootstrap.Modal(document.getElementById('myForm'), {})

            $('#add').click(function() {
                flag = "add";
                $('#myFormTitle').text("Tambah Data");
                $('#ar_id').val("");
                $('#ar_id').prop("disabled", false);
                $('#tanggal').val("");
                $('#jenis').val("");
                $('#keterangan').val("");
                $('#amount').val("");
                $('#dk_id').val("");
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
                var ar_id = row[0];
                readFromServer({
                    "ar_id": ar_id
                }, function(data) {
                    flag = "edit";
                    $('#myFormTitle').text("Edit Data");
                    $('#ar_id').val(data["ar_id"]);
                    $('#ar_id').prop("disabled", true);

                    $('#tanggal').val(data["tanggal"]);
                    $('#jenis').val(data["jenis"]);
                    $('#keterangan').val(data["keterangan"]);
                    $('#amount').val(data["amount"]);
                    $('#dk_id').val(data["dk_id"]);
                    $('#payment_id').val(data["payment_id"]);
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
        $sqlCount = "SELECT count(*) FROM ar;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT ar_id, tanggal, jenis, keterangan, amount, dk_id, payment_id FROM ar WHERE ar_id LIKE :search OR tanggal LIKE :search OR jenis LIKE :search OR keterangan LIKE :search OR amount LIKE :search OR dk_id LIKE :search OR payment_id LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into ar(ar_id, tanggal, jenis, keterangan, amount, dk_id, payment_id) VALUES (:ar_id, :tanggal, :jenis, :keterangan, :amount, :dk_id, :payment_id);";
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
    } else if ($_REQUEST["flag"] == "read") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $param = json_decode($body, true);
            $sql = "SELECT ar_id, tanggal, jenis, keterangan, amount, dk_id, payment_id FROM ar WHERE ar_id=:ar_id;";
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
            $sql = "UPDATE ar SET tanggal = :tanggal, jenis = :jenis, keterangan = :keterangan, amount = :amount, dk_id = :dk_id, payment_id = :payment_id WHERE ar_id=:ar_id;";
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