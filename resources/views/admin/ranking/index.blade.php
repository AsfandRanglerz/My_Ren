@extends('admin.layout.app')
@section('title', 'Rankings')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Rankings</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                <table class="table " id="table_id_events table_id_rankings">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>User ID</th>
                                            <th>User Name</th>

                                            <th>Installs(Products)</th>
                                            <th>Points</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rankings as $index => $ranking)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $ranking->user_id }}</td>
                                                <td>{{ $ranking->name }}</td>
                                                <td>{{ $ranking->products_count }}</td>
                                                <td>{{ $ranking->points }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>

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
    <script type="text/javascript">
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#table_id_rankings')) {
                $('#table_id_rankings').DataTable().destroy();
            }
            $('#table_id_rankings').DataTable();
        });
    </script>
@endsection
