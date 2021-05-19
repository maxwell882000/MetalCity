@extends('layouts.app')



@section('css')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2-bootstrap.min.css') }}">
@endsection

@section('content')
    <form action="{{ route('admin.strings.update', $string) }}" method="post" accept-charset="UTF-8">
        @csrf
        @method('put')
        <div class="block">

            <div class="block-content">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <h3 class="content-heading pt-0">Изменить </h3>

                    </div>
                </div>

                <textarea name="string" id="text" cols="30" rows="10" class="form-control raw-textarea">{!! $string_lang  !!}</textarea>

                <div class="block-content text-right pb-10">
                    <button class="btn btn-success" type="submit">Сохранить</button>
                </div>
                </div>

            </div>
        </div>
    </form>

@endsection

