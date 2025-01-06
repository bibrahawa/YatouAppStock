@extends('app')

@section('contentheader')
@stop

@section('title')
    @if($subcategory->id)
        {{ trans('core.editing') }} {{ $subcategory->name }}
    @else
        {{ trans('core.add_new_subcategory') }}
    @endif
@stop

@section('main-content')

    <div class="panel-body">

        <h3 class="title-hero">
            @if($subcategory->id)
                {{ trans('core.editing') }} {{ $subcategory->name }}
            @else
                {{ trans('core.add_new_subcategory') }}
            @endif
        </h3>

        <form method="POST" action="{{ $subcategory->id ? route('subcategories.update', $subcategory->id) : route('subcategories.store') }}" enctype="multipart/form-data" class="form-horizontal bordered-row" id="ism_form">
            @csrf
            @if($subcategory->id)
                @method('PUT')
                <input type="hidden" name="subcategory_id" value="{{ $subcategory->id }}">
            @endif

            <div class="form-group">
                <label class="col-sm-3 control-label">
                    {{ trans('core.subcategory_name') }}
                    <span class="required">*</span>
                </label>
                <div class="col-sm-6">
                    <input type="text" name="name" value="{{ old('name', $subcategory->name) }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" title="{{ trans('core.category_info') }}">
                    {{ trans('core.category_name') }}
                    <span class="required">*</span>
                </label>
                <div class="col-sm-6">
                    <select name="category_id" class="form-control selectpicker" title="Please select a category" data-live-search="true">
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ $subcategory->category_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-default content-box text-center pad20A mrg25T">
                <input type="submit" class="btn btn-lg btn-primary" id="submitButton" value="{{ trans('core.save') }}" onclick="submitted()">
            </div>

        </form>
    </div>
@stop
