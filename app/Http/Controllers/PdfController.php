<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class PdfController extends Controller
{
    public function downloadPdf()
    {
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



// ----------------------------------------------------------------------------------------------
        
        $url = 'http://127.0.0.1:8000/'; // Cambia esta URL por la que quieres convertir

        // Generar PDF a partir de HTML
        $pdfPath = storage_path('app/public/example.png'); // Ruta donde se guardará el PDF

        // pagina web url (mas acercada)
        Browsershot::url($url)
            ->windowSize(1024, 720)
            ->setNodeBinary('/c/Program Files/nodejs/node') // Ruta absoluta hacia node
            ->setNpmBinary('/c/Program Files/nodejs/npm') // Ruta absoluta hacia npm
            ->save($pdfPath); // Guarda el PDF en la carpeta 'storage/app/public/'

        // Retornar el PDF como una descarga
        return response()->download($pdfPath);

    }
}
