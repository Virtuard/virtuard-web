@extends ('layouts.app')
@section('content')
    <div class="d-flex justify-content-center align-items-center" style="background: #f5f5f5;height: calc(100vh - 130px);">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-3">Apa yang ingin anda masukkan?</h1>
                @if (!Auth::check())
                    <div class="row">
                        <div class="col-4 mb-3">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa fa-lg mr-2 fa-shopping-bag"></i>
                                    Business
                                </div>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa fa-lg mr-2 fa-tree"></i>
                                    Natural and Landscapes
                                </div>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa fa-lg mr-2 fa-home"></i>
                                    Property
                                </div>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa fa-lg mr-2 fa-laptop"></i>
                                    Rendering and Arts
                                </div>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa fa-lg mr-2 fa-industry"></i>
                                    Accomodation
                                </div>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa-lg mr-2 icofont-ship"></i>
                                    Vehicles
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="#login" class="card h-100 text-decoration-none" data-toggle="modal" data-target="#login">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fa fa-lg mr-2 fa-leaf"></i>
                                    Cultural Heritage
                                </div>
                            </a>
                        </div>
                    </div> 
                @else
                    @php
                        $user = Auth::user()->role_id;
                    @endphp
                    @if ($user == 1)
                        <div class="row">
                            <div class="col-4 mb-3">
                                <a href="/admin/module/hotel/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-shopping-bag"></i>
                                        Business
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/admin/module/tour/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-tree"></i>
                                        Natural and Landscapes
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/admin/module/space/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-home"></i>
                                        Property
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/admin/module/flight/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-laptop"></i>
                                        Rendering and Art
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/admin/module/car/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-industry"></i>
                                        Accomodation
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/admin/module/boat/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa-lg mr-2 icofont-ship"></i>
                                        Vehicles
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="/admin/module/event/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-leaf"></i>
                                        Cultural Heritage
                                    </div>
                                </a>
                            </div>
                        </div>
                    @elseif ($user == 2 || $user == 3)
                        <div class="row">
                            <div class="col-4 mb-3">
                                <a href="/user/hotel/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-shopping-bag"></i>
                                        Business
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/user/tour/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-tree"></i>
                                        Natural and Landscapes
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/user/space/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-home"></i>
                                        Property
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/user/flight/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-laptop"></i>
                                        Rendering and Art
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/user/car/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-industry"></i>
                                        Accomodation
                                    </div>
                                </a>
                            </div>
                            <div class="col-4 mb-3">
                                <a href="/user/boat/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa-lg mr-2 icofont-ship"></i>
                                        Vehicles
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="/user/event/create" class="card h-100 text-decoration-none">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fa fa-lg mr-2 fa-leaf"></i>
                                        Cultural Heritage
                                    </div>
                                </a>
                            </div>
                        </div>
                    {{-- @elseif($user == 3)
                        <p class="text-center">Jika anda ingin membuat layanan, silahkan daftarkan diri anda sebagai Vendor <br> dengan menghubungi melakukan permintaan melalui: <a href="{{url('user/profile-setting')}}">dashboard</a></p> --}}
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
