@extends('app')

@section('title')
    {{ trans('core.damage_product_list') }}
@stop

@section('contentheader')
    {{ trans('core.damage_product_list') }}
@stop

@section('breadcrumb')
    {{ trans('core.damage_product_list') }}
@stop

@section('main-content')
    <div class="panel-heading">
        @if(auth()->user()->can('product.create'))
            <a href="{{ route('damage.new') }}" class="btn btn-success btn-alt btn-xs" style="border-radius: 0px !important;">
                <i class='fa fa-plus'></i>
                {{ trans('core.add_damage_item') }}
            </a>
        @endif

        @if(count(Request::input()))
            <span class="pull-right">
                <a class="btn btn-alt btn-default font-black btn-xs" href="{{ route('product.index') }}">
                    <i class="fa fa-eraser"></i>
                    {{ trans('core.clear') }}
                </a>

                <a class="btn btn-primary btn-alt btn-xs" id="searchButton">
                    <i class="fa fa-search"></i>
                    {{ trans('core.modify_search') }}
                </a>
            </span>
        @else
            <span class="pull-right">
                <a class="btn btn-primary btn-alt btn-xs " id="searchButton">
                    <i class="fa fa-search"></i>
                    {{ trans('core.search') }}
                </a>
            </span>
        @endif
    </div>

    <div class="panel-body">
        <div class="table-responsive" style="min-height: 300px;">
            <table class="table table-bordered table-striped">
                <thead class="{{ settings('theme') }}">
                    <td class="text-center font-white">{{ trans('core.name') }}</td>
                    <td class="text-center font-white">{{ trans('core.quantity') }}</td>
                    <td class="text-center font-white">{{ trans('core.note') }}</td>
                    <td class="text-center font-white">{{ trans('core.date') }}</td>
                </thead>

                <tbody>
                    @foreach($damaged_products as $product)
                        <tr>
                            <td class="text-center">
                                {{ $product->product->name }}
                            </td>
                            <td class="text-center">
                                {{ $product->quantity }}
                            </td>

                            <td class="text-center">
                                @if($product->note)
                                    {{ $product->note }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="text-center">{{ carbonDate($product->date, '') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!--Pagination-->
        <div class="pull-right">
            {{ $damaged_products->links() }}
        </div>
        <!--Ends-->
    </div>

    <!-- Product search modal -->
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal" method="GET" action="{{ route('product.index') }}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"> {{ trans('core.search').' '.trans('core.product') }}</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="col-sm-3">{{ trans('core.name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" value="{{ Request::get('name') }}" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="code" class="col-sm-3">{{ trans('core.product_code') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="code" value="{{ Request::get('code') }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('core.close') }}</button>
                        <button type="submit" class="btn btn-primary" data-disable-with="{{ trans('core.searching') }}">{{ trans('core.search') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- search modal ends -->

@stop

@section('js')
    @parent
    <script>
        $(function() {
            $('#searchButton').click(function(event) {
                event.preventDefault();
                $('#searchModal').modal('show')
            });
        });

        $(function() {
            $('.number').on('input', function() {
                match = (/(\d{0,100})[^.]*((?:\.\d{0,5})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
                this.value = match[1] + match[2];
            });
        });
    </script>
@stop
