<template>
    <section class="flex space-x-4 w-full min-h-screen">
        <!-- Imagen de la maquina (parte izquierda) -->
        <figure class="w-1/4">
            <h1 class="font-bold text-xl mb-6 ml-4">ROBAG</h1>
            <img class="rounded-[20px] border border-grayD9 p-4 w-full" src="@/../../public/images/machine_1.png"
                alt="">
        </figure>

        <!-- Infomracion de producción (parte derecha) -->
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
                *No hay datos para
                este intervalo de tiempo</h1>

            <!-- graficas en rectangulo negro -->
            <OEEPanel :date="searchDate" :items="data" :loading="loading" :teoricProduction="bpm"
                :bpmUpdated="bpmUpdated" @finished-bpm-updated="bpmUpdated = false" />

            <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
            <div class="mt-4 space-y-4">

                <!-- primer fila -->
                <div class="flex space-x-4">
                    <!-- Tiempos -->
                    <TimePanel :date="searchDate" :items="data" :loading="loading" />

                    <!-- PRODUCCIÓN DIARIA -->
                    <ProductionPanel :items="data" :loading="loading" />
                </div>

                <!-- Segunda fila -->
                <div class="flex space-x-4">
                    <!-- Velocidad -->
                    <VelocityPanel :items="data" :loading="loading" />

                    <!-- HISTOGRAMA -->
                    <DesviacionPanel :items="data" :loading="loading" />
                </div>

                <!-- Tercera fila -->
                <div class="flex space-x-4">
                    <!-- PELICULA -->
                    <FilmPanel :items="data" :loading="loading" />

                    <!-- BASCULA -->
                    <ScalePanel :items="data" :loading="loading" />
                </div>
            </div>
        </article>
    </section>
</template>
<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import OEEPanel from '@/MyComponents/Home/OEEPanel.vue';
import TimePanel from '@/MyComponents/Home/TimePanel.vue';
import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';
import { Link, useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    data() {
        return {
            // modales
            showEmailModal: false,
            // cargas
            loading: false,
            // general
            editModbusConfig: false,
            // bpm: 120, //bpm a maxima velocidad ajustable
            data: [],
            activeTab: '1',
            searchDate: null,
            startDate: null, //vista movil
            finishDate: null, //vista movil
            bpmUpdated: false, //bandera que dispara evento de calculo de OEE cuando se cambia el bpm
        }
    },
    components: {
        PrimaryButton,
        ProductionPanel,
        DesviacionPanel,
        VelocityPanel,
        ScalePanel,
        TimePanel,
        FilmPanel,
        OEEPanel,
        DialogModal,
        InputError,
        InputLabel,
        Link,
    },
    props: {
        bpm: Number,
        // schedule_settings: {
        //     type: Object,
        //     default: null,
        // },
        // modbus_configurations: Object,
    },
    methods: {
        // calncelEditingModbusConf() {
        //     this.editModbusConfig = false;
        //     this.modbusForm.reset();
        // },
        // updateModbusConf() {
        //     this.modbusForm.put(route('modbus-configuration.update', this.modbus_configurations), {
        //         onSuccess: () => {
        //             this.$notify({
        //                 title: "Configuraciones de modbus actualizadas",
        //                 message: "",
        //                 type: "success"
        //             })
        //         },
        //         onFinish: () => {
        //             this.editModbusConfig = false;
        //         }
        //     });
        // },
        // openScheduleSettings() {
        //     if (this.schedule_settings === null) {
        //         this.$inertia.get(route('schedule-email-settings.create'));
        //     } else {
        //         this.$inertia.get(route('schedule-email-settings.edit', this.schedule_settings));
        //     }
        // },
        // getFileName() {
        //     const now = new Date();
        //     return `reporte_robag_${now.getFullYear()}_${String(now.getMonth() + 1).padStart(2, '0')}_${String(now.getDate()).padStart(2, '0')}_${String(now.getHours()).padStart(2, '0')}_${String(now.getMinutes()).padStart(2, '0')}_${String(now.getSeconds()).padStart(2, '0')}.xlsx`;
        // },
        // sendEmail() {
        //     this.emailForm.transform(data => ({
        //         ...data,
        //         dates: this.searchDate,
        //     })).post(route('robag.email-report'), {
        //         onSuccess: () => {
        //             this.showEmailModal = false;
        //             this.emailForm.reset();
        //             this.$notify({
        //                 title: 'Correo enviado',
        //                 message: '',
        //                 type: 'success'
        //             })
        //         },
        //         onError: (error) => {
        //             console.log(error);
        //         },
        //     });
        // },
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
        // handleDropdownCommand(command) {
        //     if (command == 'email') {
        //         this.showEmailModal = true;
        //     }
        // },
        // exportReport() {
        //     const url = route('robag.export-report', { dates: this.searchDate });
        //     window.open(url, '_blank');
        // },
        async getDataByDateRange() {
            this.loading = true;
            try {
                const response = await axios.post(route('robag.get-data-by-date-range'), { date: this.searchDate });
                if (response.status === 200) {
                    this.data = response.data.data;
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