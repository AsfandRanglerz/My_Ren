@extends('admin.layout.app')
@section('title', 'Points Details')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- Card Header -->
                            <div class="card-header">
                                <h4> {{ $data->name ?? 'N/A' }} - Installations Detail</h4>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body table-striped table-bordered table-responsive">
                                <!-- Data Table -->
                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Product</th>
                                            <th>Code</th>
                                            <th>Points Per Sale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->sales as $sale)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>


                                                {{-- ✅ Product ka naam --}}
                                                <td>{{ $sale->product->name ?? 'N/A' }}</td>

                                                {{-- ✅ Scan Code --}}
                                                <td>{{ $sale->scan_code }}</td>

                                                {{-- ✅ Points Earned --}}
                                                <td>{{ $sale->points_earned }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.section-body -->
        </section>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#table_id_events').DataTable();
        });
    </script>
@endsection
