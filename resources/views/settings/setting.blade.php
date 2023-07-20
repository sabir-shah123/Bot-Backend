@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Settings', 'url' => '#']];
@endphp
@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    @include('htmls.form', $form_fields, [
                        'action' => route('setting.save'),
                        'method' => 'POST',
                    ])
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    <img src="{{ asset('crm.jpg') }}" alt="Logo" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <div class="card-body pt-5">
                    @php
                        $href = 'https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri=' . route('authorization.gohighlevel.callback') . '&client_id=' . setting('client_id') . '&scope=calendars.readonly campaigns.readonly contacts.write contacts.readonly locations.readonly calendars/events.readonly locations/customFields.readonly locations/customValues.write opportunities.readonly calendars/events.write opportunities.write users.readonly users.write locations/customFields.write';
                        $description = 'Connect to GoHighLevel';
                        if(is_connected()){
                            $description  = 'Already Connected! Want to change?';
                        }
                    @endphp
                    @include('htmls.elements.anchor', ['href' => $href,'description' => $description])
                </div>
            </div>
        </div>
    </div>

@endsection
