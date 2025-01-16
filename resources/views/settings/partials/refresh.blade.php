@extends('app')

@section('title', 'Advance Option')

@section('contentheader', 'Advance Option')

@section('breadcrumb', 'Advance Option')

@section('main-content')
    <div class="panel-heading">
        <h6 style="color: red;">*Please be sure you want to run this action or not. Refresh & seed action will erase all your data from the database.</h6>
    </div>

    <div class="panel-body">
        <form method="post" action="{{ route('your.route.name') }}">
            @csrf
            <div class="form-group">
                <label for="action">Select Action</label>
                <select class="form-control" name="action" id="action">
                    <option value="all">Refresh & Seed</option>
                    <option value="only-migrate">Migrate Only</option>
                    <option value="only-seed">Seed Only</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
@stop

@section('js')
    @parent
    <script>
        // Add your JavaScript here if needed
    </script>
@stop
