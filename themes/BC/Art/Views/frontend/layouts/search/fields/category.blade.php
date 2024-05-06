<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label> Category </label>
        @php
            $categories = \Modules\Art\Models\ArtCategory::where('status', 'publish')->get();
        @endphp
        <div class="smart-search smart-search-category">
            <select name="category_id" class="form-control" style="width: 100%;">
                <option value="">-- Select Categoty --</option>
                @foreach ($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>