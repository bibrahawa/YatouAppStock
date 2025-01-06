@extends('app')

@section('title')
	Advance Option
@stop

@section('contentheader')
	Advance Option
@stop

@section('breadcrumb')
	Advance Option
@stop


@section('main-content')
    <div class="panel-heading" >
        <h6 style="color: red;">*Please be sure you want to run this action or not. Refresh & seed action will erase all your data from database.</h6>
    </div> 
   
    <div class="panel-body">
        <form method="post">
        	{{ csrf_field() }}
		  <div class="form-group">
		    <label for="exampleInputEmail1">Select Action</label>
		    <select class="form-control" name="action">
		    	<option value="all">Refresh & Seed</option>
		    	<option value="only-migrate">Migrate Only</option>
		    	<option value="only-seed">Seed Only</option>
		    </select>
		  </div>
		  <div class="form-group">
		    <label for="exampleInputPassword1">Password</label>
		    <input type="password" name="password" class="form-control" placeholder="Enter Password" required="true">
		  </div>
		  <button type="submit" class="btn btn-default">Submit</button>
		</form>
    </div>
@stop

@section('js')
    @parent
    <script>
        
    </script>

@stop