<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // -------------------------------------------------------------------------------------



        // Abrir pdf sin descargar. Funciona bien con url-------------------------------------
        // $pdf = Browsershot::url('https://reporteo.dtw.com.mx/')
        // // ->setIncludePath('$PATH:/c/Program Files/nodejs')
        //     ->format('A4')
        //     ->landscape()
        //     ->showBackground()
        //     ->waitUntilNetworkIdle() // Espera a que se carguen todos los recursos (JS, CSS)
        //     ->pdf(); //genera el pdf
        //     // ->savePdf('laravel.pdf'); //guarda el pdf en public

        //     return response($pdf, 200, [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => 'inline; filename="example.pdf"',
        //     ]);
        //     // return response()->download('app/public/laravel.pdf');
        // -------------------------------------------------------------------------------------



        // Descarga el archivo del path indicado -----------------------------------------------
        // $url = 'https://reporteo.dtw.com.mx/'; // Cambia esta URL por la que quieres convertir

        // // Generar PDF a partir de HTML
        // $pdfPath = storage_path('app/public/example.pdf'); // Ruta donde se guardará el PDF

        // // pagina web url (mas acercada)
        // Browsershot::url($url)
        //     // ->format('A4')
        //     // ->landscape()
        //     ->paperSize('280', '280')
        //     ->scale(0.75)
        //     // ->margins('2', '2', '2', '2' )
        //     ->showBackground()
        //     ->save($pdfPath); // Guarda el PDF en la carpeta 'storage/app/public/'

        // // Retornar el PDF como una descarga
        // return response()->download($pdfPath);
        // ----------------------------------------------------------------------------------------

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


    public function generateReport()
    {
        $apiKey  = 'mb_bF3v/xGVEZxOBpGkLCdug9jHvLg3uhZaAsvRGZ7pk3M=';
        $baseUrl = 'http://localhost:3000/api';
        $client  = new Client();

        // 1. Obtener lista de dashboards y buscar "Robag1"
        $response = $client->request('GET', $baseUrl . '/dashboard', [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboards = json_decode($response->getBody(), true);

        $dashboardId = null;
        foreach ($dashboards as $dashboard) {
            if (isset($dashboard['name']) && $dashboard['name'] === 'Robag1') {
                $dashboardId = $dashboard['id'];
                break;
            }
        }
        if (!$dashboardId) {
            abort(404, 'Dashboard "Robag1" no encontrado');
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
        $pdf = Pdf::loadView('pdf.report', ['cardsData' => $cardsData]);
        
        return $pdf->stream('reporte.pdf');

        return $pdf->download('reporte.pdf');
    }
}
