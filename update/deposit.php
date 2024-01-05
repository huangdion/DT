<?php
include "../library.php";

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Update Deposit</title>
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
            <h1 class="text-center">Deposit</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Tambah Data</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="myForm">
                                <div class="mb-3">
                                    <label for="group_id" class="form-label">Group ID</label>
                                    <!-- <input type="text" class="form-control" id="group_id" placeholder="Isikan ID Group" required> -->
                                    <select name="group_id" id="group_id" class="form-select">
                                        <option value="">Pilih Satu</option>
                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dk_id" class="form-label">DK ID</label>
                                    <input type="text" class="form-control" id="dk_id" placeholder="Isikan DK ID" required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" placeholder="Isikan Amount" required>
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-control" id="type">
                                        <option value="Cash">Cash</option>
                                        <option value="Debit">Debit</option>
                                        <option value="Credit">Credit</option>

                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" placeholder="Isikan Card Number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="expired_date" class="form-label">Expired Date</label>
                                    <input type="date" class="form-control" id="expired_date" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <p id="feedback">
                            <p>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button id="save" type="submit" form="myForm" class="btn btn-primary">Simpan Perubahan</button>
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
                            <th>Group ID</th>
                            <th>Tanggal</th>
                            <th>DK ID</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Card Number</th>
                            <th>Expired Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $.get("optionsocc.php?flag=group_id",
                    function(data, group_id) {
                        for (i = 0; i <= data.length; i++) {
                            $('#group_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
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
                        data: "group_id"
                    },
                    {
                        data: "tanggal"
                    },
                    {
                        data: "dk_id"
                    },
                    {
                        data: "amount"
                    },
                    {
                        data: "type"
                    },
                    {
                        data: "card_number"
                    },
                    {
                        data: "expired_date"
                    },
                    {
                        "orderable": false,
                        "data": null,
                        "defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>"
                    }
                ],
            });

            var myModal = new bootstrap.Modal(document.getElementById('myForm'), {});

            $('#add').click(function() {
                flag = "add";
                $('#myFormTitle').text("Tambah Data");
                $('#group_id').val("");
                $('#tanggal').val("");
                $('#dk_id').val("");
                $('#amount').val("");
                $('#type').val("");
                $('#card_number').val("");
                $('#expired_date').val("");
                $('#feedback').text("");
                myModal.show();
            });

            function postToServer(obj, callBack) {
                $.ajax({
                    url: '?flag=' + flag,
                    type: 'POST',
                    data: JSON.stringify(obj),
                    contentType: 'application/json',
                    success: function(data) {
                        if (data["status"] == 1) {
                            callBack();
                        } else {
                            $("#feedback").text(data["message"]);
                        }
                    }
                });
            }

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

            $('#save').click(function(e) {
                e.preventDefault();

                var data = {
                    group_id: $('#group_id').val(),
                    tanggal: $('#tanggal').val(),
                    dk_id: $('#dk_id').val(),
                    amount: $('#amount').val(),
                    type: $('#type').val(),
                    card_number: $('#card_number').val(),
                    expired_date: $('#expired_date').val()
                };

                var flag = $('#group_id').prop('disabled') ? 'edit' : 'add';

                $.ajax({
                    url: '?flag=' + flag,
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response.status == 1) {
                            // Retrieve the modal instance and hide it
                            var myModal = bootstrap.Modal.getInstance(document.getElementById('myForm'));
                            myModal.hide();
                            table.ajax.reload();
                        } else {
                            $("#feedback").text(response.message);
                        }
                    }
                });
            });


            // klik pada button edit
            table.on('click', '#edit', function(e) {
                //ambil data dari baris yang diklik
                var row = table.row(e.target.closest('tr')).data();
                var group_id = row.group_id;
                readFromServer({
                    "group_id": group_id
                }, function(data) {
                    flag = "edit";
                    $('#myFormTitle').text("Edit Data");
                    $('#group_id').val(data["group_id"]);
                    // $('#group_id').prop("disabled", true);

                    $('#tanggal').val(data["tanggal"]);
                    $('#dk_id').val(data["dk_id"]);
                    $('#amount').val(data["amount"]);
                    $('#type').val(data["type"]);
                    $('#card_number').val(data["card_number"]);
                    $('#expired_date').val(data["expired_date"]);
                    $('#feedback').text("");
                    $('#myForm').modal('show');
                });
            });

            // klik pada button delete
            table.on('click', '#delete', function(e) {
                //ambil data dari baris yang diklik
                var row = table.row(e.target.closest('tr')).data();
                var group_id = row.group_id;

                readFromServer({
                    "group_id": group_id
                }, function(data) {
                    flag = "delete";
                    $('#myFormTitle').text("Hapus Data");
                    $('#group_id').val(data["group_id"]);
                    $('#group_id').prop("disabled", true);
                    $('#tanggal').val(data["tanggal"]);
                    $('#dk_id').val(data["dk_id"]);
                    $('#amount').val(data["amount"]);
                    $('#type').val(data["type"]);
                    $('#card_number').val(data["card_number"]);
                    $('#expired_date').val(data["expired_date"]);
                    $('#feedback').text("");
                    $('#save').text("Delete record");
                    myModal.show();
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
        $sqlCount = "SELECT count(*) FROM deposit;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT group_id, tanggal, dk_id, amount, type, card_number, expired_date FROM deposit WHERE group_id LIKE :search OR tanggal LIKE :search OR dk_id LIKE :search OR amount LIKE :search OR type LIKE :search OR card_number LIKE :search OR expired_date LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into deposit(group_id, tanggal, dk_id, amount, type, card_number, expired_date) VALUES (:group_id, :tanggal, :dk_id, :amount, :type, :card_number, :expired_date);";
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
            $sql = "SELECT group_id, tanggal, dk_id, amount, type, card_number, expired_date FROM deposit WHERE group_id=:group_id;";
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
            $sql = "UPDATE deposit SET tanggal = :tanggal, dk_id = :dk_id, amount = :amount, type = :type, card_number = :card_number, expired_date = :expired_date WHERE group_id=:group_id;";
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