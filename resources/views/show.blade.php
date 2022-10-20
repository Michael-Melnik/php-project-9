@extends('layouts.app')
@section('content')
    @include('flash::message')
    <main class="flex-grow-1">
        <div class="container-lg">
            <h1 class="mt-5 mb-3">Сайт: {{ $url->name }}</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tbody><tr>
                        <td>ID</td>
                        <td>{{ $url->id }}</td>
                    </tr>
                    <tr>
                        <td>Имя</td>
                        <td>{{ $url->name }}</td>
                    </tr>
                    <tr>
                        <td>Дата создания</td>
                        <td>{{ $url->created_at }}</td>
                    </tr>
                    </tbody></table>
            </div>
        </div>
    </main>
@endsection
