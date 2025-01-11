<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte General</title>

    <style>

        * {
            font-family: system-ui,sans-serif;
            font-size: 12px;
        }

        .tabla  {
            width: 100%;
        }

        .tabla th,td {
            padding: 8px 10px;
            text-align: left;
        }

        .tabla thead th {
            border-bottom: 1px solid black;
        }

        .tabla tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
        }

    </style>
</head>
<body>

<h1 class="title">Reporte General</h1>

<table class="tabla">
    <thead>
        <tr>
            <th>Monto</th>
            <th>Descripción</th>
            <th>Concepto</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ingresos as $ingreso)
            <tr>
                <td>{{ $ingreso->Monto  }}</td>
                <td>{{ $ingreso->Descripcion  }}</td>
                <td>{{ $ingreso->Concepto  }}</td>
                <td style="white-space: nowrap;">{{ $ingreso->Fecha  }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h2 class="title">Egresos</h2>
<table class="tabla">
    <thead>
    <tr>
        <th>Monto</th>
        <th>Interes</th>
        <th>Fecha de Pago</th>
        <th>Acreditor</th>
        <th>Concepto</th>
    </tr>
    </thead>
    <tbody>
    @foreach($deudas as $deuda)
        <tr>
            <td>{{ $deuda->monto  }}</td>
            <td>{{ $deuda->interes  }}</td>
            <td>{{ $deuda->fecha_de_pago  }}</td>
            <td>{{ $deuda->acreditor  }}</td>
            <td>{{ $deuda->concepto  }}</td>
            <td style="white-space: nowrap;">{{ $deuda->Fecha  }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
