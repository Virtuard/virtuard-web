<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label> Category </label>
        @php
            $businessCategories = \Modules\Core\Models\Terms::query()
                ->select('id', 'name')
                ->whereHas('attribute', function($q){
                    $q->where('slug', 'business-type');
                })
                ->groupBy('name')
                ->orderBy('name')
                ->get();
        @endphp
        <div class="smart-search smart-search-category">
            <select name="term_id" class="form-control" style="width: 100%;">
                <option value="">-- {{ __('Select Category') }} --</option>
                @foreach ($businessCategories as $category)
                    <option value="{{$category->id}}" @if(request('term_id') == $category->id) selected @endif>{{$category->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>