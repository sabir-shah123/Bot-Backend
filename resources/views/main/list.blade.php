@php
    $breadcrumb = [['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => $page_route, 'url' => '#']];
@endphp
@extends('layouts.app')
@section('title', $page_title ?? 'No title')
@section('page-title', $page_title ?? 'No title')

@section('content')
    <div class="row">
        <div class="col-md-12 text-right py-2">
            <a href="{{ route($page_route . '.add') }}" class="btn btn-primary  py-3" style="float: right">Add
                {{ $page_title ?? 'Add New' }}</a>
        </div>
        <div class="col-md-12 mx-auto">
            <div id="expbuttons"></div>
            <div class="card card-xxl-stretch-50 mb-5 mb-xl-10">
                <!--begin::Body-->
                <div class="card-body pt-5">
                    <table id="kt_datatable" class="table table-row-bordered gy-5">
                        <thead>
                            <tr class="fw-semibold fs-6 text-muted">
                                <td class="text-start"> Id </td>
                                @foreach ($table_fields as $field)
                                    <td class="text-start">{{ $field }}</td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($table_data as $data)
                                <tr>
                                    <td class="text-start">{{ $loop->iteration }}</td>
                                    @foreach ($table_fields as $key => $value)
                                        <td class="text-start">
                                            @if ($key == 'action')
                                                @include('htmls.action', [
                                                    'action' => $actions,
                                                    'id' => $data->id ?? null,
                                                    'dropdown' => true,
                                                ])
                                            @else
                                                @if (checkIfHtml($data->$key))
                                                    {!! $data->$key !!}
                                                @else
                                                    {{ $data->$key }}
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Body-->
            </div>
        </div>

    @endsection

    @section('js')

    @endsection
