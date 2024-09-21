<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-[350px]">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <div class="flex items-center space-x-1">
                <p class="text-[#6D6E72] font-bold text-sm">VELOCIDAD</p>
                <el-tooltip
                    placement="top">
                    <template #content>
                        <p>
                            Bolsas por minuto de la máquina, representada en función de los <br>
                            días seleccionados. El eje X muestra los días según las <br>
                            fechas elegidas, mientras que el eje Y indica la cantidad de <br>
                            bolsas por minuto (bpm) promedio.
                        </p>
                    </template>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </el-tooltip>
            </div>
            <BasicArea :series="getChartSeries" :chartOptions="updatedChartOptions" :width="width" />
        </div>
    </main>
</template>

<script>
import BasicArea from '@/MyComponents/Chart/Area/BasicArea.vue';
import { format } from 'date-fns';

export default {
    data() {
        return {
            chartOptions: {
                stroke: {
                    curve: 'straight',
                    colors: ['#A24917'] // Cambia el color de la línea (en este caso, rojo)
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
                                color: '#FF0000', // Color inicial del gradiente (rojo)
                                opacity: 0.5
                            },
                            {
                                offset: 100,
                                color: '#FFA500', // Color final del gradiente (naranja)
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
                xaxis: {
                    type: 'datetime',
                    title: {
                        text: 'Tiempo',
                    },
                },
                yaxis: {
                    title: {
                        text: 'BPM (Bolsa por minuto)',
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
        items: {
            default: [],
            required: true,
            type: Array,
        },
        loading: Boolean, //estado de carga al obtener las datos
        width: String,
    },
    computed: {
        uniqueFormattedDates() {
            const dates = this.items.map(item => format(new Date(item.created_at), 'yyyy-MM-dd'));
            const uniqueDates = [...new Set(dates)];
            uniqueDates.sort((a, b) => new Date(a) - new Date(b));
            return uniqueDates;
        },
        getChartSeries() {
            const averages = this.uniqueFormattedDates.map(date => {
                const filteredItems = this.items.filter(item => format(new Date(item.created_at), 'yyyy-MM-dd') === date);
                const totalBagsPerMinute = filteredItems.reduce((sum, item) => sum + (parseFloat(item.bags_per_minute) || 0), 0);
                const average = totalBagsPerMinute / filteredItems.length || 0;
                return average.toFixed(2); // Redondea a dos decimales
            });
            return [{ name: "Bolsas por minuto", data: averages }];
        },
        updatedChartOptions() {
            return {
                ...this.chartOptions,
                labels: this.uniqueFormattedDates
            };
        },
    }
}
</script>
