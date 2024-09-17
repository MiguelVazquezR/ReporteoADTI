<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-[350px]">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>
        <div v-else>
            {{ items.map(i => i.created_at) }}
            <p class="text-[#6D6E72] font-bold text-sm">{{ variableName }}</p>
            <BasicArea :series="series" :chartOptions="chartOptions" />
        </div>
    </main>
</template>

<script>
import BasicArea from '@/MyComponents/Chart/Area/BasicArea.vue';
import axios from 'axios';
import { format } from 'date-fns';

export default {
    data() {
        return {
            items: [],
            series: [{
                name: "Lectura",
                data: [34, 44, 54, 21, 12, 43, 33, 23] // Datos de ejemplo
            }],
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
                labels: ['6:00', '6:15', '6:30', '6:45', '7:00', '7:15', '7:30', '8:00'],
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
            loading: false, //estado de carga al obtener las datos
        }
    },
    components: {
        BasicArea
    },
    props: {
        variableName: String,
        date: String,
        timeLabels: Array,
    },
    computed: {

    },
    methods: {
        async fetchMachineData() {
            try {
                this.loading = true;

                const response = await axios.get(route('robag.get-variable-data', {
                     date: this.date,
                    timeRange: [this.timeLabels[0], this.timeLabels[this.timeLabels.length - 1]]
                }));

                if (response.status === 200) {
                    this.items = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        }
    },
    // async mounted() {
    //     await this.fetchMachineData();
    // }
}
</script>
