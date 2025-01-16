@extends('app')

@section('title')
    {{trans('core.transaction_list')}}
@stop

@section('contentheader')
    {{trans('core.transaction_list')}}
@stop

@section('breadcrumb')
    {{trans('core.transaction_list')}}
@stop

@section('main-content')
    <div class="panel-heading" >
        <?php
            $url = \URL::full();
            $searchParam = "?";
            $parameterExists = strpos($url,$searchParam);
        ?>
            @if($parameterExists !== false)
                <a target="_blank" href="{{ \URL::full() }}&print=true" class="btn btn-warning btn-alt btn-xs"><i class="fa fa-print"></i> {!! trans('core.print') !!}</a>
            @else
                <a target="_blank" href="{{ \URL::full() }}?print=true" class="btn btn-warning btn-alt btn-xs"><i class="fa fa-print"></i> {!! trans('core.print') !!}</a>
        @endif

        @if(count(Request::input()))
        <span class="pull-right">
            <a class="btn btn-default btn-alt btn-xs" href="{{ route('payment.list') }}">
                <i class="fa fa-eraser"></i>
                {{ trans('core.clear') }}
            </a>

            <a class="btn btn-primary btn-alt btn-xs" id="searchButton">
                <i class="fa fa-search"></i>
                {{ trans('core.modify_search') }}
            </a>
        </span>
        @else
            <a class="btn btn-primary btn-alt btn-xs pull-right" id="searchButton" style="border-radius: 0px !important;" >
                <i class="fa fa-search"></i>
                {{ trans('core.search') }}
            </a>
        @endif

        <input type="button" class="btn btn-alt bg-purple btn-xs" onclick="showSummary()" id="summaryBtn" value="Summary">
    </div>

    <div class="panel-body">
        <div id="summaryDiv" style="display: none;">
            <table style="width: 100%;" class="table table-bordered" >
                <tr style="background-color: #F8F9F9; border: 1px solid #ddd;">
                    <td @if(!rtlLocale()) style="text-align: right;" @endif>
                        <b>{{trans('core.total_debit')}} :</b>
                    </td>
                    <td @if(rtlLocale()) style="text-align: right;" @endif>
                        {{settings('currency_code')}}
                        {{twoPlaceDecimal($total_debit)}}
                    </td>
                </tr>

                <tr style="background-color: #F8F9F9;border: 2px solid #ddd; ">
                    <td @if(!rtlLocale()) style="text-align: right;" @endif>
                        <b>{{trans('core.total_credit')}}</b>
                    </td>
                    <td @if(rtlLocale()) style="text-align: right;" @endif>
                        {{settings('currency_code')}}
                        {{twoPlaceDecimal($total_credit)}}
                    </td>
                </tr>

                @if($total_return > 0)
                <tr style="background-color: #F8F9F9;border: 2px solid #ddd; ">
                    <td @if(!rtlLocale()) style="text-align: right;" @endif>
                        <b>{{trans('core.total_return')}}</b>
                    </td>
                    <td @if(rtlLocale()) style="text-align: right;" @endif>
                        {{settings('currency_code')}}
                        {{twoPlaceDecimal($total_return)}}
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <div class="table-responsive" id="tableDIv">
            <table class="table table-bordered table-striped" >
                <thead class="{{settings('theme')}}">
                    <td class="text-center font-white">{{trans('core.date')}}</td>
                    <td class="text-center font-white">{{trans('core.receipt_no')}}</td>
                    <td class="text-center font-white">{{trans('core.invoice_no')}}</td>
                    <td class="text-center font-white">{{trans('core.name')}}</td>
                    <td class="text-center font-white">{{trans('core.amount')}}</td>
                    <td class="text-center font-white">{{trans('core.payment_method')}}</td>
                    <td class="text-center font-white">{{trans('core.type')}}</td>
                    <td class="text-center font-white">{{trans('core.print_receipt')}}</td>
                </thead>

                <tbody style="background-color: #fff;">
                    @foreach($payments as $payment)
                        <tr>
                            <td class="text-center tooltip-button" data-placement="bottom" title="{{ carbonDate($payment->date, 'g:i:a') }}">
                                {{carbonDate($payment->date, 'y-m-d')}}
                            </td>

                            <td class="text-center">#{{ref($payment->id)}}</td>

                            <td class="text-center">{{$payment->reference_no}}</td>


                            <td class="text-center">{{$payment->client->name}}</td>

                            <td class="text-center">
                                {{settings('currency_code')}}
                                {{twoPlaceDecimal($payment->amount)}}
                            </td>

                            <td class="text-center">
                                {{title_case($payment->method)}}
                            </td>

                            <td  class="text-center">
                                <span
                                @if($payment->type == 'debit')
                                    class="font-red"
                                @elseif($payment->type == 'return')
                                    class="font-orange"
                                @else
                                    class="font-green"
                                @endif>
                                {{title_case($payment->type)}}
                                </span>
                            </td>

                            <td class="text-center">
                                <a target="_BLINK" href="{{route('payment.voucher', $payment)}}" class="btn btn-border btn-alt border-orange btn-link font-orange btn-xs">
                                    <i class="fa fa-print"></i>
                                    {{trans('core.print')}}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!--Pagination-->
            <div class="pull-right">
                {{ $payments->links() }}
            </div>
            <!--Ends-->
        </div>
    </div>

    <!-- Transaction search modal -->
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> {{ trans('core.search').' '.trans('core.transaction') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.receipt_no')}}</label>
                        <div class="col-sm-9">
                            <input type="text" name="receipt_no" value="{{ Request::get('receipt_no') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.invoice_no')}}</label>
                        <div class="col-sm-9">
                            <input type="text" name="invoice_no" value="{{ Request::get('invoice_no') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.client')}}</label>
                        <div class="col-sm-9">
                            <select name="client" class="form-control selectpicker" data-live-search="true">
                                <option value="">{{ trans('core.please_select') }}</option>
                                @foreach($clients as $id => $name)
                                    <option value="{{ $id }}" {{ Request::get('client') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.type')}}</label>
                        <div class="col-sm-9">
                            <select name="type" class="form-control selectpicker">
                                <option value="">{{ trans('core.please_select') }}</option>
                                @foreach($type as $key => $value)
                                    <option value="{{ $key }}" {{ Request::get('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.payment_method')}}</label>
                        <div class="col-sm-9">
                            <select name="method" class="form-control selectpicker">
                                <option value="">{{ trans('core.please_select') }}</option>
                                @foreach($methods as $key => $value)
                                    <option value="{{ $key }}" {{ Request::get('method') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.from')}}</label>
                        <div class="col-sm-9">
                            <input type="text" name="from" value="{{ Request::get('from') }}" class="form-control dateTime" placeholder="yyyy-mm-dd">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" @if(!rtlLocale()) style="text-align: right;" @endif>{{trans('core.to')}}</label>
                        <div class="col-sm-9">
                            <input type="text" name="to" value="{{ Request::get('to') }}" class="form-control dateTime" placeholder="yyyy-mm-dd">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('core.close')}}</button>
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
        })

        function showSummary() {
            var x = document.getElementById("summaryDiv");
            var y = document.getElementById("tableDIv");
            var elem = document.getElementById("summaryBtn");
            if (elem.value=="Summary") elem.value = "Transaction List";
            else elem.value = "Summary";
            if (x.style.display === "none") {
                x.style.display = "block";
                y.style.display = "none";
            } else {
                x.style.display = "none";
                y.style.display = "block";
            }
        }
    </script>

@stop
