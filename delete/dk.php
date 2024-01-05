<?php
include "../library.php";

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Delete DK</title>
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
            <h1 class="text-center">DK</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Tambah Data DK</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="dk_id" class="form-label">DK ID</label>
                                <input type="text" class="form-control" id="dk_id" placeholder="Isikan ID DK">
                            </div>
                            <div class="mb-3">
                                <label for="group_id" class="form-label">Group ID</label>
                                <!-- <input type="text" class="form-control" id="group_id" placeholder="Isikan group id"> -->
                                <select name="group_id" id="group_id" class="form-select">
                                    <option value="">Pilih Satu</option>
                                </select>

                            </div>
                            <div class="mb-3">
                                <label for="room_id" class="form-label">Room ID</label>
                                <!-- <input type="text" class="form-control" id="room_id" placeholder="Isikan room_id"> -->
                                <select name="room_id" id="room_id" class="form-select">
                                    <option value="">Pilih Satu</option>
                                </select>

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
                            <th>DK ID</th>
                            <th>Group ID</th>
                            <th>Room ID</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $.get("SelectOptions.php?flag=room_id",
                    function(data, status_kamar) {
                        for (i = 0; i <= data.length; i++) {
                            $('#room_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
                        }
                    }
                );
                $.get("SelectOptions.php?flag=group_id",
                    function(data, status_kamar) {
                        for (i = 0; i <= data.length; i++) {
                            $('#group_id').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
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
                columns: [
                    // Columns definition
                    {
                        data: 'dk_id'
                    },
                    {
                        data: 'group_id'
                    },
                    {
                        data: 'room_id'
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'jenis'
                    },
                    {
                        data: 'keterangan'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        "orderable": false,
                        "data": null,
                        "defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>"
                    }
                ]
            });

            $('#add').click(function() {
                flag = "add";
                $('#myFormTitle').text("Add Record");
                $('#dk_id').val("");
                $('#group_id').val("");
                $('#room_id').val("");
                $('#tanggal').val("");
                $('#jenis').val("");
                $('#keterangan').val("");
                $('#amount').val("");
                $('#save').text("Save Changes");
                $('#feedback').text("");
                $('#myForm').modal('show');
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

            $('#save').click(function(e) {
                e.preventDefault();

                var data = {
                    dk_id: $('#dk_id').val(),
                    group_id: $('#group_id').val(),
                    room_id: $('#room_id').val(),
                    tanggal: $('#tanggal').val(),
                    jenis: $('#jenis').val(),
                    keterangan: $('#keterangan').val(),
                    amount: $('#amount').val()
                };

                flag = $('#dk_id').prop('disabled') ? 'edit' : 'add';

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
            table.on('click', '#edit', function(e) {
                var data = table.row($(this).parents('tr')).data();

                $('#myFormTitle').text("Edit Record");
                $('#dk_id').val(data.dk_id).prop('disabled', true);
                $('#group_id').val(data.group_id);
                $('#room_id').val(data.room_id);
                $('#tanggal').val(data.tanggal);
                $('#jenis').val(data.jenis);
                $('#keterangan').val(data.keterangan);
                $('#amount').val(data.amount);
                $('#feedback').text("");
                $('#save').text("Update Record");

                $('#myForm').modal('show');
            });
            table.on('click', '#delete', function(e) {
                // Ambil data dari baris yang diklik
                var data = table.row(e.target.closest('tr')).data();
                var dk_id = data.dk_id;

                // Open the modal
                $('#myFormTitle').text("Delete Record");
                $('#dk_id').val(data.dk_id).prop('disabled', true);
                $('#group_id').val(data.group_id);
                $('#room_id').val(data.room_id);
                $('#tanggal').val(data.tanggal);
                $('#jenis').val(data.jenis);
                $('#keterangan').val(data.keterangan);
                $('#amount').val(data.amount);
                $('#feedback').text("");
                $('#save').text("Delete Record");

                $('#myForm').modal('show');

                // Initialize the modal
                var myModal = new bootstrap.Modal(document.getElementById('myForm'));
                myModal.show();

                // Handle delete button click inside the modal
                $('#save').off('click').on('click', function() {
                    var deleteData = {
                        dk_id: $('#dk_id').val()
                    };

                    $.ajax({
                        url: '?flag=delete',
                        type: 'POST',
                        data: JSON.stringify(deleteData),
                        contentType: 'application/json',
                        success: function(response) {
                            if (response.status == 1) {
                                // Close the modal after successful delete
                                myModal.hide();
                                table.ajax.reload();

                                // Clean up modal events
                                myModal.dispose();
                            } else {
                                $("#feedback").text(response.message);
                            }
                        }
                    });
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
        $sqlCount = "SELECT count(*) FROM dk;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT dk_id, group_id, room_id, tanggal, jenis, keterangan, amount FROM dk WHERE dk_id LIKE :search OR group_id LIKE :search OR room_id LIKE :search OR tanggal LIKE :search OR jenis LIKE :search OR keterangan LIKE :search OR amount LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into dk(dk_id, group_id, room_id, tanggal, jenis, keterangan, amount) VALUES (:dk_id, :group_id, :room_id, :tanggal, :jenis, :keterangan, :amount);";
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
            $sql = "SELECT dk_id, group_id, room_id, tanggal, jenis, keterangan, amount FROM dk WHERE dk_id=:dk_id;";
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
            $sql = "UPDATE dk SET group_id = :group_id, room_id = :room_id, tanggal = :tanggal, jenis = :jenis, keterangan = :keterangan, amount = :amount WHERE dk_id=:dk_id;";
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
    } else if ($_REQUEST["flag"] == "delete") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
            $sql = "DELETE FROM dk WHERE dk_id=:dk_id;";
            deleteRow($con, $sql, array("dk_id" => $data['dk_id']));
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