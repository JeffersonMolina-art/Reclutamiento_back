<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de postulaciones por estado</title>

    <style>
        @page {
            margin: 32pt 36pt 42pt;
        }

        body {
            margin: 0;
            color: #263238;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9pt;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .encabezado {
            padding-bottom: 10pt;
            border-bottom: 1pt solid #2c8362;
        }

        .area,
        .generacion,
        .total-etiqueta {
            color: #55616a;
            font-size: 8pt;
        }

        h1 {
            margin: 2pt 0 0;
            font-size: 17pt;
        }

        .generacion {
            margin: 4pt 0 0;
        }

        .filtros {
            margin: 10pt 0;
        }

        .filtros th,
        .filtros td {
            padding: 2pt 0;
            text-align: left;
            vertical-align: top;
        }

        .filtros th {
            width: 65pt;
        }

        .totales {
            margin-bottom: 12pt;
            border-top: 0.5pt solid #d8dde0;
            border-bottom: 0.5pt solid #d8dde0;
        }

        .totales td {
            padding: 5pt 0;
        }

        .total-etiqueta {
            display: block;
        }

        .total-valor {
            font-size: 12pt;
            font-weight: bold;
        }

        .vacante-encabezado {
            margin: 12pt 0 4pt;
            border-bottom: 0.5pt solid #d8dde0;
            page-break-after: avoid;
        }

        .vacante-encabezado td {
            padding: 4pt 0;
            vertical-align: middle;
        }

        .vacante-encabezado h2 {
            margin: 0;
            font-size: 11pt;
        }

        .vacante-total {
            width: 80pt;
            text-align: right;
            font-weight: bold;
        }

        .tabla-datos {
            margin-bottom: 9pt;
            table-layout: fixed;
        }

        .tabla-datos th,
        .tabla-datos td {
            padding: 4pt 6pt;
            border-bottom: 0.5pt solid #d8dde0;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        .tabla-datos th {
            background-color: #f3f4f5;
            font-size: 8pt;
        }

        .tabla-datos .centrado {
            text-align: center;
        }

        .tabla-resumen td {
            font-weight: bold;
        }

        .tabla-datos .estado-titulo {
            padding-top: 6pt;
            background-color: #fff;
            font-size: 9pt;
        }

        .columna-id {
            width: 35pt;
        }

        .columna-fecha {
            width: 65pt;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .vacio {
            padding: 16pt 0;
            color: #55616a;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="encabezado">
    <div class="area">Reclutamiento</div>
    <h1>Reporte de postulaciones por estado</h1>
    <p class="generacion">Generado por {{ $usuario }} el {{ $generado }}</p>
</div>

<table class="filtros">
    <tbody>
    <tr>
        <th scope="row">Vacante</th>
        <td>{{ $vacanteFiltrada ?: 'Todas las vacantes' }}</td>
    </tr>
    <tr>
        <th scope="row">Estados</th>
        <td>{{ $estadosFiltrados ?: 'Todos los estados' }}</td>
    </tr>
    <tr>
        <th scope="row">Período</th>
        <td>
            @if($desde && $hasta)
                Del {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}
                al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}
            @elseif($desde)
                Desde el {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}
            @elseif($hasta)
                Hasta el {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}
            @else
                Todas las fechas
            @endif
        </td>
    </tr>
    </tbody>
</table>

<table class="totales">
    <tbody>
    <tr>
        <td width="50%">
            <span class="total-etiqueta">Total de postulaciones</span>
            <span class="total-valor">{{ $total }}</span>
        </td>
        <td width="50%">
            <span class="total-etiqueta">Vacantes con postulaciones</span>
            <span class="total-valor">{{ count($vacantes) }}</span>
        </td>
    </tr>
    </tbody>
</table>

@forelse($vacantes as $vacante)
    <table class="vacante-encabezado">
        <tbody>
        <tr>
            <td><h2>{{ $vacante['nombre'] }}</h2></td>
            <td class="vacante-total">Total {{ $vacante['total'] }}</td>
        </tr>
        </tbody>
    </table>

    @if(!empty($vacante['resumen']))
        @foreach(array_chunk($vacante['resumen'], 6) as $bloqueResumen)
            <table class="tabla-datos tabla-resumen">
                <thead>
                <tr>
                    @foreach($bloqueResumen as $fila)
                        <th scope="col" class="centrado">{{ $fila['nombre'] }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr>
                    @foreach($bloqueResumen as $fila)
                        <td class="centrado">{{ $fila['cantidad'] }}</td>
                    @endforeach
                </tr>
                </tbody>
            </table>
        @endforeach
    @endif

    @foreach($vacante['estados'] as $estado)
        <table class="tabla-datos">
            <thead>
            <tr>
                <th colspan="4" class="estado-titulo">
                    {{ $estado['nombre'] }} ({{ count($estado['postulaciones']) }})
                </th>
            </tr>
            <tr>
                <th scope="col" class="columna-id centrado">ID</th>
                <th scope="col">Candidato</th>
                <th scope="col">Correo</th>
                <th scope="col" class="columna-fecha centrado">Fecha</th>
            </tr>
            </thead>
            <tbody>
            @foreach($estado['postulaciones'] as $postulacion)
                <tr>
                    <td class="centrado">{{ $postulacion['id'] }}</td>
                    <td>{{ $postulacion['candidato'] ?: 'Sin nombre' }}</td>
                    <td>{{ $postulacion['correo'] ?: '—' }}</td>
                    <td class="centrado">{{ $postulacion['fecha'] ?: '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
@empty
    <div class="vacio">
        No hay postulaciones para los filtros seleccionados.
    </div>
@endforelse
</body>
</html>
