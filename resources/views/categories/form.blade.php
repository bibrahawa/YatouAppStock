@extends('app')

@section('contentheader')
@stop

@section('breadcrumb')
    <a href="{{ route('category.index') }}">{{ trans('core.category_index') }}</a>
    <li>
        @if($category->id)
            {{ trans('core.editing') }} {{ $category->category_name }}
        @else
            {{ trans('core.add_new_category') }}
        @endif
    </li>
@stop

@section('main-content')
    <div class="panel-body">
        <h3 class="title-hero">
            @if($category->id)
                {{ trans('core.editing') }}
                {{ $category->category_name }}
            @else
                {{ trans('core.add_new_category') }}
            @endif
        </h3>
        <div class="example-box-wrapper">
            <form action="{{ $category->id ? route('category.update', $category->id) : route('category.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal bordered-row" id="ism_form">
            @csrf
            @if($category->id)
                @method('PUT')
            @endif

                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.category_name') }}</label>
                    <span class="required">*</span>
                    <div class="col-sm-6">
                        <input type="hidden" name="id" value="{{ $category->id }}">
                        <input type="text" name="name" value="{{ old('name', $category->category_name) }}" class="form-control">
                    </div>
                </div>

                <div class="bg-default content-box text-center pad20A mrg25T">
                    <input class="btn btn-lg btn-primary" type="submit" id="submitButton" value="{{ trans('core.save') }}" onclick="submitted()">
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    @parent

@stop
