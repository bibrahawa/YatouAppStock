@extends('layouts.pos')

@section('title')
    Point of Sale
@endsection

@section('css')
    @vite(['resources/css/app.css'])
@endsection

@section('main-content')
    {{-- Div container pour React --}}
    <div id="pos-app" 
         data-customers="{{ json_encode($customers) }}"
         data-categories="{{ json_encode($categories) }}"
         data-settings="{{ json_encode([
             'invoice_tax' => settings('invoice_tax'),
             'invoice_tax_rate' => settings('invoice_tax_rate'),
             'invoice_tax_type' => settings('invoice_tax_type'),
             'theme' => settings('theme')
         ]) }}"
    ></div>
@endsection

@section('js')
    @vite(['resources/js/pos.js'])
@endsection