@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generar Grafo</h1>
    <form action="{{ route('guardar-grafo') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="query">Término de Búsqueda:</label>
            <input type="text" id="query" name="query" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Generar Grafo</button>
    </form>
</div>
@endsection
