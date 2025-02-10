<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte {{ $dashboardName }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .card {
            margin-bottom: 40px;
        }

        table {
            width: 48%; /* Ancho de la tabla para que quepan dos por fila */
            border-collapse: collapse;
            margin-top: 42px;
            float: left; /* Colocar tablas una al lado de la otra */
            margin-right: 2%; /* Espacio entre tablas */
        }

        th,
        td {
            border: 1px solid #333;
            padding: 1px;
            font-size: 10px;
            text-align: center;
        }

        thead {
            background-color: #00688e;
            color: #fff;
        }

        .chart-container {
            width: 100%;
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
            display: inline-block;
            border-radius: 3px;
        }

        .label {
            font-size: 8px;
            display: block;
            margin-top: 2px;
            transform: rotate(90deg);
            transform-origin: left bottom;
            white-space: nowrap;
            width: 6px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    @if (app()->environment() === 'production')
        <img style="position: absolute; right: 0; top: 0; width: auto; height: 30px;"
            src="{{ asset('images/logo_colors.png') }}">
    @else
        <img style="position: absolute; right: 0; top: 0; width: auto; height: 30px;"
            src="{{ public_path('images\logo_colors.png') }}">
    @endif
    <h1 style="margin: 0; font-size: 13px">Reporte: {{ $dashboardName }}</h1>
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
                        $barHeightPx = is_numeric($row[1]) ? ($row[1] / $maxY) * 100 : 0;
                    @endphp
                    <div class="bar-wrapper">
                        <div class="bar" style="height: {{ $barHeightPx }}px; width: {{ $barWidth }}px; background-color: {{ $card['color'] }}"></div>
                        <div class="label">{{ $row[0] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Tablas con datos -->
            <div class="clearfix">
                @php
                    $rowsPerTable = 48;
                    $totalRows = count($card['rows']);
                    $numTables = ceil($totalRows / $rowsPerTable);
                @endphp

                @for ($i = 0; $i < $numTables; $i++)
                    @php
                        $start = $i * $rowsPerTable;
                        $end = min($start + $rowsPerTable, $totalRows);
                        $rows = array_slice($card['rows'], $start, $end - $start);
                    @endphp

                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ $card['x_name'] }}</th>
                                <th>{{ $card['y_name'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $index => $row)
                                <tr>
                                    <td>{{ $start + $loop->iteration }}</td>
                                    <td>{{ $row[0] ?? '' }}</td>
                                    <td>{{ $row[1] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endfor
            </div>
        </div>
    @endforeach
</body>

</html>