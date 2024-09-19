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
        $pdf = Browsershot::url('http://localhost:8000/pdf-template?bpm=120&date=2024-09-18&dates%5B0%5D=2024-09-04T06%3A00%3A00.000Z&dates%5B1%5D=2024-09-11T06%3A00%3A00.000Z&selectedVariables%5B0%5D=Estado%20de%20la%20m%C3%A1quina&selectedVariables%5B1%5D=Cantidad%20baja%20de%20bolsas&selectedVariables%5B2%5D=Restablecer%20contadores&selectedVariables%5B3%5D=Estado%20interno%20de%20la%20b%C3%A1scula&selectedVariables%5B4%5D=Tiempo%20de%20actividad%20de%20Robag&selectedVariables%5B5%5D=Motivo%20del%20estado%20de%20parada&timeSlots%5B0%5D=6%3A00&timeSlots%5B1%5D=6%3A30&timeSlots%5B2%5D=7%3A00&timeSlots%5B3%5D=7%3A30&timeSlots%5B4%5D=8%3A00&timeSlots%5B5%5D=8%3A30')
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
