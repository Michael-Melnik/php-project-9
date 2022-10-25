@extends('layouts.app')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Сайты</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Последняя проверка</th>
                    <th>Код ответа</th>
                </tr>
                @foreach ($urls as $url)

                    <tr>
                        <td>{{ $url->id }}</td>
                        <td><a href="{{ route('url.show', ['id' => $url->id]) }}">{{ $url->name }}</a></td>
                        <td>{{ $lastChek[$url->id]->created_at ?? '' }}</td>
                        <td>{{ $lastChek[$url->id]->status_code ?? '' }}</td>
                    </tr>
                @endforeach
            </table>
            <nav>
                <ul class="pagination">
                    <li>{{ $urls->links() }}</li>
                </ul>
            </nav>
        </div>
    </div>
@endsection

