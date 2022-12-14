<html>

<head>
    <title>Download Data Scanlog Pegawai</title>
    <!-- MULAI STYLE CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css"
        integrity="sha256-pODNVtK3uOhL8FUNWWvFQK0QoQoV3YA9wGGng6mbZ0E=" crossorigin="anonymous" />

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
</head>

<body>
    <div class="container" style="padding-top: 50px">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Data Scanlog Pegawai</h3>
            </div>
            <div class="card-body">
                <!-- MULAI DATE RANGE PICKER -->
                <div class="row input-daterange">
                    <div class="col-md-4">
                        <input type="text" name="from_date" id="from_date" class="form-control"
                            placeholder="From Date" readonly />
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date"
                            readonly />
                    </div>
                    <div class="col-md-4">
                        <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                        <button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
                    </div>
                </div>
                <!-- AKHIR DATE RANGE PICKER -->
                <br>
                <a href="javascript:void(0)" class="btn btn-info" id="download-data">Download Data</a>
                <br><br>
                <table id="scanlog" class="table table-striped table-bordered table-sm" style="width:100%">
                    <thead>
                        <tr>
                            <th>PIN</th>
                            <th>Name</th>
                            <th>Scan Date</th>
                            <th>Scan Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scan_logs as $scanlog)
                            <tr>
                                <td>{{ $scanlog->pin }}</td>
                                <td>{{ $scanlog->name }}</td>
                                <td>{{ $scanlog->scan_date }}</td>
                                <td>{{ $scanlog->scan_time }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>

    <script>
        $(document).ready(function() {

            // load_data();

            $('.input-daterange').datepicker({
                todayBtn: 'linked',
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            $('#filter').click(function() {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                if (from_date != '' && to_date != '') {
                    $('#scanlog').DataTable().destroy();
                    load_data(from_date, to_date);
                } else {
                    alert('Both Date is required');
                }
            });
            $('#refresh').click(function() {
                $('#from_date').val('');
                $('#to_date').val('');
                $('#scanlog').DataTable().destroy();
                load_data();
            });

            // function load_data(from_date = '', to_date = '') {
            //     $('#scanlog').DataTable({
            //         processing: true,
            //         serverSide: true,
            //         ajax: {
            //             url: "{{ route('scanlog.index') }}",
            //             type: 'GET',
            //             data: {
            //                 from_date: from_date,
            //                 to_date: to_date
            //             }
            //         },
            //     });
            // }

        });
    </script>
</body>

</html>
