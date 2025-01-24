@extends('layouts.app')
@section('head')
@endsection
@section('content')
    <section class="pricing-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title text-center">
                        <h2>{{ setting_item_with_lang('user_plans_page_title', app()->getLocale()) ?? __("Pricing Packages")}}</h2>
                        <div class="my-3">
                            Thank you for making a booking at Virtuard
                            {{-- @include('admin.message') --}}
                        </div>
                        <p class="text-center">
                            <a href="{{route('user.booking_history')}}" class="btn btn-primary">{{__('Booking History')}}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
