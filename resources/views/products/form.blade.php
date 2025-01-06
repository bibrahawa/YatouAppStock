@extends('app')

@section('title')
    @if($product->id)
        {{trans('core.editing')}} {{$product->name}}
    @else
        {{trans('core.add_new_product')}}
    @endif
    || @parent
@stop

@section('contentheader')
@stop

@section('breadcrumb')
    <a href="{{route('product.index')}}">{{trans('core.product_list')}}</a>
    <li>
        @if($product->id)
            {{trans('core.editing')}} {{$product->name}}
        @else
            {{trans('core.add_new_product')}}
        @endif
    </li>
@stop

@section('main-content')

    <div class="panel-body">
        <h3 class="title-hero">
            @if($product->id)
                {{trans('core.editing')}} {{$product->name}}
            @else
                {{trans('core.add_new_product')}}
            @endif
        </h3>

        <div class="example-box-wrapper">
            <form method="post" action="{{ $product->id ? route('product.update', $product->id) : route('product.post') }}" enctype="multipart/form-data" class="form-horizontal bordered-row" id="ism_form">
                @csrf
                @if($product->id)
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        {{ trans('core.product_name')}}
                        <span class="required">*</span>
                    </label>

                    <div class="col-sm-4">
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" id="productName">
                    </div>

                    <label class="col-sm-2 control-label">
                        {{ trans('core.product_code')}}
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="code" value="{{ old('code', $product->code) }}" class="form-control" id="code">
                    </div>

                    <button
                        class="btn btn-info col-sm-1 tooltip-button"
                        type="button"
                        onclick="document.getElementById('code').value = generateCode()"
                        title="Click here to generate random code"
                        >
                        <i class="fa fa-random"></i>
                    </button>

                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"> {{ trans('core.category_name')}} <span class="required">*</span></label>
                    <div class="col-sm-4">
                        <select name="category_id" class="form-control selectpicker" id="category_id" data-live-search="true">
                            <option value="">{{ __('Please select a category') }}</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ $id == old('category_id', $product->category_id) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="col-sm-2 control-label">
                        {{ trans('core.subcategory_name')}}
                    </label>
                    <div class="col-sm-4">
                        <select name="subcategory_id" class="form-control selectpickerLive" id="subcategoryOptions" data-live-search="true">
                            <option value="">{{ __('Please select a Subcategory') }}</option>
                            @foreach($subcategories as $id => $name)
                                <option value="{{ $id }}" {{ $id == old('subcategory_id', $product->subcategory_id) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        {{ trans('core.cost_price')}}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-4">
                        <input type="text" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="form-control number">
                    </div>

                    <label class="col-sm-2 control-label">
                        {{ trans('core.mrp')}}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-4">
                        <input type="text" name="mrp" value="{{ old('mrp', $product->mrp) }}" class="form-control number">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" >
                        {{ trans('core.minimum_retails_price')}}
                    </label>
                    <div class="col-sm-4">
                        <input type="text" name="minimum_retail_price" value="{{ old('minimum_retail_price', $product->minimum_retail_price) }}" class="form-control popover-button-default number" data-content="Show this value when new sell only, no effect to any transaction" data-placement="bottom" data-trigger="focus">
                    </div>

                    @if(settings('product_tax') == 1)
                        <label class="col-sm-2 control-label">
                            {{ trans('core.product_tax')}}
                        </label>

                        <div class="col-sm-4">
                            <select name="tax_id" class="form-control selectpicker">
                                @foreach($taxes as $id => $name)
                                    <option value="{{ $id }}" {{ $id == old('tax_id') ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                    @else
                        <label class="col-sm-2 control-label">
                            {{ trans('core.product_tax')}}
                        </label>

                        <div class="col-sm-4 tooltip-button" title="To enable product tax, go to the settings">
                            <input type="text" disabled class="form-control" value="disabled">
                        </div>

                    @endif
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"> {{ trans('core.unit')}} </label>
                    <div class="col-sm-4">
                        <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="form-control">
                    </div>

                    <label for="featured" class="col-sm-2 control-label">
                        Status
                    </label>
                    <div class="col-sm-4 tooltip-button" title="Only active products shows in new sell & purchases">
                        <select name="status" class="form-control selectpickerLive" data-live-search="true">
                            <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label tooltip-button" title="Opening stock is the value of goods available for sale in the beginning of an accounting period">
                        {{ trans('core.opening_stocks')}}
                    </label>
                    <div class="col-sm-4">
                        <input type="text" name="opening_stock" value="{{ old('opening_stock', $product->opening_stock) }}" class="form-control number">
                    </div>

                    <label class="col-sm-2 control-label">
                        {{ trans('core.alert_range')}}
                    </label>
                    <div class="col-sm-4">
                        <input type="text" name="alert_quantity" value="{{ old('alert_quantity', $product->alert_quantity) }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        {{trans('core.image')}}
                    </label>
                    <div class="col-sm-4">
                        <input type="file" name="image">
                    </div>

                    @if(!empty($product->image))
                        <div class="col-sm-3" >
                            <p>
                                <a href="{{url('uploads/products/' . $product->image)}}">
                                    <abbr title="Show Product Image">
                                        <img src="{!! asset('uploads/products/'. $product->image)!!}"
                                            class="img-thumbnail img-responsive" alt="" >
                                    </abbr>
                                </a>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-default content-box text-center pad20A mrg25T">
                    <input type="submit" class="btn btn-lg btn-primary" id="submitButton" value="{{ trans('core.save') }}" onclick="submitted()">
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    @parent
     <script type="text/javascript">
        $(document).ready(function(){
            $('#category_id').on('change',function(){
                $('#subcategoryOptions').html('');
                var categoryID = $(this).val();
                if(categoryID){
                    $.ajax({
                        type:'get',
                        url:'ajaxData',
                        data:'categoryID='+categoryID,
                        success:function(html){
                            $('#subcategoryOptions').html(html);
                        }
                    });
                }
            });
        });

        /*generate random product code*/
        var productName = document.getElementById('productName');
        var randomNumber;
        productName.onkeyup = function(){
            randomNumber = productName.value.toUpperCase();
        }

        function generateCode() {
            if(randomNumber){
                return randomNumber.substring(0, 2) + (Math.floor(Math.random()*1000)+ 999);
            }else{
                return Math.floor(Math.random()*90000) + 100000;
            }
        }
        /*ends*/

        $(function() {
          $('.number').on('input', function() {
            match = (/(\d{0,100})[^.]*((?:\.\d{0,5})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
            this.value = match[1] + match[2];
          });
        });
    </script>
@stop
