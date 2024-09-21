<template>
    <section class="flex space-x-4 w-full min-h-screen mb-10">
        <!-- Imagen de la maquina -->
        <figure class="w-1/4">
            <h1 class="font-bold text-xl mb-6 ml-4">ROBAG</h1>
            <img class="rounded-[20px] border border-grayD9 p-4 w-full" src="@/../../public/images/machine_1.png"
                alt="">
        </figure>

        <!-- graficas -->
        <article class="w-3/4">
            <div class="flex items-center justify-between space-x-3">
                <div class="flex items-center space-x-4 lg:-left-40 z-10">
                    <div v-if="isMobile" class="flex items-center space-x-2">
                        <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                            v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio"
                            format="DD MMM, YY h:mmA" size="small" />
                        <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                            v-model="finishDate" type="date" class="!w-1/2" placeholder="Final"
                            format="DD MMM, YY h:mmA" size="small" />
                    </div>
                    <div v-else>
                        <el-date-picker @change="getDataByDateRange" v-model="searchDate" type="datetimerange"
                            range-separator="A" start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin"
                            class="!w-full" format="DD MMM, YY h:mmA" />
                    </div>
                </div>
            </div>

            <h1 v-if="!data.length" class="text-blue-600 font-bold text-sm text-center py-1 mt-2 bg-blue-100">
                *No hay datos para este intervalo de tiempo
            </h1>

            <OEEPanel ref="oeePanel" :date="searchDate" :items="data" :loading="loading" :teoricProduction="bpm" />
            <div class="mt-4 space-y-4">
                <div class="flex space-x-4">
                    <TimePanel :date="searchDate" :items="data" :loading="loading" class="w-1/3" />
                    <ProductionPanel :items="data" :loading="loading" class="w-2/3" />
                </div>
                <div class="flex space-x-4">
                    <VelocityPanel :items="data" height="260" :loading="loading" class="w-1/2" />
                    <DesviacionPanel :items="data" :loading="loading" class="w-1/2" />
                </div>
                <div class="flex space-x-4">
                    <FilmPanel :items="data" :loading="loading" class="w-2/5" />
                    <ScalePanel :items="data" :loading="loading" class="w-3/5" />
                </div>
            </div>
        </article>
    </section>
</template>
<script>
import OEEPanel from '@/MyComponents/Home/OEEPanel.vue';
import TimePanel from '@/MyComponents/Home/TimePanel.vue';
import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';

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
            searchDate: null,
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
    },
    emits: ['updated-dates'],
    props: {
        bpm: Number,
    },
    watch: {
        bpm() {
            this.$refs.oeePanel.updateOEEData();
        }
    },
    methods: {
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
                const response = await axios.post(route('robag.get-data-by-date-range'), { date: this.searchDate });
                if (response.status === 200) {
                    this.data = response.data.data;
                    this.$emit(
                        'updated-dates',
                        this.data.length ? this.searchDate : []
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
    created() {
        const today = new Date(); // Obtiene la fecha y hora actuales

        // Crear la primera fecha con la hora 6:00 AM
        const startDate = new Date(today);
        startDate.setHours(6, 0, 0, 0); // Establecer hora a 6:00 AM

        // Crear la segunda fecha con la hora actual
        const endDate = new Date(today);
        endDate.setHours(today.getHours(), today.getMinutes(), today.getSeconds(), today.getMilliseconds()); // Establecer la hora actual

        // Inicializar searchDate con las dos fechas
        this.searchDate = [startDate, endDate];

        this.getDataByDateRange(); // Recupera los registros del día de hoy
    }
}
</script>