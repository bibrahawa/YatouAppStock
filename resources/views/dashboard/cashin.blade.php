@extends('app')

@section('contentheader')
    {{trans('core.total_received_today')}}
@stop

@section('breadcrumb')
    Total Received Today
@stop

@section('main-content')
    <div class="panel-body">
        <table class="table table-bordered">
            <thead class="{{settings('theme')}}">
                <td class="text-center font-white">{{trans('core.receipt_no')}}</td>
                <td class="text-center font-white">{{trans('core.time')}}</td>
                <td class="text-center font-white">{{trans('core.name')}}</td>
                <td class="text-center font-white">{{trans('core.note')}}</td>
                <td class="text-center font-white">{{trans('core.method')}}</td>
                <td class="text-center font-white">{{trans('core.amount')}}</td>
                <td class="text-center font-white">{{trans('core.print_receipt')}}</td>
            </thead>

            <tbody>
                @foreach($received_cash as $received_cash)
                    <tr>
                        <td class="text-center">#{{ref($received_cash->id)}}</td>
                        <td class="text-center">{{carbonDate($received_cash->created_at, 'time')}}</td>
                        <td class="text-center">{{$received_cash->client->name}}</td>
                        <td class="text-center">{{$received_cash->note}}</td>
                        <td class="text-center">
                            {{title_case($received_cash->method)}}
                        </td>
                        <td class="text-center">
                            {{settings('currency_code')}}
                            {{bangla_digit($received_cash->amount)}}
                        </td>
                        <td class="text-center">
                            <a target="_BLINK" href="{{route('payment.voucher', $received_cash)}}" class="btn btn-alt btn-warning btn-xs">
                                <i class="fa fa-print"></i>
                                {{trans('core.print')}}
                            </a>
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="7"></td>
                </tr>

                @if($totalCashPayment > 0)
                <tr  style="background-color: #FFF9F9;">
                    <td colspan="5" style="text-align: right;">
                        {{trans('core.total_cash_payment')}}
                    </td>
                    <td colspan="2" style="text-align: left;">
                        {{settings('currency_code')}}
                        {{bangla_digit($totalCashPayment)}}
                    </td>
                </tr>
                @endif

                @if($totalCardPayment > 0)
                <tr  style="background-color: #FFF9F9;">
                    <td colspan="5" style="text-align: right;">
                        {{trans('core.total_card_payment')}}
                    </td>
                    <td colspan="2" style="text-align: left;">
                        {{settings('currency_code')}}
                        {{bangla_digit($totalCardPayment)}}
                    </td>
                </tr>
                @endif

                @if($totalCashAndCardPayment > 0)
                <tr  style="background-color: #FFF9F9;">
                    <td colspan="5" style="text-align: right;">
                        {{trans('core.total_card_and_cash_payment')}}
                    </td>
                    <td colspan="2" style="text-align: left;">
                        {{settings('currency_code')}}
                        {{$totalCashAndCardPayment}}
                    </td>
                </tr>
                @endif

                @if($totalMobilePayment > 0)
                <tr  style="background-color: #FFF9F9;">
                    <td colspan="5" style="text-align: right;">
                        {{trans('core.total_mobile_payment')}}
                    </td>
                    <td colspan="2" style="text-align: left;">
                        {{settings('currency_code')}}
                        {{$totalMobilePayment}}
                    </td>
                </tr>
                @endif

                <tr  style="background-color: #F8FCD4;">
                    <td colspan="5" style="text-align: right;  font-size: 18px;">
                        <b>{{trans('core.total')}}</b>
                    </td>
                    <td colspan="2" style="text-align: left; font-size: 18px;">
                        <b>
                            {{settings('currency_code')}}
                            {{bangla_digit($total_received_amount)}}
                        <b>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="panel-footer">
        <span style="padding: 10px;">

        </span>

        <a class="btn btn-border btn-alt border-black font-black btn-xs pull-right" href="{{route('transactions.today')}}">
            <i class="fa fa-backward"></i>
            {{trans('core.back')}}
        </a>
    </div>
@stop
