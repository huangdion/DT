<?php
include("library.php");

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Deposit</title>
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
                                    <input type="text" class="form-control" id="group_id" placeholder="Isikan ID Group" required>
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
                                    <input type="text" class="form-control" id="type" placeholder="Isikan Type" required>
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

            <div class="container m-md-auto">
                <p>
                    <button id="add" type="button" class="btn btn-primary btn-sm">Tambah</button>
                </p>
                <table id="example" class="display table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Group ID</th>
                            <th>Tanggal</th>
                            <th>DK ID</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Card Number</th>
                            <th>Expired Date</th>
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
                data: "group_id"
            }, {
                data: "tanggal"
            }, {
                data: "dk_id"
            }, {
                data: "amount"
            }, {
                data: "type"
            }, {
                data: "card_number"
            }, {
                data: "expired_date"
            }],
        });

        var myModal = new bootstrap.Modal(document.getElementById('myForm'), {})

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
        $sqlCount = "SELECT count(*) FROM deposit;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT group_id, tanggal, dk_id, amount, 'type',card_number, expired_date  FROM deposit WHERE group_id LIKE :search LIMIT $length OFFSET $start";

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
    }
} else {
    showUI();
}
?>