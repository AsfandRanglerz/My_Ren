@extends('admin.layout.app')
@section('title', 'Points Deduction History')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Points Deduction History</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr role="row" class="odd">
                                            <th>Sr.</th>
                                            <th>User Id</th>
                                            <th>Admin</th>
                                            <th>Admin Type</th>
                                            <th>Deducted Points</th>
											<th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deductions as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $data->users->identity ?? "-" }}</td>
                                                <td>{{ $data->Admin_name }}</td>
												<td>{{ $data->Admin_type }}</td>
												<td>{{ $data->deducted_points }}</td>
												<td>{{ $data->date_time }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <!-- Initialize DataTable -->
    <script type="text/javascript">
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#table_id_events')) {
                $('#table_id_events').DataTable().destroy();
            }
            $('#table_id_events').DataTable();
        });

    </script>
@endsection
