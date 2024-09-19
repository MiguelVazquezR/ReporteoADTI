<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class PdfController extends Controller
{
    public function downloadPdf(Request $request)
    {   
        // Abrir pdf desde una vista sin descargar. -------------------------------------
        // $html = inertia('Home/Template');
        $pdf = Browsershot::url('http://localhost:8000/pdf-template')
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
}
