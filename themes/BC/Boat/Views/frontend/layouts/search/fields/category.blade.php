<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label> Category </label>
        @php
            $boatCategories = \Modules\Core\Models\Terms::query()
                ->select('id', 'name')
                ->whereHas('attribute', function($q){
                    $q->where('slug', 'boat-type');
                })
                ->groupBy('name')
                ->orderBy('name')
                ->get();
        @endphp
        <div class="smart-search smart-search-category">
            <select name="terms[]" class="form-control" style="width: 100%;">
                <option value="">-- {{ __('Select Category') }} --</option>
                @foreach ($boatCategories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>