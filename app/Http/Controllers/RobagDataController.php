<?php

namespace App\Http\Controllers;

use App\Models\RobagData;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
// use PhpOffice\PhpSpreadsheet\Chart\Legend;
// use PhpOffice\PhpSpreadsheet\Chart\Chart;
// use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
// use PhpOffice\PhpSpreadsheet\Chart\Title;
// use PhpOffice\PhpSpreadsheet\Chart\Layout;
// use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
        $start = Carbon::parse($request->date[0])->toDateTimeString();
        $end = Carbon::parse($request->date[1])->toDateTimeString();

        // Ventas y gastos de la semana seleccionada
        $data = RobagData::whereBetween('created_at', [$start, $end])
            ->get();

        return response()->json(compact('data'));
    }

    public function generateReport()
    {
        $spreadsheet = new Spreadsheet();

        // Crear nueva hoja para gráfico de pastel
        $worksheetTimes = $spreadsheet->getActiveSheet();
        $worksheetTimes->setTitle('Tiempos');
        $this->generateTimesSheet($worksheetTimes);

        // Crear nueva hoja para gráfico de barras de producción
        $worksheetProduction = $spreadsheet->createSheet();
        $worksheetProduction->setTitle('Producción');
        $this->generateProductionSheet($worksheetProduction);

        // Crear nueva hoja para gráfico de área
        $worksheetSpeed = $spreadsheet->createSheet();
        $worksheetSpeed->setTitle('Velocidad');
        $this->generateSpeedSheet($worksheetSpeed);

        // Crear nueva hoja para gráfico de barras
        $worksheetDeviation = $spreadsheet->createSheet();
        $worksheetDeviation->setTitle('Desviación');
        $this->generateDeviationSheet($worksheetDeviation);

        // Generar hoja para gráfico de pastel
        $worksheetFilm = $spreadsheet->createSheet();
        $worksheetFilm->setTitle('Pelicula');
        $this->generateFilmSheet($worksheetFilm);

        // Crear nueva hoja para tabla de escala
        $worksheetScale = $spreadsheet->createSheet();
        $worksheetScale->setTitle('Báscula');
        $this->generateScaleSheet($worksheetScale);

        // Write the Excel file and save it
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // Include charts
        $fileName = 'reporte_robag_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
    }

    public function generateTimesSheet($worksheet)
    {
        // Insert data into the worksheet
        $worksheet->fromArray(
            [
                ['Categoría', 'Valor'],
                ['En pausa', 1.2],
                ['En falla', 0.5],
                ['Sin película', 0.6],
                ['En interlock', 0.2],
            ]
        );

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(25);
        $worksheet->getColumnDimension('B')->setWidth(15);

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Tiempos!$B$1', null, 1), // Valor
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Tiempos!$A$2:$A$5', null, 4), // Categorías
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Tiempos!$B$2:$B$5', null, 4), // Valores
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_PIECHART_3D, // plotType
            null, // plotGrouping (not used for radar chart)
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
        $title = new Title('Distribución de Tiempos (HRS)');
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
            $yAxisLabel  // yAxisLabel (not used in radar chart)
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('D1');
        $chart->setBottomRightPosition('L10');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateProductionSheet($worksheet)
    {
        // Insert data into the worksheet
        $worksheet->fromArray(
            [
                ['Fecha', 'Número de bolsas', 'Meta'],
                ['22 Ago 24', 21000, 36000],
                ['23 Ago 24', 12000, 36000],
                ['24 Ago 24', 33000, 33000],
                ['25 Ago 24', 49000, 40000],
                ['26 Ago 24', 18000, 36000],
                ['27 Ago 24', 11000, 15000],
                ['28 Ago 24', 9000, 15000],
            ]
        );

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(15);
        $worksheet->getColumnDimension('B')->setWidth(15);
        $worksheet->getColumnDimension('C')->setWidth(15);

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Producción!$B$1', null, 1), // Número de bolsas
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Producción!$C$1', null, 1), // Meta
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Producción!$A$2:$A$8', null, 7), // Fechas
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Producción!$B$2:$B$8', null, 7), // Número de bolsas
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Producción!$C$2:$C$8', null, 7), // Meta
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
        $chart->setBottomRightPosition('M10');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateSpeedSheet($worksheet)
    {
        // Insert data into the worksheet
        $worksheet->fromArray(
            [
                ['Fecha', 'Bolsas por minuto'],
                ['22 Ago', 150],
                ['23 Ago', 125],
                ['24 Ago', 120],
                ['25 Ago', 115],
                ['26 Ago', 100],
                ['27 Ago', 130],
                ['28 Ago', 145],
            ]
        );

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Velocidad!$B$1', null, 1), // Bolsas por minuto
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Velocidad!$A$2:$A$8', null, 7), // Fechas
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Velocidad!$B$2:$B$8', null, 7), // Bolsas por minuto
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
        $chart->setBottomRightPosition('L10');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateDeviationSheet($worksheet)
    {
        // Insert data into the worksheet
        $worksheet->fromArray(
            [
                ['Desviación', 'Número de muestras'],
                [-3, 21],
                [-2, 12],
                [-1, 33],
                [0, 49],
                [2, 18],
                [3, 11],
                [5, 5],
            ]
        );

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Desviación!$B$1', null, 1), // Número de muestras
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Desviación!$A$2:$A$8', null, 7), // Desviación
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Desviación!$B$2:$B$8', null, 7), // Número de muestras
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
        $chart->setBottomRightPosition('L10');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateFilmSheet($worksheet)
    {
        // Insert data into the worksheet
        $worksheet->fromArray(
            [
                ['Categoría', 'Cantidad'],
                ['Bolsas llenas', 500],
                ['Bolsas desperdiciadas', 100],
            ]
        );

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Create data series
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Pelicula!$B$1', null, 1), // Cantidad
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Pelicula!$A$2:$A$3', null, 2), // Categorías
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Pelicula!$B$2:$B$3', null, 2), // Cantidades
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
        $chart->setBottomRightPosition('L10');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);
    }

    public function generateScaleSheet($worksheet)
    {
        // Insert data into the worksheet
        $worksheet->fromArray(
            [
                ['Variable', 'Valor promedio'],
                ['Peso medio', '54.50 g'],
                ['Desviación estándar', '1.6 '],
                ['Peso total de descarga', '33,495.8 Kg'],
                ['Total regalado', '698.85 g'],
                ['Porcentaje regalado', '3.97%'],
            ],
            null, // No style
            'A1'  // Start cell
        );

        // Ajustar el ancho de las columnas
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getColumnDimension('B')->setWidth(20);

        // Establecer estilo de la fila de encabezado
        $headerRow = $worksheet->getStyle('A1:B1');
        $headerRow->getFont()->setBold(true);
        $headerRow->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerRow->getFill()->getStartColor()->setARGB('FFFF00'); // Color de fondo amarillo
    }
}
