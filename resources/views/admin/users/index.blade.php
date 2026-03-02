@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">

        <div class="card overflow-hiddenCoupons">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($sl=\App\HelperClass::indexNumberSerialization($users))
                        @foreach($users as $data)
                        <tr>
                            <td>{{$sl++}}</td>
                           <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{route('admin.edit', $data->id)}}" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    <form method="post" action="{{route('admin.delete', $data->id)}}">
                                        @csrf
                                        @method('delete')
                                    <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
                <div class="col-sm">
                    <div class="text-muted">
                        Showing <span class="fw-semibold">10</span> of <span class="fw-semibold">59</span> Results
                    </div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    <ul class="pagination  m-0">
                        <li class="page-item">
                            <a href="#" class="page-link"><i class='bx bx-left-arrow-alt'></i></a>
                        </li>
                        <li class="page-item active">
                            <a href="#" class="page-link">1</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link">2</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link">3</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link"><i class='bx bx-right-arrow-alt'></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div> <!-- end card -->

    </div>


@endsection
