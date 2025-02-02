<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Dashboard: Robag1</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .card { margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }

        /* Estilos para la gráfica compatibles con Dompdf */
        .chart-container {
            width: 100%;
            height: 250px;
            border: 1px solid #ccc;
            text-align: center;
            margin-bottom: 10px;
            padding-top: 5px;
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
        }
    </style>
</head>
<body>
    <h1>Reporte de Dashboard: Robag1</h1>
    @foreach($cardsData as $card)
        <div class="card">
            <h2>{{ $card['card_name'] }}</h2>
            <p><strong>{{ $card['x_name'] }}</strong> vs <strong>{{ $card['y_name'] }}</strong></p>

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

                // Calcular cada cuántos registros saltar
                $skipFactor = $totalRecords > 30 ? ceil($totalRecords / 30) : 1;
                $filteredRows = [];

                foreach ($card['rows'] as $index => $row) {
                    if ($index % $skipFactor == 0) {
                        $filteredRows[] = $row;
                    }
                }

                // Calcular ancho de barras dinámicamente
                $numBars = count($filteredRows);
                $availableWidth = 500; // Ancho total estimado del contenedor
                $barWidth = min(50, floor($availableWidth / max($numBars, 1))); // Máximo 50px por barra
            @endphp

            <!-- Gráfica de barras -->
            <div class="chart-container">
                @foreach($filteredRows as $row)
                    @php
                        $barHeightPx = (is_numeric($row[1]) ? ($row[1] / $maxY * 200) : 0);
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
                        <th>{{ $card['x_name'] }}</th>
                        <th>{{ $card['y_name'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($card['rows'] as $row)
                        <tr>
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
