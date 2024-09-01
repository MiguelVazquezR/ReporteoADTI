<?php

namespace App\Http\Controllers;

use App\Models\RobagData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Mail\ReportEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RobagDataController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Aquí se asume que el request tiene un JSON con los datos de la máquina
        $data = $request->all();

        // Guardar los datos en la base de datos
        RobagData::create($data);

        // Responder con un mensaje de éxito
        return response()->json(['message' => 'Datos guardados correctamente'], 200);
    }

    public function show(RobagData $robagData)
    {
        //
    }

    public function edit(RobagData $robagData)
    {
        //
    }

    public function update(Request $request, RobagData $robagData)
    {
        //
    }

    public function destroy(RobagData $robagData)
    {
        //
    }

    public function getDataByDateRange(Request $request)
    {
        $data = $this->getItemsByDateRange($request->date);

        return response()->json(compact('data'));
    }

    public function generateReport($saveToStorage = false, $dates = null)
    {
        if (is_null($dates) && request('dates')) {
            $dates = request('dates');
        } else {
            // rango de fechas por defecto para evitar errores
            $dates = [
                today()->startOfDay()->addHours(6)->toDateTimeString(), // Fecha de hoy a las 00:00
                now()->addHours(6)->toDateTimeString(),                 // Fecha de hoy con la hora actual
            ];
        }

        $items = $this->getItemsByDateRange($dates);

        // si no hay elementos para generar reporte salgo del metodo
        if (!$items->count()) return null;

        $spreadsheet = new Spreadsheet();

        // Crear y configurar las hojas de cálculo
        $worksheetTimes = $spreadsheet->getActiveSheet();
        $worksheetTimes->setTitle('Tiempos');
        $this->generateTimesSheet($worksheetTimes, $items, $dates);

        $worksheetProduction = $spreadsheet->createSheet();
        $worksheetProduction->setTitle('Producción');
        $this->generateProductionSheet($worksheetProduction, $items, $dates);

        $worksheetSpeed = $spreadsheet->createSheet();
        $worksheetSpeed->setTitle('Velocidad');
        $this->generateSpeedSheet($worksheetSpeed, $items, $dates);

        $worksheetDeviation = $spreadsheet->createSheet();
        $worksheetDeviation->setTitle('Desviación');
        $this->generateDeviationSheet($worksheetDeviation, $items, $dates);

        $worksheetFilm = $spreadsheet->createSheet();
        $worksheetFilm->setTitle('Pelicula');
        $this->generateFilmSheet($worksheetFilm, $items, $dates);

        $worksheetScale = $spreadsheet->createSheet();
        $worksheetScale->setTitle('Báscula');
        $this->generateScaleSheet($worksheetScale, $items, $dates);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // Include charts
        $fileName = 'reporte_robag_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        if ($saveToStorage) {
            // Crear la carpeta si no existe
            $directoryPath = storage_path('app/reports');
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            // Guardar el archivo en la carpeta storage/app/reports para poder adjuntarlo en correo
            $filePath = $directoryPath . '/' . $fileName;
            $writer->save($filePath);

            return $filePath; // Devolver la ruta del archivo
        } else {
            // Descargar el archivo
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
            $writer->save('php://output');
        }
    }

    public function generateTimesSheet($worksheet, $items, $dates)
    {
        // Paso 1: Ordenar los items por la propiedad created_at y agrupar por fecha
        $groupedItems = $items->sortBy('created_at')->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        // Paso 2: Obtener los valores máximos de paused_time, fault_time, interlock_time, out_of_film_time por cada día
        $maxValuesPerDay = $groupedItems->map(fn($dayItems) => [
            'paused_time' => $dayItems->max('paused_time'),
            'fault_time' => $dayItems->max('fault_time'),
            'interlock_time' => $dayItems->max('interlock_time'),
            'out_of_film_time' => $dayItems->max('out_of_film_time'),
        ]);

        // Paso 3: Sumar los valores de cada propiedad para obtener los totales y convertir segundos a horas
        $totalPausedTime = $maxValuesPerDay->sum('paused_time');
        $totalFaultTime = $maxValuesPerDay->sum('fault_time');
        $totalInterlockTime = $maxValuesPerDay->sum('interlock_time');
        $totalOutOfFilmTime = $maxValuesPerDay->sum('out_of_film_time');

        // Paso 4: Crear el array final con los totales
        $result = [
            ['Categoría', 'Valor en horas', 'Valor formateado'],
            ['En pausa', $totalPausedTime / 60 / 60, $this->formatTime($totalPausedTime)],
            ['En falla', $totalFaultTime / 60 / 60, $this->formatTime($totalFaultTime)],
            ['Sin película', $totalOutOfFilmTime / 60 / 60, $this->formatTime($totalOutOfFilmTime)],
            ['En interlock', $totalInterlockTime / 60 / 60, $this->formatTime($totalInterlockTime)],
        ];

        // Paso 5: Obtener la fecha y hora mínima y máxima de los registros
        $startDateTime = Carbon::parse($dates[0])->subHours(6)->format('d M, Y • h:iA');
        $endDateTime = Carbon::parse($dates[1])->subHours(6)->format('d M, Y • h:iA');

        // Paso 6: Insertar el rango de fechas en la primera fila
        $dateRange = "Del $startDateTime a $endDateTime";
        $worksheet->setCellValue('A1', $dateRange);

        // Unir celdas de A1 a C1
        $worksheet->mergeCells('A1:C1');

        // Establecer estilo para la primera fila
        $headerDateRange = $worksheet->getStyle('A1:C1');
        $headerDateRange->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerDateRange->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Insertar los datos de $result en el worksheet empezando desde A2
        $worksheet->fromArray($result, null, 'A2');

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(25);
        $worksheet->getColumnDimension('B')->setWidth(15);
        $worksheet->getColumnDimension('C')->setWidth(20);

        // Establecer estilo de la fila de encabezado (A2:C2)
        $headerRow = $worksheet->getStyle('A2:C2');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Tiempos!$B$2', null, 1), // Valor
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Tiempos!$A$3:$A$6', null, 4), // Categorías
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Tiempos!$B$3:$B$6', null, 4), // Valores
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_PIECHART_3D, // plotType
            null, // plotGrouping (not used for radar chart)
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        // Define chart title and labels
        $title = new Title('Distribución de Tiempos (HORAS)');
        $yAxisLabel = null; // No se utiliza en gráfico de radar

        // Create the chart
        $chart = new Chart(
            'chart9', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel // yAxisLabel (not used in radar chart)
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('E1');
        $chart->setBottomRightPosition('N14');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateProductionSheet($worksheet, $items, $dates)
    {
        // Paso 1: Ordenar los items por fecha ascendente y agrupar por día
        $groupedItems = $items->sortBy(function ($item) {
            return $item->created_at;
        })->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        $result = [['Fecha', 'Número de bolsas', 'Meta']];

        // Paso 2: Iterar sobre cada grupo de días
        foreach ($groupedItems as $date => $dayItems) {
            // Obtener el valor máximo de 'full_bags' para este día
            $maxFullBags = $dayItems->max('full_bags');

            // Formatear la fecha al formato deseado (ej. 25 Ago, 24)
            $formattedDate = Carbon::parse($date)->format('d M, y');

            // Obtener la meta para este día
            // Asume que $metaValues tiene las metas en el mismo orden de las fechas en los registros
            $metaValue = 36000;

            // Agregar la fila al resultado
            $result[] = [$formattedDate, $maxFullBags, $metaValue];
        }

        // Obtener la fecha y hora de rango seleccionado
        $startDateTime = Carbon::parse($dates[0])->subHours(6)->format('d M, Y • h:iA');
        $endDateTime = Carbon::parse($dates[1])->subHours(6)->format('d M, Y • h:iA');

        // Insertar el rango de fechas en la primera fila
        $dateRange = "Del $startDateTime a $endDateTime";
        $worksheet->setCellValue('A1', $dateRange);

        // Unir celdas de A1 a C1
        $worksheet->mergeCells('A1:C1');

        // Establecer estilo para la primera fila
        $headerDateRange = $worksheet->getStyle('A1:C1');
        $headerDateRange->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerDateRange->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Insertar los datos de $result en el worksheet empezando desde A2
        $worksheet->fromArray($result, null, 'A2');

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(15);
        $worksheet->getColumnDimension('B')->setWidth(20);
        $worksheet->getColumnDimension('C')->setWidth(15);

        // Establecer estilo de la fila de encabezado
        $headerRow = $worksheet->getStyle('A2:C2');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Producción!$B$2', null, 1), // Número de bolsas
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Producción!$C$2', null, 1), // Meta
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Producción!$A$3:$A$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Fechas
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Producción!$B$3:$B$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Número de bolsas
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Producción!$C$3:$C$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Meta
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART_3D, // plotType
            DataSeries::GROUPING_CLUSTERED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );

        // Set additional dataseries parameters
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        // Define chart title and labels
        $title = new Title('Producción vs Meta');
        $yAxisLabel = new Title('Número de bolsas');

        // Create the chart
        $chart = new Chart(
            'chart8', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('E1');
        $chart->setBottomRightPosition('N14');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateSpeedSheet($worksheet, $items, $dates)
    {
        // Paso 1: Ordenar los items por fecha ascendente y agrupar por día
        $groupedItems = $items->sortBy(function ($item) {
            return $item->created_at;
        })->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        $result = [['Fecha', 'Bolsas por minuto']];

        // Paso 2: Iterar sobre cada grupo de días
        foreach ($groupedItems as $date => $dayItems) {
            // Calcular el promedio de 'bags_per_minute' para este día
            $averageBagsPerMinute = $dayItems->avg('bags_per_minute');

            // Formatear la fecha al formato deseado (ej. 25 Ago, 24)
            $formattedDate = Carbon::parse($date)->format('d M, y');

            // Redondear el promedio a un número entero
            $averageBagsPerMinute = round($averageBagsPerMinute);

            // Agregar la fila al resultado
            $result[] = [$formattedDate, $averageBagsPerMinute];
        }

        // Obtener la fecha y hora de rango seleccionado
        $startDateTime = Carbon::parse($dates[0])->subHours(6)->format('d M, Y • h:iA');
        $endDateTime = Carbon::parse($dates[1])->subHours(6)->format('d M, Y • h:iA');

        // Insertar el rango de fechas en la primera fila
        $dateRange = "Del $startDateTime a $endDateTime";
        $worksheet->setCellValue('A1', $dateRange);

        // Unir celdas de A1 a C1
        $worksheet->mergeCells('A1:C1');

        // Establecer estilo para la primera fila
        $headerDateRange = $worksheet->getStyle('A1:C1');
        $headerDateRange->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerDateRange->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Insertar los datos de $result en el worksheet empezando desde A2
        $worksheet->fromArray($result, null, 'A2');

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Establecer estilo de la fila de encabezado
        $headerRow = $worksheet->getStyle('A2:B2');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Velocidad!$B$2', null, 1), // Bolsas por minuto
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Velocidad!$A$3:$A$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Fechas
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Velocidad!$B$3:$B$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Bolsas por minuto
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_AREACHART_3D, // plotType
            null, // plotGrouping (not used for area chart)
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );

        // Set additional dataseries parameters
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        // Define chart title and labels
        $title = new Title('Velocidad de Producción');
        $yAxisLabel = new Title('Bolsas por minuto');

        // Create the chart
        $chart = new Chart(
            'chart7', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('D1');
        $chart->setBottomRightPosition('N14');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateDeviationSheet($worksheet, $items, $dates)
    {
        // Paso 1: Redondear la desviación estándar hacia el entero más próximo
        $roundedItems = $items->map(function ($item) {
            $item->rounded_deviation = round($item->standard_deviation);
            return $item;
        });

        // Paso 2: Agrupar los elementos por la desviación estándar redondeada
        $groupedItems = $roundedItems->groupBy('rounded_deviation');

        $result = [['Desviación', 'Número de muestras']];

        // Paso 3: Iterar sobre cada grupo, ordenando por desviación
        foreach ($groupedItems->sortKeys() as $deviation => $group) {
            // Contar el número de muestras en cada grupo
            $sampleCount = $group->count();

            // Agregar la fila al resultado
            $result[] = [$deviation, $sampleCount];
        }

        // Obtener la fecha y hora de rango seleccionado
        $startDateTime = Carbon::parse($dates[0])->subHours(6)->format('d M, Y • h:iA');
        $endDateTime = Carbon::parse($dates[1])->subHours(6)->format('d M, Y • h:iA');

        // Insertar el rango de fechas en la primera fila
        $dateRange = "Del $startDateTime a $endDateTime";
        $worksheet->setCellValue('A1', $dateRange);

        // Unir celdas de A1 a C1
        $worksheet->mergeCells('A1:C1');

        // Establecer estilo para la primera fila
        $headerDateRange = $worksheet->getStyle('A1:C1');
        $headerDateRange->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerDateRange->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Insertar los datos de $result en el worksheet empezando desde A2
        $worksheet->fromArray($result, null, 'A2');

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Establecer estilo de la fila de encabezado
        $headerRow = $worksheet->getStyle('A2:B2');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Desviación!$B$2', null, 1), // Número de muestras
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Desviación!$A$3:$A$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Desviación
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Desviación!$B$3:$B$' . $groupedItems->count() + 2, null, $groupedItems->count()), // Número de muestras
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART_3D, // plotType
            DataSeries::GROUPING_CLUSTERED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );

        // Set additional dataseries parameters
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        // Define chart title and labels
        $title = new Title('Distribución de Desviaciones');
        $yAxisLabel = new Title('Número de muestras');

        // Create the chart
        $chart = new Chart(
            'chart6', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('D1');
        $chart->setBottomRightPosition('N14');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateFilmSheet($worksheet, $items, $dates)
    {
        // obtencion de datos para la grafica y tabla
        // Paso 1: Ordenar los items por la propiedad created_at y agrupar por fecha
        $groupedItems = $items->sortBy('created_at')->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        // Paso 2: Obtener los valores máximos de full_bags, total_waste por cada día
        $maxValuesPerDay = $groupedItems->map(function ($dayItems) {
            return [
                'full_bags' => $dayItems->max('full_bags'),
                'total_waste' => $dayItems->max('total_waste'),
            ];
        });

        // Paso 3: Sumar los valores de cada propiedad para obtener los totales
        $totalFullBags = $maxValuesPerDay->sum('full_bags');
        $totalWaste = $maxValuesPerDay->sum('total_waste');

        // Paso 4: Crear el array final con los totales
        $result = [
            ['Categoría', 'Cantidad'],
            ['Bolsas llenas', $totalFullBags],
            ['Bolsas desperdiciadas', $totalWaste],
            ['Total', $totalWaste + $totalFullBags],
        ];

        // Obtener la fecha y hora de rango seleccionado
        $startDateTime = Carbon::parse($dates[0])->subHours(6)->format('d M, Y • h:iA');
        $endDateTime = Carbon::parse($dates[1])->subHours(6)->format('d M, Y • h:iA');

        // Insertar el rango de fechas en la primera fila
        $dateRange = "Del $startDateTime a $endDateTime";
        $worksheet->setCellValue('A1', $dateRange);

        // Unir celdas de A1 a C1
        $worksheet->mergeCells('A1:C1');

        // Establecer estilo para la primera fila
        $headerDateRange = $worksheet->getStyle('A1:C1');
        $headerDateRange->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerDateRange->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Insertar los datos de $result en el worksheet empezando desde A2
        $worksheet->fromArray($result, null, 'A2');

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Establecer estilo de la fila de encabezado
        $headerRow = $worksheet->getStyle('A2:B2');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Pelicula!$B$2', null, 1), // Cantidad
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Pelicula!$A$3:$A$4', null, 2), // Categorías
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Pelicula!$B$3:$B$4', null, 2), // Cantidades
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_DONUTCHART, // plotType
            null, // plotGrouping (not used for pie chart)
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        // Define chart title and labels
        $title = new Title('USO DE PELICULA');
        $yAxisLabel = null; // No se utiliza en gráfico de pastel

        // Create the chart
        $chart = new Chart(
            'chart5', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel (not used in pie chart)
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('D1');
        $chart->setBottomRightPosition('N14');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateScaleSheet($worksheet, $items, $dates)
    {
        // Paso 1: Ordenar los items por la propiedad created_at y agrupar por fecha
        $groupedItems = $items->sortBy('created_at')->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        // Paso 2: Obtener los valores promedio de mean_weight, standard_deviation, total_dump_weight, total_giveaway, giveaway_percentage por cada día
        $averageValuesPerDay = $groupedItems->map(function ($dayItems) {
            return [
                'mean_weight' => $dayItems->avg('mean_weight'),
                'standard_deviation' => $dayItems->avg('standard_deviation'),
                'total_dump_weight' => $dayItems->avg('total_dump_weight'),
                'total_giveaway' => $dayItems->avg('total_giveaway'),
                'giveaway_percentage' => $dayItems->avg('giveaway_percentage'),
            ];
        });

        // Paso 3: Calcular los promedios generales directamente y redondear a 2 dígitos
        $totals = $averageValuesPerDay->reduce(function ($carry, $day) {
            foreach ($day as $key => $value) {
                $carry[$key] = ($carry[$key] ?? 0) + $value;
            }
            return $carry;
        }, []);

        $dayCount = $averageValuesPerDay->count();
        $overallAverages = array_map(fn($total) => round($total / $dayCount, 2), $totals);

        // Paso 4: Crear el array final con los totales
        $result = [
            ['Variable', 'Valor promedio'],
            ['Peso medio', $overallAverages['mean_weight'] . 'g'],
            ['Desviación estándar', $overallAverages['standard_deviation']],
            ['Peso total de descarga', $overallAverages['total_dump_weight'] . 'g'],
            ['Total regalado', $overallAverages['total_giveaway'] . 'g'],
            ['Porcentaje regalado', $overallAverages['giveaway_percentage'] . '%'],
        ];

        // Obtener la fecha y hora de rango seleccionado
        $startDateTime = Carbon::parse($dates[0])->subHours(6)->format('d M, Y • h:iA');
        $endDateTime = Carbon::parse($dates[1])->subHours(6)->format('d M, Y • h:iA');

        // Insertar el rango de fechas en la primera fila
        $dateRange = "Del $startDateTime a $endDateTime";
        $worksheet->setCellValue('A1', $dateRange);

        // Unir celdas de A1 a C1
        $worksheet->mergeCells('A1:C1');

        // Establecer estilo para la primera fila
        $headerDateRange = $worksheet->getStyle('A1:C1');
        $headerDateRange->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerDateRange->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris

        // Insertar los datos de $result en el worksheet empezando desde A2
        $worksheet->fromArray($result, null, 'A2');

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Establecer estilo de la fila de encabezado
        $headerRow = $worksheet->getStyle('A2:B2');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('F2F2F2'); // Color de fondo gris
    }

    public function emailReport(Request $request)
    {
        $validatedData = $request->validate([
            'main_email' => 'required|email',
            'cco' => 'nullable|array',
            'cco.*' => 'nullable|email', // Cada CCO debe ser un correo válido
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Recoger los datos validados
        $mainEmail = $validatedData['main_email'];
        $cco = $validatedData['cco'] ?? [];
        $subject = $validatedData['subject'];
        $description = $validatedData['description'] ?? '';

        // Generar el reporte y guardar en storage
        $filePath = $this->generateReport(true, $request->dates); // Guardar el archivo y obtener la ruta

        // Enviar el correo con el archivo adjunto
        Mail::to($mainEmail)
            ->cc($cco)
            ->send(new ReportEmail($subject, $description, $filePath));
    }

    // funciones privadas
    private function getItemsByDateRange($dates)
    {
        $start = Carbon::parse($dates[0])->subHours(6)->toDateTimeString();
        $end = Carbon::parse($dates[1])->subHours(6)->toDateTimeString();

        // Ventas y gastos de la semana seleccionada
        $items = RobagData::whereBetween('created_at', [$start, $end])
            ->get();

        return $items;
    }

    private function formatTime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);

        $formattedTime = '';

        if ($days > 0) {
            $formattedTime .= $days . ' día' . ($days > 1 ? 's ' : ' ');
        }

        if ($hours > 0) {
            $formattedTime .= $hours . 'h ';
        }

        if ($minutes > 0) {
            $formattedTime .= $minutes . 'm ';
        }

        if ($seconds > 0 || $formattedTime === '') {
            $formattedTime .= $seconds . 's';
        }

        return trim($formattedTime);
    }
}
