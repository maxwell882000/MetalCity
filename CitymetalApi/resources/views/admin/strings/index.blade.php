@extends('layouts.app')

@section('title', 'Текст')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.css') }}">

    <style>
        .js-dataTable-full .btn {
            height: 100%;
        }

        .js-dataTable-full-uz .btn {
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="col-lg-12">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="ru-tab" data-toggle="tab" href="#ru" role="tab"
                   aria-controls="ru" aria-selected="true">Ru</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="uz-tab" data-toggle="tab" href="#uz" role="tab"
                   aria-controls="uz" aria-selected="false">Uz</a>
            </li>
        </ul>
    </div>


    <div class="block">

        <div class="form-body">
            <div class="tab-content col-12" id="myTabContent">
                {{-- ru --}}
                <div class="tab-pane fade show active" id="ru" role="tabpanel" aria-labelledby="ru-tab">
                    <div class="block-content block-content-full">
                        <div class="table-responsive">
                            <table class="table table-stripped table-bordered table-vcenter js-dataTable-full">

                                <thead>
                                <tr>
                                    <th class="text-center">Строки</th>
                                    <th class="text-center">Действия</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($strings_ru as  $key =>$string)
                                    <tr>
                                        <td class="text-center font-w600">{!! $string !!} </td>
                                        <td class="text-center font-w600 d-flex align-items-center justify-content-center">
                                            <a href="{{ route('admin.strings.edit', 'ru.'.$key) }}"
                                               class="btn btn-sm btn-alt-info mr-10" data-toggle="tooltip"
                                               title="Редактировать"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- uz --}}
                <div class="tab-pane fade" id="uz" role="tabpanel" aria-labelledby="uz-tab">
                    <div class="block-content block-content-full">
                        <div class="table-responsive">
                        <table class="table table-stripped table-bordered table-vcenter js-dataTable-full-uz">
                        <thead>
                        <tr>
                            <th class="text-center">Строки</th>
                            <th class="text-center">Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($strings_uz as  $key =>$string)
                            <tr>
                                <td class="text-center font-w600">{!! $string !!} </td>
                                <td class="text-center font-w600 d-flex align-items-center justify-content-center">
                                    <a href="{{ route('admin.strings.edit', 'uz.'.$key) }}" class="btn btn-sm btn-alt-info mr-10" data-toggle="tooltip"
                                       title="Редактировать"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                        </div>
                    </div>
                </div>

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
        jQuery('.js-dataTable-full-uz').dataTable({
            "order": [],
            pageLength: 10,
            lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
            autoWidth: true,
            language: ru_datatable
        });
    </script>
@endsection
