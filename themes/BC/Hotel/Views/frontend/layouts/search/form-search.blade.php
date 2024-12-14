<!--
<form action="{{ route('hotel.search') }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        {{-- <div class="row d-flex align-items-center">
            @php $hotel_search_fields = setting_item_array('hotel_search_fields');
            $hotel_search_fields = array_values(\Illuminate\Support\Arr::sort($hotel_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
            $hotel_search_fields[0]['title'] = 'Location';
            @endphp
            @if (!empty($hotel_search_fields))
                @foreach ($hotel_search_fields as $field)
                    @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Hotel::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Hotel::frontend.layouts.search-map.fields.location')
                            @break
                            @case ('date')
                                @include('Hotel::frontend.layouts.search.fields.category')
                                @break
                            @case ('guests')
                                @include('Hotel::frontend.layouts.search.fields.range')
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div> --}}
        
        <div class="row d-flex align-items-center">
            <div class="col-md-2 border-right">
                @include('Hotel::frontend.layouts.search.fields.service_name')
            </div>
            {{-- <div class="col-md-3 border-right">
                @include('partials.search.fields.location')
            </div> --}}
            <div class="col-md-2 border-right">
                @include('Hotel::frontend.layouts.search.fields.range')
            </div>
            {{-- <div class="col-md-3 border-right">
                @include('Hotel::frontend.layouts.search.fields.category')
            </div> --}}
            <div class="col-md-2 border-right">
                @include('partials.search.fields.ipanorama')
            </div>
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{ __('Search') }}</button>
    </div>
</form>

-->




<div class="search-form p-3 bg-white shadow rounded">
    <form method="GET" action="{{ route('hotel.search') }}">
        <div class="row align-items-center">
            <div class="col-md-2">
                <label for="service_name" class="form-label">{{ __('Service Name') }}</label>
                <input type="text" class="form-control" name="service_name" placeholder="Service name">
            </div>
            <div class="col-md-2">
                @include('partials.search.fields.location')
            </div>
            <div class="col-md-2">
                <label for="date_range" class="form-label">{{ __('Check In - Check Out') }}</label>
                <div class="input-group">
                    <input type="text" id="check_in_date" name="check_in" class="form-control" placeholder="In"
                        readonly>
                    <input type="text" id="check_out_date" name="check_out" class="form-control"
                        placeholder="Out" readonly>
                </div>
            </div>

            <div class="col-md-2">
                <label for="guests" class="form-label">{{ __('Guests Adults & Child') }}</label>
                <div class="d-flex gap-3">
                    <div class="w-50">
                        <input type="number" id="adults" name="adults" class="form-control" placeholder="Adults" min="1" value="1">
                    </div>
            
                    <div class="w-50">
                        <input type="number" id="children" name="children" class="form-control" placeholder="Children" min="0" value="0">
                    </div>
                </div>
            </div>
            
            
            

            <div class="col-md-2 ">

                @include('Hotel::frontend.layouts.search.fields.range')
            </div>
            
            <div class="col-md-2 ">
                @include('partials.search.fields.ipanorama')
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> {{ __('Search') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#check_in_date', {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                let checkOutPicker = flatpickr('#check_out_date', {
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d",
                    minDate: selectedDates[0],
                });
                checkOutPicker.open(); 
            },
        });
    });
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    .search-form {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
    }

    .search-form .form-label {
        font-weight: 600;
        color: #333333;
    }

    .search-form .form-control {
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #1ba0e2;
        border: none;
    }

    .btn-primary:hover {
        background-color: #1482ba;
    }

    .form-control:focus {
        border-color: #1ba0e2;
        box-shadow: 0 0 5px rgba(27, 160, 226, 0.5);
    }
</style>
