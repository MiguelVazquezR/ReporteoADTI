<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-[350px]">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <p class="text-[#6D6E72] font-bold text-sm">VELOCIDAD</p>
            <BasicArea :series="getChartSeries" :chartOptions="updatedChartOptions" />
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
                        text: 'Historial de bolsas',
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
