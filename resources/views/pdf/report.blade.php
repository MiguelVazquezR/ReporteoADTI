<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte Robag1</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .card {
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 2px;
            text-align: center;
        }

        thead {
            background-color: #00688e;
            color: #fff;
        }

        /* Estilos para la gráfica compatibles con Dompdf */
        .chart-container {
            width: 100%;
            height: 33%;
            text-align: center;
            padding-top: 3px;
            position: relative;
        }

        .bar-wrapper {
            display: inline-block;
            margin: 0;
            vertical-align: bottom;
        }

        .bar {
            background-color: #3498db;
            display: inline-block;
            border-radius: 3px;
        }

        .label {
            font-size: 8px;
            display: block;
            margin-top: 2px;
            /* Etiquetas en vertical */
            transform: rotate(90deg);
            transform-origin: left bottom;
            white-space: nowrap;
            width: 6px;
        }
    </style>
</head>

<body>
    @if (app()->environment() === 'production')
        {{-- en servidor cpanel colocar asset --}}
        <img style="position: absolute; right: 0; top: 0; width: auto; height: 45px;"
            src="{{ asset('images/logo_colors.png') }}">
    @else
        <img style="position: absolute; right: 0; top: 0; width: auto; height: 45px;"
            src="{{ public_path('images\logo_colors.png') }}">
    @endif
    <h1 style="margin: 0">Reporte: Robag1</h1>
    @foreach ($cardsData as $card)
        <div class="card">
            @php
                $maxY = 0;
                $totalRecords = count($card['rows']);

                // Encontrar el valor máximo de Y
                foreach ($card['rows'] as $row) {
                    if (is_numeric($row[1]) && $row[1] > $maxY) {
                        $maxY = $row[1];
                    }
                }
                $maxY = $maxY > 0 ? $maxY : 1;

                // Solo mostrar los ultimos 74 registros
                $startIndex = $totalRecords - 74;
                $filteredRows = [];

                foreach ($card['rows'] as $index => $row) {
                    if ($index >= $startIndex) {
                        $filteredRows[] = $row;
                    }
                }

                // Calcular ancho de barras dinámicamente
                $numBars = count($filteredRows);
                $availableWidth = 200; // Ancho total estimado del contenedor
                $barWidth = min(50, floor($availableWidth / max($numBars, 1))); // Máximo 50px por barra
            @endphp

            <!-- Gráfica de barras -->
            <div class="chart-container">
                <h2>{{ $card['card_name'] }}</h2>
                <p style="margin: 0"><strong>{{ $card['x_name'] }}</strong> vs <strong>{{ $card['y_name'] }}</strong></p>
                @foreach ($filteredRows as $row)
                    @php
                        $barHeightPx = is_numeric($row[1]) ? ($row[1] / $maxY) * 200 : 0;
                    @endphp
                    <div class="bar-wrapper">
                        <div class="bar" style="height: {{ $barHeightPx }}px; width: {{ $barWidth }}px;"></div>
                        <div class="label">{{ $row[0] }}</div>
                    </div>
                @endforeach
            </div>
            <!-- Tabla con datos -->
            <table>
                <thead>
                    <tr>
                        <th># Registro</th>
                        <th>{{ $card['x_name'] }}</th>
                        <th>{{ $card['y_name'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($card['rows'] as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row[0] ?? '' }}</td>
                            <td>{{ $row[1] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>

</html>
