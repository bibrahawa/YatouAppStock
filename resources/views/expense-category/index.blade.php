@extends('app')

@section('contentheader')
	Expense Categories
@stop

@section('breadcrumb')
	Expense Categories
@stop

@section('main-content')

    <div class="panel-heading">
		@if(auth()->user()->can('expense.create'))
			<a id="addButton" class="btn btn-success btn-alt btn-xs" style="border-radius: 0px !important;">
				<i class='fa fa-plus'></i> 
				Create New Expense Category
			</a>
		@endif

        <!--advance search-->
        @if(count(Request::input()))
            <span class="pull-right">   
                <a class="btn btn-default btn-alt btn-xs" href="{{ action('ExpenseController@getIndex') }}">
                    <i class="fa fa-eraser"></i> 
                    {{ trans('core.clear') }}
                </a>

                <a class="btn btn-primary btn-alt btn-xs" id="searchButton">
                    <i class="fa fa-search"></i> 
                    {{ trans('core.modify_search') }}
                </a>
            </span>
        @else
            <a class="btn btn-primary btn-alt btn-xs pull-right" id="searchButton">
                <i class="fa fa-search"></i> 
                {{ trans('core.search') }}
            </a>
        @endif
        <!--ends-->
    </div>
	
    <div class="panel-body">
        <div class="table-responsive">
    		<table class="table table-bordered table-striped" id="example">
    			<thead class="{{settings('theme')}}">
    				<td class="text-center font-white"># &nbsp;&nbsp;</td>
    				<td class="text-center font-white">{{trans('core.name')}}</td>
                    <td class="text-center font-white">Amount</td>
    				<td class="text-center font-white">{{trans('core.actions')}}</td>
    			</thead>

    			<tbody>
    				@foreach($categories as $category)
    					<tr>
    						<td class="text-center">{{$loop->iteration}}</td>
    						<td class="text-center">{{ $category->name }}</td>
                            <td class="text-center">{{$category->expenses()->sum('amount')}}</td>
    						<td class="text-center">
    							@if(auth()->user()->can('expense.manage'))
                                <a href="#" 
                                    data-id="{{$category->id}}" 
                                    data-name="{{$category->name}}"
                                    class="btn btn-info btn-alt btn-xs btn-edit">
                                    <i class="fa fa-edit"></i>
                                    {{trans('core.edit')}}
                                </a>

                                <!--Expense Delete button trigger-->
                                <a href="#" 
                                    data-id="{{$category->id}}" 
                                    data-name="{{$category->name}}"  
                                    class="btn btn-danger btn-alt btn-xs btn-delete"
                                >
                                    <i class="fa fa-trash"></i>
                                    {{trans('core.delete')}}
                                </a>
                                @endif
                                <!--Delete button trigger ends-->
    						</td>
    					</tr>
    				@endforeach
    			</tbody>
    		</table>
        </div>

        <!--Pagination-->
        <div class="pull-right">
            {{ $categories->links() }}
        </div>
        <!--Ends-->
    </div>

	<!--Create Expense Modal -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['class' => '', 'id' => 'ism_form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> 
                        Create New Expense Category
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                		<label>Name</label>
                    	<input type="text" class="form-control" name="name" required>
                    </div>                                             
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                    	{{trans('core.close')}}
                    </button>
                    <input type="submit" class="btn btn-primary" id="submitButton" value="{{ trans('core.save') }}" onclick="submitted()">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- Create Expense modal ends -->


    <!-- Delete Modal Starts -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
      {!! Form::open(['route'=>'expense.category.delete','method'=>'POST']) !!}
      {!! Form::hidden('id',null,['id'=>'deleteExpenseInput']) !!}
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                Delete Expense 
                <span id="deleteExpenseName" ></span>
            </h4>
          </div>
          <div class="modal-body">
            <h3>Are you sure you want to delete this expense?</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
    <!-- Modal Ends -->

    <!-- Edit Modal Starts -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
      {!! Form::open(['route'=>'expense.category.edit','method'=>'POST']) !!}
      {!! Form::hidden('id',null,['id'=>'editExpenseInput']) !!}
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                Edit Expense Category
            </h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label>Expense Category Name</label>
                <input type="text" name="name" class="form-control" id="editName" required>
            </div> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-info">Update</button>
          </div>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
    <!-- Modal Ends -->   
@stop

@section('js')
	@parent
	<script>
		$(function() {
            $('#addButton').click(function(event) {
                event.preventDefault();
                $('#addModal').modal('show')
            });
        })

        $(function() {
            $('#searchButton').click(function(event) {
                event.preventDefault();
                $('#searchModal').modal('show')
            });
        })

        $(document).ready(function(){
            $('.btn-delete').on('click',function(e){
                e.preventDefault();
                $('#deleteModal').modal();
                $('#deleteExpenseInput').val($(this).attr('data-id'));
                $('#deleteExpenseName').val($(this).attr('data-name'));
            })
        });

         $(document).ready(function(){
            $('.btn-edit').on('click',function(e){
                e.preventDefault();
                $('#editModal').modal();
                $('#editExpenseInput').val($(this).attr('data-id'));
                $('#editName').val($(this).attr('data-name'));
            })
        });
	</script>
@stop