<template>
    <Head title="Reporte Robag" />
    <header class="py-7 text-center font-bold">
        <p>
            Reporte de Robag: {{ formatDateTime(dates[0]) }} a {{ formatDateTime(dates[1]) }}
        </p>
        <button @click="downloadPdf" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3">Descargar PDF</button>
    </header>
    <main class="px-10 min-h-screen">
        <section class="h-full">
            <!-- graficas en rectangulo negro -->
            <OEEPanel ref="oeePanel" :date="dates" :items="data" :loading="loading" :teoricProduction="bpm" class="!h-1/2" />

            <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
            <div class="mt-4 space-y-4 h-screen">
                <!-- primer fila -->
                <div class="grid grid-cols-3 gap-x-4 !h-1/2 mb-10">
                    <!-- Tiempos -->
                    <TimePanel :date="dates" :items="data" :loading="loading" width="65%" />

                    <!-- PRODUCCIÓN DIARIA -->
                    <ProductionPanel :items="data" :loading="loading" width="65%" class="col-span-2" />
                </div>

                <!-- Segunda fila -->
                <div class="flex space-x-4">
                    <!-- Velocidad -->
                    <VelocityPanel :items="data" :loading="loading" class="w-1/2" width="65%" />

                    <!-- HISTOGRAMA -->
                    <DesviacionPanel :items="data" :loading="loading" class="w-1/2" width="65%" />
                </div>

                <!-- Tercera fila -->
                <div class="flex space-x-4">
                    <!-- PELICULA -->
                    <FilmPanel :items="data" :loading="loading" class="w-[45%]" />

                    <!-- BASCULA -->
                    <ScalePanel :items="data" :loading="loading" class="w-[55%]" width="65%" />
                </div>
            </div>
        </section>
    </main>
</template>
<script>
import OEEPanel from '@/MyComponents/Home/OEEPanel.vue';
import TimePanel from '@/MyComponents/Home/TimePanel.vue';
import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';
import { Head } from '@inertiajs/vue3';
import { format } from "date-fns";

export default {
    data() {
        return {
            // modales
            showEmailModal: false,
            // cargas
            loading: false,
            // general
            editModbusConfig: false,
            data: [],
            // dates: null,
            startDate: null, //vista movil
            finishDate: null, //vista movil
        }
    },
    components: {
        ProductionPanel,
        DesviacionPanel,
        VelocityPanel,
        ScalePanel,
        TimePanel,
        FilmPanel,
        OEEPanel,
        Head
    },
    emits: ['updated-dates'],
    props: {
        bpm: {
            default: 120,
            type: Number,
        },
        dates: Array,
    },
    watch: {
        bpm() {
            this.$refs.oeePanel.updateOEEData();
        }
    },
    methods: {
        downloadPdf() {
            window.location.href = '/download-pdf'; // Redirige a la ruta para descargar el PDF
        },
        formatDateTime(dateTime) {
            return format(dateTime, "dd MMM, yyyy - H:mm a");
        },
        handleStartDateChange(value) {
            this.startDate = value;
            // Si finishDate es nulo, aplica la regla de deshabilitación
            if (!this.finishDate) {
                this.disabledPrevDays();
            }
        },
        handleFinishDateChange(value) {
            this.finishDate = value;
            // Si startDate es nulo, aplica la regla de deshabilitación
            if (!this.startDate) {
                this.disabledNextDays();
            }
        },
        disabledPrevDays(date) {
            if (this.finishDate) {
                return date.getTime() > new Date(this.finishDate).getTime();
            }
            return false;
        },
        disabledNextDays(date) {
            if (this.startDate) {
                return date.getTime() < new Date(this.startDate).getTime();
            }
            return false;
        },
        async getDataByDateRange() {
            this.loading = true;
            try {
                const response = await axios.post(route('robag.get-data-by-date-range'), { date: this.dates });
                if (response.status === 200) {
                    this.data = response.data.data;
                    this.$emit(
                        'updated-dates',
                        this.data.length ? this.dates : []
                    );
                }

            } catch (error) {
                console.log(error)
            } finally {
                this.loading = false;
            }
        }
    },
    computed: {
        isMobile() {
            return window.innerWidth < 768;
        }
    },
    mounted() {
        this.getDataByDateRange(); // Recupera los registros del día de hoy
    }
}
</script>