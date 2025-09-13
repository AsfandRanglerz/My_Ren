@extends('admin.layout.app')
@section('title', 'Installations Detail')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- Card Header -->
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">{{ $data->name ?? 'N/A' }} - Installations Detail</h4>
                                <div class="text-end">
                                    <div class="bg-success px-3 py-2 rounded d-inline-block">
                                        <h6 class="mb-0 text-light">Total Points: {{ $totalPoints ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>



                            <!-- Card Body -->
                            <div class="card-body table-striped table-bordered table-responsive">
                                <!-- Data Table -->
                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Product</th>
                                            <th>SN Code</th>
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
