@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>
    <h2>Tus Grafos</h2>
    <ul>
        @foreach($grafos as $grafo)
            <li>{{ $grafo->query }} - {{ $grafo->created_at }}</li>
        @endforeach
    </ul>
    <a href="{{ route('generar-grafo') }}" class="btn btn-primary">Generar Nuevo Grafo</a>
</div>
@endsection
