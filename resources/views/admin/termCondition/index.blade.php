@extends('admin.layout.app')
@section('title', 'Terms & Conditions')
@section('content')


    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12">
                                    <h4>Terms & Conditions</h4>
                                </div>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">


                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Details</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>

                                            <td>
                                                @if ($data && $data->description)
                                                    {!! Str::limit(strip_tags($data->description), 200, '...') !!}
                                                @else
                                                    <p>No description available.</p>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-4">

                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Terms & Conditions') &&
                                                                $sideMenuPermissions['Terms & Conditions']->contains('edit')))
                                                        <a href="{{ url('admin/term-condition-edit') }}"
                                                            class="btn btn-primary"><span class="fa fa-edit"></a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

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

    <script>
        $(document).ready(function() {
            $('#table_id_events').DataTable()
        })
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Are you sure you want to delete this record?`,
                    text: "If you delete this, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>

@endsection
