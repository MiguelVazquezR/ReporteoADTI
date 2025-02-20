import puppeteer from 'puppeteer'; // Si usas ES Modules
// const puppeteer = require('puppeteer'); // Si usas CommonJS

(async () => {
    // 1. Iniciar el navegador
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    // 2. Definir la URL con los parámetros
    const url = 'http://127.0.0.1:8000/machine-data-pdf-template?bpm=120&date=2025-02-19&dates%5B0%5D=2025-02-19T12%3A00%3A00.000Z&dates%5B1%5D=2025-02-19T23%3A22%3A53.208Z&selectedVariables%5B0%5D=Tiempo%20de%20actividad%20de%20Robag&selectedVariables%5B1%5D=Tiempo%20de%20interlock&selectedVariables%5B2%5D=Tiempo%20pausado&selectedVariables%5B3%5D=Tiempo%20de%20ejecuci%C3%B3n&selectedVariables%5B4%5D=Tiempo%20de%20falla&timeSlots%5B0%5D=2025-02-19%2011%3A00&timeSlots%5B1%5D=2025-02-19%2011%3A30&timeSlots%5B2%5D=2025-02-19%2012%3A00&timeSlots%5B3%5D=2025-02-19%2012%3A30&timeSlots%5B4%5D=2025-02-19%2013%3A00&timeSlots%5B5%5D=2025-02-19%2013%3A30&timeSlots%5B6%5D=2025-02-19%2014%3A00&timeSlots%5B7%5D=2025-02-19%2014%3A30&timeSlots%5B8%5D=2025-02-19%2015%3A00&timeSlots%5B9%5D=2025-02-19%2015%3A30&timeSlots%5B10%5D=2025-02-19%2016%3A00';

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