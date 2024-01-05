<?php
include("../library.php");

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
        <title>Room</title>
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
            <h1 class="text-center">Room</h1>
            <!-- Modal -->
            <div class="modal fade" id="myForm" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="myFormTitle">Update Data</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="myForm">
                                <div class="mb-3">
                                    <label for="room_id" class="form-label">Room ID</label>
                                    <input type="text" class="form-control" id="room_id" placeholder="Isikan ID Kamar" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lantai" class="form-label">Lantai</label>
                                    <input type="number" class="form-control" id="lantai" placeholder="Isikan lantai" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_kamar" class="form-label">Jenis Kamar</label>
                                    <!-- <input type="text" class="form-control" id="jenis_kamar"> -->
                                    <select name="jenis_kamar" id="jenis_kamar" class="form-select">
                                        <option value="">Pilih Satu</option>
                                        <!-- <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row["jenis_kamar"] . '">' . $row["jenis_kamar"] . '</option>';
                                            }
                                        } else {
                                            echo "No results";
                                        }
                                        $con->close();
                                        ?> -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="status_kamar" class="form-label">Status Kamar</label>
                                    <input type="text" class="form-control" id="status_kamar" placeholder="Isikan Status Kamar" required>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <input type="text" class="form-control" id="keterangan" placeholder="Isikan Keterangan" required>
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
                            <th>Room ID</th>
                            <th>Lantai</th>
                            <th>Jenis Kamar</th>
                            <th>Status Kamar</th>
                            <th>Keterangan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function() {
            $.get("SelectOptions.php?flag=jenis_kamar",
                function(data, status_kamar) {
                    for (i = 0; i <= data.length; i++) {
                        $('#jenis_kamar').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
                    }
                }
            );
        });
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                serverSide: true,
                ajax: {
                    url: '?flag=show',
                    type: 'POST'
                },
                columns: [{
                        data: "room_id"
                    },
                    {
                        data: "lantai"
                    },
                    {
                        data: "jenis_kamar"
                    },
                    {
                        data: "status_kamar"
                    },
                    {
                        data: "keterangan"
                    },
                    {
                        "orderable": false,
                        "data": null,
                        "defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>"
                    }
                ],
            });

            $('#add').click(function() {
                $('#myFormTitle').text("Tambah Data");
                $('#myForm').trigger("reset");
                $('#feedback').text("");
                $('#myForm').modal('show');
            });

            $('#save').click(function(e) {
                e.preventDefault();

                var data = {
                    room_id: $('#room_id').val(),
                    lantai: $('#lantai').val(),
                    jenis_kamar: $('#jenis_kamar').val(),
                    status_kamar: $('#status_kamar').val(),
                    keterangan: $('#keterangan').val()
                };

                var flag = $('#room_id').prop('disabled') ? 'edit' : 'add';

                $.ajax({
                    url: '?flag=' + flag,
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function(response) {
                        console.log(typeof response); // Check the type of response
                        console.log(response); // Log the response object
                        if (response.status == 1) {
                            $('#myForm').modal('hide');
                            table.ajax.reload();
                        } else {
                            $("#feedback").text(response.message);
                        }
                    }
                });
            });

            table.on('click', '#edit', function(e) {
                var data = table.row($(this).parents('tr')).data();

                $('#myFormTitle').text("Edit Data");
                $('#room_id').val(data.room_id).prop('disabled', true);
                $('#lantai').val(data.lantai);
                $('#jenis_kamar').val(data.jenis_kamar);
                $('#status_kamar').val(data.status_kamar);
                $('#keterangan').val(data.keterangan);
                $('#feedback').text("");
                $('#myForm').modal('show');
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
        $sqlCount = "SELECT count(*) FROM room;";

        //untuk mengembalikan data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT room_id, lantai, jenis_kamar, status_kamar, keterangan FROM room WHERE room_id LIKE :search LIMIT $length OFFSET $start";

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
            $sql = "INSERT into room(room_id, lantai, jenis_kamar, status_kamar, keterangan) VALUES (:room_id, :lantai, :jenis_kamar, :status_kamar, :keterangan);";

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
            $sql = "SELECT room_id, lantai, jenis_kamar, status_kamar, keterangan FROM room WHERE room_id=:room_id;";
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
            error_log("jenis_kamar: " . $data["jenis_kamar"]);
            $sql = "UPDATE room SET lantai = :lantai, jenis_kamar = :jenis_kamar, status_kamar = :status_kamar, keterangan = :keterangan WHERE room_id=:room_id;";
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