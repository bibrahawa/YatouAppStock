@extends('app')

@section('title')
    @if($client->id)
        Editing {{$client->name}}
    @else
        {{trans('core.add_new_customer')}}
    @endif
@stop

@section('contentheader')
@stop

@section('breadcrumb')
    <a href="{{route('client.index')}}">
        {{trans('core.customer_list')}}
    </a>
    <li>{{trans('core.add_new_customer')}}</li>
@stop

@section('main-content')

    <div class="panel-body">

        <h3 class="title-hero">
            @if($client->id)
                Editing <b>{{$client->name}}</b>
            @else
                {{trans('core.add_new_customer')}}
            @endif
        </h3>

        <div class="example-box-wrapper">
            <form action="{{ route('client.save') }}" method="post" enctype="multipart/form-data" class="form-horizontal bordered-row" id="ism_form">
                @csrf

                <input type="hidden" name="id" value="{{ $client->id }}">
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.first_name') }}<span class="required">*</span></label>
                    <div class="col-sm-6">
                        <input type="text" name="first_name" value="{{ $client->first_name }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.last_name') }}</label>
                    <div class="col-sm-6">
                        <input type="text" name="last_name" value="{{ $client->last_name }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.email') }}</label>
                    <div class="col-sm-6">
                        <input type="text" name="email" value="{{ $client->email }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.phone') }}<span class="required">*</span></label>
                    <div class="col-sm-6">
                        <input type="text" name="phone" value="{{ $client->phone }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.address') }}<span class="required">*</span></label>
                    <div class="col-sm-6">
                        <textarea name="address" class="form-control" rows="3">{{ $client->address }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ trans('core.company_name') }}</label>
                    <div class="col-sm-6">
                        <input type="text" name="company_name" value="{{ $client->company_name }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        {{ trans('core.account_no') }}
                    </label>
                    <div class="col-sm-6">
                        <input type="text" name="account_no" value="{{ $client->account_no }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">
                        {{ trans('core.previous_due') }}
                    </label>
                    <div class="col-sm-6">
                        <input type="text" name="previous_due" value="{{ $client->previous_due }}" class="form-control" onkeypress="return event.charCode <= 57 && event.charCode != 32">
                    </div>
                </div>

                @if($client->client_type != 'purchaser')
                    <input type="hidden" name="client_type" value="customer">
                @else
                    <input type="hidden" name="client_type" value="purchaser">
                @endif

            </form>
        </div>

        <div class="bg-default content-box text-center pad20A mrg25T">
            <input type="submit" class="btn btn-lg btn-primary" id="submitButton" value="{{ trans('core.save') }}" onclick="submitted()">
        </div>

    </div>

@stop
