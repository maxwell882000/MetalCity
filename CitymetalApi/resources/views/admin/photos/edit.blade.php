@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2-bootstrap.min.css') }}">
@endsection

@section('content')
    <form action="{{ route('admin.photos.update', $photo) }}" method="post" enctype="multipart/form-data"  accept-charset="UTF-8">
        @csrf
        @method('put')
        <div class="block">

            <div class="block-content">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <h3 class="content-heading pt-0">Изменить рисунок</h3>
                        <p>{{ $photo }}</p>
                    </div>
                </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <div class="form-material">
<img src="/bot/{{$photo}}.jpg" width="500px">

                            </div>
                        </div>
                        <input type="file" name="image">
                    </div>

                <div class="block-content text-right pb-10">
                    <button class="btn btn-success" type="submit">Сохранить</button>
                </div>
                </div>

            </div>
        </div>
    </form>

@endsection

