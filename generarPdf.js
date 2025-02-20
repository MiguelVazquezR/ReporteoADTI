import puppeteer from 'puppeteer';

(async () => {
    // 1. Iniciar el navegador
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    // 2. Obtener los parámetros de la línea de comandos
    const param = process.argv[2]; // Ejemplo: "bpm=120&date=2025-02-19&..."
    const url = `http://localhost:8000/report1-template?dashboard=${param}`;
    // const url = `http://localhost:8000/report1-template?dashboard=Robag1`;

    // 3. Navegar a la URL
    await page.goto(url, { waitUntil: 'networkidle2' });

    // 4. Esperar a que un elemento específico esté presente
    await page.waitForSelector('#data-loaded', { visible: true, timeout: 30000 }); // Timeout de 30 segundos

    // Esperar 3 segundos adicionales para animaciones de graficas
    await new Promise(resolve => setTimeout(resolve, 3000));

    // 5. Generar el PDF con orientación horizontal y márgenes reducidos
    await page.pdf({
        path: 'reporte.pdf', // Ruta donde se guardará el PDF
        format: 'A4', // Formato del PDF
        landscape: true, // Orientación horizontal
        margin: { top: '10mm', right: '10mm', bottom: '10mm', left: '10mm' }, // Márgenes reducidos
        printBackground: true, // Incluir fondos (útil si usas colores o imágenes de fondo)
    });

    // 6. Cerrar el navegador
    await browser.close();

    console.log('PDF generado correctamente.');
})();