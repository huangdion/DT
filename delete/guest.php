<?php
include "../library.php";

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Delete Guest</title>
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
            <h1 class="text-center">Guest</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Tambah Data Guest</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="guest_id" class="form-label">Guest ID</label>
                                <input type="text" class="form-control" id="guest_id" placeholder="Isikan ID Guest">
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" placeholder="Isikan Nama">
                            </div>
                            <div class="mb-3">
                                <label for="jenis_id" class="form-label">Jenis ID</label>
                                <input type="text" class="form-control" id="jenis_id" placeholder="Isikan Jenis ID">
                            </div>
                            <div class="mb-3">
                                <label for="nomor_id" class="form-label">Nomor ID</label>
                                <input type="text" class="form-control" id="nomor_id" placeholder="Isikan Nomor ID">
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" placeholder="Isikan Contact Number">
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
                            <th>Guest ID</th>
                            <th>Nama</th>
                            <th>Jenis ID</th>
                            <th>Nomor ID</th>
                            <th>Contact Number</th>
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
                        data: "guest_id"
                    },
                    {
                        data: "nama"
                    },
                    {
                        data: "jenis_id"
                    },
                    {
                        data: "nomor_id"
                    },
                    {
                        data: "contact_number"
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
                $('#myFormTitle').text("Tambah Data");
                $('#guest_id').val("");
                $('#guest_id').prop("disabled", false);
                $('#nama').val("");
                $('#jenis_id').val("");
                $('#nomor_id').val("");
                $('#contact_number').val("");
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

            // Klik pada button save
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

            // Klik pada button edit
            table.on('click', '#edit', function(e) {
                var row = table.row(e.target.closest('tr')).data();
                var guest_id = row.guest_id;
                readFromServer({
                    "guest_id": guest_id
                }, function(data) {
                    flag = "edit";
                    $('#myFormTitle').text("Edit Data");
                    $('#guest_id').val(data["guest_id"]);
                    $('#guest_id').prop("disabled", true);
                    $('#nama').val(data["nama"]);
                    $('#jenis_id').val(data["jenis_id"]);
                    $('#nomor_id').val(data["nomor_id"]);
                    $('#contact_number').val(data["contact_number"]);
                    $('#feedback').text("");
                    $('#myForm').modal('show');
                });
            });

            // Klik pada button delete
            table.on('click', '#delete', function(e) {
                var row = table.row(e.target.closest('tr')).data();
                var guest_id = row.guest_id;
                readFromServer({
                    "guest_id": guest_id
                }, function(data) {
                    flag = "delete";
                    $('#myFormTitle').text("Hapus Data");
                    $('#guest_id').val(data["guest_id"]);
                    $('#guest_id').prop("disabled", true);
                    $('#nama').val(data["nama"]);
                    $('#jenis_id').val(data["jenis_id"]);
                    $('#nomor_id').val(data["nomor_id"]);
                    $('#contact_number').val(data["contact_number"]);
                    $('#save').text("Delete record");

                    $('#feedback').text("");
                    $('#myForm').modal('show');
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
        </script>
    </body>

    </html>
<?php
}
if (isset($_REQUEST["flag"])) {
    if ($_REQUEST["flag"] == "show") {
        $con = openConnection();

        //untuk hitung total baris
        $sqlCount = "SELECT count(*) FROM guest;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT guest_id, nama, jenis_id, nomor_id, contact_number FROM guest WHERE guest_id LIKE :search OR nama LIKE :search OR jenis_id LIKE :search OR nomor_id LIKE :search OR contact_number LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into guest(guest_id, nama, jenis_id, nomor_id, contact_number) VALUES (:guest_id, :nama, :jenis_id, :nomor_id, :contact_number);";
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
            $sql = "SELECT guest_id, nama, jenis_id, nomor_id, contact_number FROM guest WHERE guest_id=:guest_id;";
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
            $sql = "UPDATE guest SET nama = :nama, jenis_id = :jenis_id, nomor_id = :nomor_id, contact_number = :contact_number WHERE guest_id=:guest_id;";
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
            $sql = "DELETE FROM guest WHERE guest_id=:guest_id;";
            deleteRow($con, $sql, array("guest_id" => $data['guest_id']));
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