@forelse($subcategory as $item)
    <option value="{{$item->id}}">{{$item->name}}</option>
@empty
    <option value="">No Data Available</option>
@endforelse
