<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-80">
        <div v-if="loading" class="text-sm my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <div class="flex items-center space-x-1">
                <p class="text-[#6D6E72] font-bold text-sm">TIEMPOS</p>
                <el-tooltip placement="top">
                    <template #content>
                        <p>
                            Tiempos totales acumulados de la maquina (ejecución, pausado, <br>
                            en falla, sin pelicula e interlock) <br>
                            desde <span class="text-yellow-500">{{ interval[0] }}</span> a <span class="text-yellow-500">{{ interval[1] }}</span>
                        </p>
                    </template>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </el-tooltip>
            </div>
            <CircleCustomAngle :series="maxTimeTotals.percentage" :chartOptions="chartOptions" :width="width" />
        </div>
    </main>
</template>

<script>
import { differenceInSeconds, format } from 'date-fns';
import { es } from 'date-fns/locale';
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
                                return seriesName + ":  " + this.formatTime(opts.seriesIndex);
                            }.bind(this) // Asegura que `this` dentro de `formatter` se refiere al componente Vue
                        },
                    }
                },
                colors: ['#1AA217', '#17A281', '#A24917', '#A24917', '#A24917'],
                labels: ['Ejecución', 'Pausado', 'Falla', 'Sin película', 'Interlock'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            show: false
                        }
                    }
                }]
            },
            interval: [null, null],
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
        },
        width: String
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
            let totalOutOfFilmTime = 0;
            let totalInterlockTime = 0;

            // Recorremos las fechas únicas
            this.uniqueFormattedDates.forEach(date => {
                const filteredItems = this.items.filter(item => format(new Date(item.created_at), 'yyyy-MM-dd') === date);

                // Obtenemos los valores máximos de cada propiedad para la fecha actual
                const maxRunTime = Math.max(...filteredItems.map(item => parseFloat(item.data['Tiempo de ejecución']) || 0));
                const maxPausedTime = Math.max(...filteredItems.map(item => parseFloat(item.data['Tiempo pausado']) || 0));
                const maxFaultTime = Math.max(...filteredItems.map(item => parseFloat(item.data['Tiempo de falla']) || 0));
                const maxOutOfFilmTime = Math.max(...filteredItems.map(item => parseFloat(item.data['Tiempo sin película']) || 0));
                const maxInterlockTime = Math.max(...filteredItems.map(item => parseFloat(item.data['Tiempo de interlock']) || 0));

                // Sumamos los valores máximos al total correspondiente
                totalRunTime += maxRunTime;
                totalPausedTime += maxPausedTime;
                totalFaultTime += maxFaultTime;
                totalOutOfFilmTime += maxOutOfFilmTime;
                totalInterlockTime += maxInterlockTime;
            });

            // Devolvemos un array con los totales
            return {
                percentage: [
                    (totalRunTime * 100 / totalRunTime).toFixed(1),
                    (totalPausedTime * 100 / totalRunTime).toFixed(1),
                    (totalFaultTime * 100 / totalRunTime).toFixed(1),
                    (totalOutOfFilmTime * 100 / totalRunTime).toFixed(1),
                    (totalInterlockTime * 100 / totalRunTime).toFixed(1),
                ],
                seconds: [
                    (totalRunTime).toFixed(1),
                    (totalPausedTime).toFixed(1),
                    (totalFaultTime).toFixed(1),
                    (totalOutOfFilmTime).toFixed(1),
                    (totalInterlockTime).toFixed(1),
                ],
            };
        }
    },
    methods: {
        formatInterval() {
            this.interval = [
                format(new Date(this.date[0]), "dd MMM yyyy • hh:mm a", { locale: es }),
                format(new Date(this.date[1]), "dd MMM yyyy • hh:mm a", { locale: es }),
            ];
        },
        formatTime(index) {
            const seconds = this.maxTimeTotals.seconds[index];
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
                this.totalTime = differenceInSecondss;
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
        this.formatInterval();
    }
}
</script>
