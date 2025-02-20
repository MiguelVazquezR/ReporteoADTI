<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PdfController extends Controller
{
    public function downloadPdf(Request $request)
    {
        // Abrir pdf desde una vista sin descargar. -------------------------------------
        // $html = inertia('Home/Template');
        $url = env('APP_URL') . '/pdf-template';
        $pdf = Browsershot::url($url)
            ->format('A4')
            ->landscape()
            ->showBackground()
            ->waitUntilNetworkIdle() // Espera a que se carguen todos los recursos (JS, CSS)
            ->pdf(); //genera el pdf

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="example.pdf"',
        ]);
    }

    public function uploadPdf(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = 'pdfs/' . $file->getClientOriginalName();
            Storage::disk('public')->put($filePath, file_get_contents($file));

            return response()->json(['message' => 'PDF guardado con éxito en la carpeta public']);
        }

        return response()->json(['message' => 'No se recibió ningún archivo'], 400);
    }

    public function savePdf(Request $request)
    {
        // Verificar si se ha recibido un archivo PDF
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Definir el nombre del archivo y la ruta de destino
            $fileName = 'example.pdf';
            $filePath = public_path('pdf/' . $fileName);

            // Mover el archivo a la carpeta public/pdf
            $file->move(public_path('pdf'), $fileName);

            return response()->json(['path' => $filePath]);
        } else {
            return response()->json(['message' => 'No se recibió ningún archivo'], 400);
        }
    }

    public function renderReport1()
    {
        return inertia('Home/Report1');
    }

    // reporte con plantilla blade y dompdf
    public function generateReportPDF($dashboardName)
    {
        $apiKey  = env('METABASE_API_KEY');
        $baseUrl = env('METABASE_API_URL');
        $client  = new Client();

        // 1. Obtener lista de dashboards y buscar $dashboardName
        $response = $client->request('GET', $baseUrl . '/dashboard', [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboards = json_decode($response->getBody(), true);

        $dashboardId = null;
        foreach ($dashboards as $dashboard) {
            if (isset($dashboard['name']) && $dashboard['name'] === $dashboardName) {
                $dashboardId = $dashboard['id'];
                break;
            }
        }
        if (!$dashboardId) {
            abort(404, "Dashboard {$dashboardName} no encontrado");
        }

        // 2. Obtener detalles del dashboard y extraer los card_ids
        $response = $client->request('GET', $baseUrl . '/dashboard/' . $dashboardId, [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboardDetails = json_decode($response->getBody(), true);
        $dashcards = $dashboardDetails['dashcards'] ?? [];

        $cardsData = [];
        foreach ($dashcards as $card) {
            $cardId = $card['card_id'] ?? null;
            if (!$cardId) {
                continue;
            }

            // 3. Obtener data de cada card: data.rows y cols.display_name (eje X e Y)
            $response = $client->request('GET', $baseUrl . '/card/' . $cardId, [
                'headers' => ['x-api-key' => $apiKey]
            ]);
            $cardDetails = json_decode($response->getBody(), true);
            $cardName = $cardDetails['name'] ?? 'Gráfico sin nombrar';
            $xName = $cardDetails['visualization_settings']['graph.dimensions'][0] ?? 'Eje X';
            $yName = $cardDetails['visualization_settings']['graph.metrics'][0] ?? 'Eje Y';
            // obtener un color aleatorio pero que contraste bien con el fondo blanco
            $color =  '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

            $response = $client->request('POST', $baseUrl . '/card/' . $cardId . '/query', [
                'headers' => ['x-api-key' => $apiKey]
            ]);
            $cardResult = json_decode($response->getBody(), true);
            $data = $cardResult['data'] ?? [];
            $rows = $data['rows'] ?? [];


            $cardsData[] = [
                'card_id' => $cardId,
                'card_name' => $cardName,
                'x_name'  => $xName,
                'y_name'  => $yName,
                'color' => $color,
                'rows' => array_slice($rows, -96) //los ultimos 96 reigstros (8 horas)
            ];
        }
        // 4. Generar PDF con la data obtenida
        $pdf = Pdf::loadView('pdf.report', ['cardsData' => $cardsData, 'dashboardName' => $dashboardName]);

        // ver en navegador
        // return $pdf->stream('reporte.pdf');
        // descargar
        // return $pdf->download('reporte.pdf');

        // Guardar el PDF en la carpeta storage/app/public y devolver la ruta completa del archivo
        $pdf->save(storage_path('app/public/reporte.pdf'));
        return storage_path('app/public/reporte.pdf');
    }

    // obtener datos de graficas de metabase de $dashboardName
    public function getMetabaseDataFromDashboard($dashboardName)
    {
        $apiKey  = env('METABASE_API_KEY');
        $baseUrl = env('METABASE_API_URL');
        $client  = new Client();

        // 1. Obtener lista de dashboards y buscar $dashboardName
        $response = $client->request('GET', $baseUrl . '/dashboard', [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboards = json_decode($response->getBody(), true);

        $dashboardId = null;
        foreach ($dashboards as $dashboard) {
            if (isset($dashboard['name']) && $dashboard['name'] === $dashboardName) {
                $dashboardId = $dashboard['id'];
                break;
            }
        }
        if (!$dashboardId) {
            abort(404, "Dashboard {$dashboardName} no encontrado");
        }

        // 2. Obtener detalles del dashboard y extraer los card_ids
        $response = $client->request('GET', $baseUrl . '/dashboard/' . $dashboardId, [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboardDetails = json_decode($response->getBody(), true);
        $dashcards = $dashboardDetails['dashcards'] ?? [];

        $cardsData = [];
        foreach ($dashcards as $card) {
            $cardId = $card['card_id'] ?? null;
            if (!$cardId) {
                continue;
            }

            // 3. Obtener data de cada card: data.rows y cols.display_name (eje X e Y)
            $response = $client->request('GET', $baseUrl . '/card/' . $cardId, [
                'headers' => ['x-api-key' => $apiKey]
            ]);
            $cardDetails = json_decode($response->getBody(), true);
            $cardName = $cardDetails['name'] ?? 'Gráfico sin nombrar';
            $xName = $cardDetails['visualization_settings']['graph.dimensions'][0] ?? 'Eje X';
            $yName = $cardDetails['visualization_settings']['graph.metrics'][0] ?? 'Eje Y';
            // obtener un color aleatorio pero que contraste bien con el fondo blanco
            $color =  '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

            $response = $client->request('POST', $baseUrl . '/card/' . $cardId . '/query', [
                'headers' => ['x-api-key' => $apiKey]
            ]);
            $cardResult = json_decode($response->getBody(), true);
            $data = $cardResult['data'] ?? [];
            $rows = $data['rows'] ?? [];

            $cardsData[] = [
                'card_id' => $cardId,
                'card_name' => $cardName,
                'x_name'  => $xName,
                'y_name'  => $yName,
                'color' => $color,
                'rows' => array_slice($rows, -96) //los ultimos 96 reigstros (8 horas)
            ];
        }
        
        // 4. enviar datos a cliente por json
        return response()->json(compact('cardsData'));
    }

    public function generateReportExcel($dashboardName)
    {
        $apiKey  = env('METABASE_API_KEY');
        $baseUrl = env('METABASE_API_URL');
        $client  = new \GuzzleHttp\Client();

        // 1. Obtener lista de dashboards y buscar $dashboardName
        $response = $client->request('GET', $baseUrl . '/dashboard', [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboards = json_decode($response->getBody(), true);

        $dashboardId = null;
        foreach ($dashboards as $dashboard) {
            if (isset($dashboard['name']) && $dashboard['name'] === $dashboardName) {
                $dashboardId = $dashboard['id'];
                break;
            }
        }
        if (!$dashboardId) {
            abort(404, "Dashboard {$dashboardName} no encontrado");
        }

        // 2. Obtener detalles del dashboard y extraer los card_ids
        $response = $client->request('GET', $baseUrl . '/dashboard/' . $dashboardId, [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboardDetails = json_decode($response->getBody(), true);
        $dashcards = $dashboardDetails['dashcards'] ?? [];

        $cardsData = [];
        foreach ($dashcards as $card) {
            $cardId = $card['card_id'] ?? null;
            if (!$cardId) continue;

            // Obtener detalles de la tarjeta
            $response = $client->request('GET', $baseUrl . '/card/' . $cardId, [
                'headers' => ['x-api-key' => $apiKey]
            ]);
            $cardDetails = json_decode($response->getBody(), true);
            $cardName = $cardDetails['name'] ?? 'Gráfico sin nombrar';
            $yName = $cardDetails['visualization_settings']['graph.metrics'][0] ?? 'Eje Y';

            // Obtener datos de la tarjeta
            $response = $client->request('POST', $baseUrl . '/card/' . $cardId . '/query', [
                'headers' => ['x-api-key' => $apiKey]
            ]);
            $cardResult = json_decode($response->getBody(), true);
            $rows = array_slice($cardResult['data']['rows'] ?? [], -96); // Últimos 96 registros (8 horas)

            $cardsData[] = [
                'card_id' => $cardId,
                'card_name' => $cardName,
                'y_name' => $yName,
                'rows' => $rows
            ];
        }

        // Crear un nuevo archivo de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Definir las columnas
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Hora');
        $col = 'C';
        foreach ($cardsData as $card) {
            $sheet->setCellValue($col . '1', $card['y_name']);
            $col++;
        }

        // Llenar los datos
        $row = 2;
        $today = now()->format('Y-m-d');

        // Encontrar el número máximo de filas entre todas las tarjetas
        $maxRows = max(array_map(function ($card) {
            return count($card['rows']);
        }, $cardsData));

        // Iterar sobre el número máximo de filas
        for ($index = 0; $index < $maxRows; $index++) {
            $sheet->setCellValue('A' . $row, $today); // Fecha

            // Hora (tomada de la primera tarjeta si existe)
            $sheet->setCellValue('B' . $row, $cardsData[0]['rows'][$index][0] ?? '');

            // Llenar las columnas de métricas
            $col = 'C';
            foreach ($cardsData as $card) {
                $sheet->setCellValue($col . $row, $card['rows'][$index][1] ?? '');
                $col++;
            }
            $row++;
        }

        // Guardar el archivo Excel
        // $writer = new Xlsx($spreadsheet);
        // $fileName = 'reporte_' . $dashboardName . '_' . now()->format('Ymd_His') . '.xlsx';
        // $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // $writer->save($temp_file);

        // // Descargar el archivo
        // return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);

        // Guardar el archivo Excel en storage/app/public/
        $fileName = 'reporte_' . $dashboardName . '_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/public/' . $fileName); // Ruta completa del archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Retornar el path relativo para usarlo en un correo
        return storage_path('app/public/' . $fileName);
    }
}
