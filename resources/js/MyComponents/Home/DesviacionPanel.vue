<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-[350px]">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <p class="text-[#6D6E72] font-bold text-sm">HISTOGRAMA</p>
            <WithRotatedLabels :series="updatedSeries" :chartOptions="updatedChartOptions" :width="width" />
        </div>
    </main>
</template>

<script>
import WithRotatedLabels from '@/MyComponents/Chart/Column/WithRotatedLabels.vue';

export default {
    data() {
        return {
            chartOptions: {
                annotations: {
                    points: [{
                        x: 'Desviación',
                        seriesIndex: 0,
                        label: {
                            borderColor: '#000',
                            offsetY: 0,
                            style: {
                                color: '#fff',
                                background: '#775DD0',
                            },
                            text: 'Muestras',
                        }
                    }]
                },
                chart: {
                    height: 280,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 0
                },
                grid: {
                    row: {
                        colors: ['#fff', '#f2f2f2']
                    }
                },
                // xaxis: {
                //     title: {
                //         text: 'Desviación',
                //     },
                //     labels: {
                //         rotate: -45
                //     },
                //     // categories: [-4, -3, -2, -1, 0, 1, 2, 3, 4],
                //     tickPlacement: 'on'
                // },
                yaxis: {
                    title: {
                        text: 'Núm de muestras',
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.85,
                        opacityTo: 0.85,
                        colorStops: [
                            {
                                offset: 0,
                                color: '#19B38E',
                                opacity: 0.5
                            },
                            {
                                offset: 50,
                                color: '#17A281',
                                opacity: 0.9
                            }
                        ],
                    },
                }
            },
        }
    },
    components: {
        WithRotatedLabels
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        },
        loading: Boolean, //estado de carga al obtener las datos
        width: String,
    },
    computed: {
        getDeviationCounts() {
            // Inicializar un objeto para contar las desviaciones
            const deviationCounts = {};

            // Recorrer todos los elementos
            this.items.forEach(item => {
                // Redondear la desviación estándar
                const roundedDeviation = Math.round(item.standard_deviation);

                // Si la desviación redondeada ya existe en deviationCounts, aumentar el conteo
                // Si no, inicializar el conteo en 1
                if (deviationCounts[roundedDeviation]) {
                    deviationCounts[roundedDeviation] += 1;
                } else {
                    deviationCounts[roundedDeviation] = 1;
                }
            });

            // Ordenar las desviaciones por clave
            const sortedDeviations = Object.keys(deviationCounts)
                .map(key => parseInt(key))
                .sort((a, b) => a - b);

            // Crear el objeto final con las desviaciones y los conteos
            const result = {
                deviations: sortedDeviations,
                counts: sortedDeviations.map(deviation => deviationCounts[deviation])
            };

            return result;
        },
        updatedChartOptions() {
            return {
                ...this.chartOptions,
                xaxis: {
                    title: {
                        text: 'Desviación',
                    },
                    labels: {
                        rotate: -45
                    },
                    categories: this.getDeviationCounts.deviations,
                    tickPlacement: 'on'
                },
            };
        },
        updatedSeries() {
            return [{
                name: 'Muestras',
                data: this.getDeviationCounts.counts
            }];
        }
    },
}
</script>
