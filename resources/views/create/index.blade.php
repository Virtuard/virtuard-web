@extends ('layouts.app')
@section('content')
    <div class="boards" style="background: #f5f5f5; padding: 120px 60px;">
        <div class="row mt-5">
            <div class="col-12">
                <h1 class="text-center mb-3">Apa yang ingin anda masukkan?</h1>
            </div>
        </div>
        <div class="row">
            @forelse ($menus as $key => $menu)
                <div class="col-4 mb-3">
                    <a href="{{ $menu['url'] }}" class="card h-100 text-decoration-none" @guest
                    data-toggle="modal"
                    data-target="#login"
                    @endguest>
                        <div class="card-body d-flex align-items-center">
                            <i class="fa fa-lg mr-2 {{ $menu['icon'] }}"></i>
                            {{ $menu['title'] }}
                        </div>
                    </a>
                </div>
            @empty
            @endforelse
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
