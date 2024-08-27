<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/3">
        <div v-if="loading" class="text-sm my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <p class="text-[#6D6E72] font-bold text-sm">TIEMPOS</p>
            <CircleCustomAngle :series="maxTimeTotals" :chartOptions="chartOptions" />
        </div>
        <!-- {{ maxTimeTotals }} -->
    </main>
</template>

<script>
import { differenceInSeconds, format } from 'date-fns';
import CircleCustomAngle from '@/MyComponents/Chart/RadialBar/CircleCustomAngle.vue';

export default {
    data() {
        return {
            totalTime: null, //tiempo en minutos tomado del intervalo seleccionado en filtro
            chartOptions: {
                chart: {
                    height: 280,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        offsetY: 0,
                        startAngle: 0,
                        endAngle: 270,
                        hollow: {
                            margin: 5,
                            size: '30%',
                            background: 'transparent',
                            image: undefined,
                        },
                        track: {
                            background: "#e7e7e7",
                            strokeWidth: '97%',
                            margin: 5, // margin is in pixels
                            dropShadow: {
                                enabled: true,
                                top: 2,
                                left: 0,
                                color: '#999',
                                opacity: 1,
                                blur: 2
                            }
                        },
                        dataLabels: {
                            name: {
                                show: false,
                            },
                            value: {
                                show: false,
                            }
                        },
                        barLabels: {
                            enabled: true,
                            useSeriesColors: true,
                            offsetX: -8,
                            fontSize: '12px',
                            formatter: function (seriesName, opts) {
                                // Usar la función formatTime desde el contexto del componente
                                return seriesName + ":  " + this.formatTime(opts.w.globals.series[opts.seriesIndex] * this.totalTime);
                            }.bind(this) // Asegura que `this` dentro de `formatter` se refiere al componente Vue
                        },
                    }
                },
                colors: ['#1AA217', '#17A281', '#A24917'],
                labels: ['Ejecución', 'Pausado', 'Falla'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            show: false
                        }
                    }
                }]
            },
        }
    },
    components: {
        CircleCustomAngle
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        },
        date: Array, //Intervalo de fechas buscadas
        loading: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        uniqueFormattedDates() {
            // Función para obtener fechas únicas y ordenadas
            const dates = this.items.map(item => format(new Date(item.created_at), 'yyyy-MM-dd'));
            const uniqueDates = [...new Set(dates)];
            uniqueDates.sort((a, b) => new Date(a) - new Date(b));
            return uniqueDates;
        },
        maxTimeTotals() {
            // Función para obtener los valores máximos por propiedad y luego sumarlos
            // Inicializamos los totales en 0
            let totalRunTime = 0;
            let totalPausedTime = 0;
            let totalFaultTime = 0;

            // Recorremos las fechas únicas
            this.uniqueFormattedDates.forEach(date => {
                const filteredItems = this.items.filter(item => format(new Date(item.created_at), 'yyyy-MM-dd') === date);

                // Obtenemos los valores máximos de cada propiedad para la fecha actual
                const maxRunTime = Math.max(...filteredItems.map(item => parseFloat(item.run_time) || 0));
                const maxPausedTime = Math.max(...filteredItems.map(item => parseFloat(item.paused_time) || 0));
                const maxFaultTime = Math.max(...filteredItems.map(item => parseFloat(item.fault_time) || 0));

                // Sumamos los valores máximos al total correspondiente
                totalRunTime += maxRunTime;
                totalPausedTime += maxPausedTime;
                totalFaultTime += maxFaultTime;
            });

            // Devolvemos un array con los totales
            return [(totalRunTime / this.totalTime).toFixed(1), (totalPausedTime / this.totalTime).toFixed(1), (totalFaultTime / this.totalTime).toFixed(1)];
        }
    },
    methods: {
        formatTime(seconds) {
            const days = Math.floor(seconds / (24 * 3600)); // Un día tiene 86400 segundos
            const hours = Math.floor((seconds % (24 * 3600)) / 3600); // Horas restantes
            const minutes = Math.floor((seconds % 3600) / 60); // Minutos restantes
            const secs = Math.floor(seconds % 60); // Segundos restantes

            let result = '';

            if (days > 0) {
                result += `${days} ${days === 1 ? 'día' : 'días'}`;
            }
            if (hours > 0) {
                result += ` ${hours}h`;
            }
            if (minutes > 0) {
                result += ` ${minutes}m`;
            }
            if (secs > 0) {
                result += ` ${secs}s`;
            }

            return result.trim(); // Elimina espacios sobrantes al inicio y fin
        },
        getTotalTime() {
            if (this.date.length) {
                const start = new Date(this.date[0]);
                const end = new Date(this.date[1]);
    
                // Calcular la diferencia en segundos
                const differenceInSecondss = differenceInSeconds(end, start);
    
                // Guardar el resultado en el componente
                this.totalTime = differenceInSecondss/60;
            }
        }
    },
    watch: {
        date() {
            this.getTotalTime();
        }
    },
    mounted() {
        this.getTotalTime();
    }
}
</script>
