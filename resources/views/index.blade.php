@extends('layouts.app')

@section('content')
    <div class="container-lg mt-3">
            <div class="row">
                <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
                    <h1 class="display-3">Анализатор страниц</h1>
                    <p class="lead">Бесплатно проверяйте сайты на SEO пригодность</p>
                    <form action="/urls" method="post" class="row">
                        @csrf
                        <div class="col-8">

                                <input type="text" name="url[name]" value="{{$url ?? ''}}" class="form-control form-control-lg @if(isset($validator)) is-invalid @endif" placeholder="https://www.example.com">
                                @if(isset($validator))
                                <div class="invalid-feedback">
                                    Некорректный URL
                                </div>
                                @endif
                        </div>
                        <div class="col-2">
                            <input type="submit" class="btn btn-primary btn-lg ms-3 px-5 text-uppercase mx-3" value="Проверить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

