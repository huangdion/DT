<?php
include "../library.php";

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Update Payment</title>
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
            <h1 class="text-center">Payment</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Update Data Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="payment_id" class="form-label">Payment ID</label>
                                <input type="text" class="form-control" id="payment_id" placeholder="Isikan ID Payment">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" placeholder="Isikan tanggal">
                            </div>
                            <div class="mb-3">
                                <label for="voucher_id" class="form-label">Voucher ID</label>
                                <input type="text" class="form-control" id="voucher_id" placeholder="Isikan voucher_id">
                            </div>
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-control" id="jenis">
                                    <option value="Cash">Cash</option>
                                    <option value="Credit">Credit</option>
                                    <option value="Partner">Partner</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" placeholder="Isikan Amount">
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
                            <th>Payment ID</th>
                            <th>Tanggal</th>
                            <th>Voucher ID</th>
                            <th>Jenis</th>
                            <th>Amount</th>
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
                    data: "payment_id"
                }, {
                    data: "tanggal"
                }, {
                    data: "voucher_id"
                }, {
                    data: "jenis"
                }, {
                    data: "amount"
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
                $('#payment_id').val("");
                $('#payment_id').prop("disabled", false);
                $('#tanggal').val("");
                $('#voucher_id').val("");
                $('#jenis').val("");
                $('#amount').val("");
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
                var jenis_kamar = row[0];
                readFromServer({
                    "jenis_kamar": jenis_kamar
                }, function(data) {
                    flag = "edit";
                    $('#myFormTitle').text("Edit Data");
                    $('#jenis_kamar').val(data["jenis_kamar"]);
                    $('#jenis_kamar').prop("disabled", true);

                    $('#rate').val(data["rate"]);
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
        $sqlCount = "SELECT count(*) FROM payment;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT payment_id, tanggal, voucher_id, jenis, amount FROM payment WHERE payment_id LIKE :search OR tanggal LIKE :search OR voucher_id LIKE :search OR jenis LIKE :search OR amount LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into payment(payment_id, tanggal, voucher_id, jenis, amount) VALUES (:payment_id, :tanggal, :voucher_id, :jenis, :amount);";
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
            $sql = "SELECT payment_id, tanggal, voucher_id, jenis, amount FROM payment WHERE payment_id=:payment_id;";
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
            $sql = "UPDATE payment SET tanggal = :tanggal, voucher_id = :voucher_id, jenis = :jenis, amount = :amount WHERE payment_id=:payment_id;";
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
