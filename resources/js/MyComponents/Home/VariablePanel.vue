<template>
    <main class="rounded-[20px] border border-grayD9 p-4" :class="height ? 'h-[220px]' : 'h-[350px]'">
        <div>
            <p class="text-[#6D6E72] font-bold text-sm">{{ variableName }}</p>
            <BasicArea :series="series" :chartOptions="chartOptions" :width="width" :height="height" />
        </div>
    </main>
</template>

<script>
import BasicArea from '@/MyComponents/Chart/Area/BasicArea.vue';

export default {
    data() {
        return {
            items: [],
            series: [{
                name: "Lectura",
                data: [] // Datos dinámicos
            }],
            chartOptions: {
                stroke: {
                    curve: 'straight',
                    colors: ['#1EAAA2'] // Cambia el color de la línea (en este caso, rojo)
                },
                fill: {
                    type: 'gradient', // También puedes usar 'solid'
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.5,
                        opacityTo: 0.9,
                        colorStops: [
                            {
                                offset: 0,
                                color: '#5BBDB8', // Color fuerte de gradiente
                                opacity: 0.5
                            },
                            {
                                offset: 100,
                                color: '#E8F9F8', // Color bajito de gradiente
                                opacity: 0.9
                            }
                        ]
                    }
                },
                chart: {
                    type: 'area',
                    height: 280,
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                labels: [], // Etiquetas dinámicas
                xaxis: {
                    type: 'time',
                    title: {
                        text: 'Tiempo',
                    },
                },
                yaxis: {
                    title: {
                        text: 'Lectura',
                    },
                    opposite: false
                },
                legend: {
                    horizontalAlign: 'left'
                }
            },
        }
    },
    components: {
        BasicArea
    },
    props: {
        variableName: String,
        data: Object, // Se espera un objeto con pares clave-valor
        width: String,
        height: String,
    },
    watch: {
        data: {
            immediate: true, // Ejecutar también la primera vez que se monta el componente
            handler(newData) {
                // Actualizar 'series.data' y 'chartOptions.labels' dinámicamente
                this.series[0].data = Object.values(newData);
                this.chartOptions.labels = Object.keys(newData);
            }
        }
    },
}
</script>
