<template>
    <div id="chart">
        <apexchart class="text-[#165345]" type="area" :height="width ? 200 : 280" :options="chartOptions" :series="series" :width="width"></apexchart>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // series: [{
            //     name: "Bolsas por minuto",
            //     data: [34, 44, 54, 21, 12, 43, 33, 23, 66, 66, 58] // Datos de ejemplo
            // }],
            // chartOptions: {
            //     stroke: {
            //         curve: 'straight',
            //         colors: ['#A24917'] // Cambia el color de la línea (en este caso, rojo)
            //     },
            //     fill: {
            //         type: 'gradient', // También puedes usar 'solid'
            //         gradient: {
            //             shadeIntensity: 1,
            //             opacityFrom: 0.5,
            //             opacityTo: 0.9,
            //             colorStops: [
            //                 {
            //                     offset: 0,
            //                     color: '#FF0000', // Color inicial del gradiente (rojo)
            //                     opacity: 0.5
            //                 },
            //                 {
            //                     offset: 100,
            //                     color: '#FFA500', // Color final del gradiente (naranja)
            //                     opacity: 0.9
            //                 }
            //             ]
            //         }
            //     },
            //     chart: {
            //         type: 'area',
            //         height: 280,
            //         zoom: {
            //             enabled: false
            //         }
            //     },
            //     dataLabels: {
            //         enabled: false
            //     },
            //     labels: ['2024-08-01', '2024-08-02', '2024-08-03', '2024-08-04', '2024-08-05', '2024-08-06', '2024-08-07', '2024-08-08', '2024-08-09', '2024-08-10', '2024-08-11'], // Fechas de ejemplo
            //     xaxis: {
            //         type: 'datetime',
            //         title: {
            //             text: 'Historial de bolsas',
            //         },
            //     },
            //     yaxis: {
            //         title: {
            //             text: 'BPM (Bolsa por minuto)',
            //         },
            //         opposite: false
            //     },
            //     legend: {
            //         horizontalAlign: 'left'
            //     }
            // },
        }
    },
    props: {
        series: Array,
        chartOptions: Object,
        width: String,
    }
}
</script>
