@extends('app')

@section('title')
    @if($purchaser->id)
        Editing <b>{{ $purchaser->name }}</b>
    @else
        {{ trans('core.add_new_supplier') }}
    @endif
@endsection

@section('contentheader')
@endsection

@section('breadcrumb')
    <a href="{{ route('purchaser.index') }}">{{ trans('core.supplier_list') }}</a>
    <li>{{ trans('core.add_new_supplier') }}</li>
@endsection

@section('main-content')

    <div class="panel-body">
        <h3 class="title-hero">
            @if($purchaser->id)
                Editing <b>{{ $purchaser->name }}</b>
            @else
                {{ trans('core.add_new_supplier') }}
            @endif
        </h3>

        <form method="POST" action="{{ route('purchaser.store') }}" enctype="multipart/form-data" class="form-horizontal bordered-row" id="ism_form">
            @csrf

            <input type="hidden" name="id" value="{{ $purchaser->id }}">

            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ trans('core.first_name') }} <span class="required">*</span></label>
                <div class="col-sm-6">
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $purchaser->first_name) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ trans('core.last_name') }}</label>
                <div class="col-sm-6">
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $purchaser->last_name) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ trans('core.company_name') }}</label>
                <div class="col-sm-6">
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $purchaser->company_name) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ trans('core.email') }} </label>
                <div class="col-sm-6">
                    <input type="email" name="email" class="form-control" value="{{ old('email', $purchaser->email) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ trans('core.phone') }} <span class="required">*</span></label>
                <div class="col-sm-6">
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $purchaser->phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ trans('core.address') }} <span class="required">*</span></label>
                <div class="col-sm-6">
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $purchaser->address) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">
                    {{ trans('core.account_no') }}
                </label>
                <div class="col-sm-6">
                    <input type="text" name="account_no" class="form-control" value="{{ old('account_no', $purchaser->account_no) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">
                    {{ trans('core.previous_due') }}
                </label>
                <div class="col-sm-6">
                    <input type="text" name="previous_due" class="form-control" value="{{ old('previous_due', $purchaser->previous_due) }}" onkeypress="return event.charCode <= 57 && event.charCode != 32">
                </div>
            </div>

            <div class="bg-default content-box text-center pad20A mrg25T">
                <input type="submit" class="btn btn-lg btn-primary" id="submitButton" value="{{ trans('core.save') }}" onclick="submitted()">
            </div>

        </form>

    </div>

@endsection
