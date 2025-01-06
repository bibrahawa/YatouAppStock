@extends('app')

@section('title')
	Product Stock Alert
@stop

@section('contentheader')
    {{trans('core.alert_title')}}
    {{bangla_digit(count($alert_products))}}
    {{trans('core.entity')}}
    {{trans('core.product')}}
@stop

@section('breadcrumb')
	Product Stock Alert
@stop

@section('main-content')
	<div class="panel-body">
		<table class="table table-bordered">
			<thead class="{{settings('theme')}}">
				<th class="text-center">{{trans('core.product_name')}}</th>
				<th class="text-center">{{trans('core.in_stock')}}</th>
				<th class="text-center">{{trans('core.alert_quantity')}}</th>
			</thead>

			<tbody style="background-color: #fff;" id="myTable">
				@foreach($alert_products as $product)
					<tr>
						@foreach($product as $item)
							<td class="text-center">{{$item}}</td>
						@endforeach
					</tr>
				@endforeach	
			</tbody>
		</table>
	</div>
@stop

@section('js')
    @parent
    <script>
        
    </script>
@stop