@extends('layouts.app')

@section('title', 'Изображение')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.css') }}">

    <style>
        .js-dataTable-full .btn {
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="block">

        <div class="block-content block-content-full">
            <div class="table-responsive">
                <table class="table table-stripped table-bordered table-vcenter js-dataTable-full">
                    <thead>
                    <tr>
                        <th class="text-center">Имя</th>
                        <th class="text-center">Изображение</th>
                        <th class="text-center">Действия</th>
                    </tr>
                    </thead>
                    <tbody>

                        @foreach($photos as $photo)
                            <tr>

                                <td class="text-center font-w600">{{ $photo['filename'] }} </td>
                                <td class="text-center font-w600"><img src="/bot/{{$photo['basename']}}" width="100px"></td>
                                <td class="text-center font-w600 d-flex align-items-center justify-content-center">
                                    <a href="{{ route('admin.photos.edit', $photo['filename']) }}" class="btn btn-sm btn-alt-info mr-10" data-toggle="tooltip"
                                       title="Редактировать"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        jQuery('.js-dataTable-full').dataTable({
            "order": [],
            pageLength: 10,
            lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
            autoWidth: true,
            language: ru_datatable
        });
    </script>
@endsection
