import puppeteer from 'puppeteer'; // Si usas ES Modules
// const puppeteer = require('puppeteer'); // Si usas CommonJS

(async () => {
    // 1. Iniciar el navegador
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    // 2. Definir la URL con los parámetros
    const url = 'http://localhost:8000/machine-data-pdf-template?bpm=120&date=2025-02-19&dates%5B0%5D=2025-02-19T12%3A00%3A00.000Z&dates%5B1%5D=2025-02-19T22%3A21%3A04.866Z&timeSlots%5B0%5D=2025-02-19%206%3A00&timeSlots%5B1%5D=2025-02-19%206%3A30&timeSlots%5B2%5D=2025-02-19%207%3A00&timeSlots%5B3%5D=2025-02-19%207%3A30&timeSlots%5B4%5D=2025-02-19%208%3A00&timeSlots%5B5%5D=2025-02-19%208%3A30';

    // 3. Navegar a la URL
    await page.goto(url, { waitUntil: 'networkidle2' });

    // 4. Generar el PDF con orientación horizontal y márgenes reducidos
    await page.pdf({
        path: 'reporte.pdf', // Ruta donde se guardará el PDF
        format: 'A4', // Formato del PDF
        landscape: true, // Orientación horizontal
        margin: { top: '10mm', right: '10mm', bottom: '10mm', left: '10mm' }, // Márgenes reducidos
        printBackground: true, // Incluir fondos (útil si usas colores o imágenes de fondo)
    });

    // 5. Cerrar el navegador
    await browser.close();

    console.log('PDF generado correctamente.');
})();