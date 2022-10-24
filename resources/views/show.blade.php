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
        <h2 class="mt-5 mb-3">Проверки</h2>
        <form method="post" action="{{route('url.check', $url->id)}}">
            @csrf
            <input type="submit" class="btn btn-primary" value="Запустить проверку">
        </form>
        <table class="table table-bordered table-hover" data-test="checks">
            <tbody>
            <tr>
                <th>ID</th>
                <th>Код ответа</th>
                <th>h1</th>
                <th>title</th>
                <th>description</th>
                <th>Дата создания</th>
            </tr>
            @foreach($checks as $check)
                <tr>
                    <td>{{$check->id}}</td>
                    <td></td>
                    <td class="text-break"></td>
                    <td class="text-break"></td>
                    <td class="text-break"></td>
                    <td>{{$check->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </main>
@endsection
