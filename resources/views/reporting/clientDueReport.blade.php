@extends('app')

@section('contentheader')
	Due Report
@stop

@section('breadcrumb')
	Due Report
@stop

@section('main-content')         

	<div class="panel-heading">
		<a href="#" class="btn btn-border btn-alt border-orange font-orange btn-xs " onclick="printDiv('printableArea')">
			<i class="fa fa-print"></i>
			{{trans('core.print')}}
		</a>
	</div>

	<div id="printableArea" class="panel-body">
		<h4 class="text-center">	
			<b>Due Report:</b>
			<br>
		 	{{carbonDate($from, 'y-m-d')}} 
		 	<b>{{trans('core.to')}}</b> 
		 	{{carbonDate($to, 'y-m-d')}} 
		 </h4><br />

		<table class="table table-bordered" width="100%">	
			<thead class="table-header-color">
				<th class="text-center" width="10%">SL No.</th>
				<th class="text-center" width="25%">{{trans('core.name')}}</th>
				<th class="text-center" width="20%">{{trans('core.total')}}</th>
				<th class="text-center" width="20%">{{trans('core.paid')}}</th>
				<th class="text-center" width="20%">{{trans('core.due')}}</th>
			</thead>

			@foreach($due_details as $due_detail)
				<tr>
					<td class="text-center">{{$loop->iteration}}</td>
					<td class="text-center">{{$due_detail['name']}}</td>
					<td class="text-center">
						{{settings('currency_code')}}
						{{$due_detail['net_total']}}
					</td>
					<td class="text-center">
						{{settings('currency_code')}} {{$due_detail['paid']}}
					</td>
					<td class="text-center">
						{{settings('currency_code')}} {{$due_detail['due']}}
					</td>
				</tr>
			@endforeach			
		</table>
	</div> <!-- printable area ends  -->

	<div class="panel-footer">
		<span style="padding: 10px;">
        
        </span>

		<a class="btn btn-border btn-alt border-black font-black btn-xs pull-right" href="{{route('report.index')}}">
	        <i class="fa fa-backward"></i> 
	        {{trans('core.back')}}
	    </a>
	</div>

@stop

@section('js')
	@parent
	<script>
		function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
	</script>
@stop