<template>
    <PublicLayout title="Inicio">
        <main class="lg:py-10 lg:px-14">

            <!-- Cuerpo del documento -->
            <section class="flex space-x-4 w-full">

                <!-- Imagen de la maquina (parte izquierda) -->
                <figure class="w-1/4">
                    <h1 class="font-bold text-xl mb-6 ml-4">Empacadora TNA</h1>
                    <img class="rounded-[20px] border border-grayD9 p-4 w-full"
                        src="@/../../public/images/machine_1.png" alt="">
                </figure>

                <!-- Infomracion de producción (parte derecha) -->
                <article class="w-3/4">
                    <div class="flex items-center justify-between space-x-3">
                        <div class="flex items-center space-x-4 lg:-left-40 z-10">
                            <div v-if="isMobile" class="flex items-center space-x-2">
                                <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                                    v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio" size="small" />
                                <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                                    v-model="finishDate" type="date" class="!w-1/2" placeholder="Final" size="small" />
                            </div>
                            <div v-else>
                                <el-date-picker @change="getDataByDateRange" v-model="searchDate" type="datetimerange"
                                    range-separator="A" start-placeholder="Fecha de inicio"
                                    end-placeholder="Fecha de fin" class="!w-full" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 w-96">
                            <InputLabel value="Producción teórica (BPM)" />
                            <!-- <el-input v-model="bpm" min="1" max="200" type="number" /> -->
                            <el-slider @change="bpmUpdated = true" v-model="bpm" :min="50" :max="150" :step="5"
                                show-stops />
                        </div>

                        <!-- Boton para generar reporte -->
                        <div>
                            <el-dropdown split-button type="primary" @click="exportReport"
                                @command="handleDropdownCommand">
                                Generar reporte
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item command="email">Enviar por correo</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>
                    </div>

                    <!-- graficas en rectangulo negro -->
                    <!-- <OEEPanel :date="searchDate" :data="data" :loading="loading" :teoricProduction="bpm"
                        :bpmUpdated="bpmUpdated" @finished-bpm-updated="bpmUpdated = false" /> -->

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
        </main>
        <DialogModal :show="showEmailModal" @close="showEmailModal = false">
            <template #title>
                <h1>Enviar reporte por correo</h1>
            </template>
            <template #content>
                <form @submit.prevent="sendEmail">
                    <div>
                        <InputLabel value="Correo electrónico detinatario*" />
                        <el-input v-model="emailForm.main_email" placeholder="Ej. admin@gmail.com" clearable />
                        <InputError :message="emailForm.errors.main_email" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="CCO" />
                        <el-select v-model="emailForm.cco" multiple filterable allow-create default-first-option
                            :reserve-keyword="false" placeholder="Agrega cualquier correo">
                            <el-option v-for="item in ccoList" :key="item.value" :label="item.label"
                                :value="item.value" />
                        </el-select>
                        <InputError :message="emailForm.errors.cco" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Asunto*" />
                        <el-input v-model="emailForm.subject" placeholder="Reporte de ..." clearable />
                        <InputError :message="emailForm.errors.subject" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Descripción del correo" />
                        <el-input v-model="emailForm.description" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                            placeholder="Escribe una descripción del producto si es necesario" clearable />
                        <InputError :message="emailForm.errors.description" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Adjunto" />
                        <p class="text-secondary text-xs">Reporte Robag 03_ago_2024 a 23_ago_2024.xls</p>
                    </div>
                </form>
            </template>
            <template #footer>
                <PrimaryButton @click="sendEmail">Enviar correo</PrimaryButton>
            </template>
        </DialogModal>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import OEEPanel from '@/MyComponents/Home/OEEPanel.vue';
import TimePanel from '@/MyComponents/Home/TimePanel.vue';
import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    data() {
        const emailForm = useForm({
            main_email: null,
            cco: [],
            subject: null,
            description: null,
        });

        return {
            // formularios
            emailForm,
            // modales
            showEmailModal: false,
            // general
            loading: false,
            bpm: 120, //bpm a maxima velocidad ajustable
            data: [],
            ccoList: [],
            activeTab: '1',
            searchDate: null,
            startDate: null, //vista movil
            finishDate: null, //vista movil
            bpmUpdated: false, //bandera que dispara evento de calculo de OEE cuando se cambia el bpm
        }
    },
    components: {
        PublicLayout,
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
    },
    props: {

    },
    methods: {
        sendEmail() {

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
        handleDropdownCommand(command) {
            if (command == 'email') {
                this.showEmailModal = true;
            }
        },
        exportReport() {
            const url = route('robag.export-report', { dates: this.searchDate });
            window.open(url, '_blank');
        },
        async getDataByDateRange() {
            this.loading = true;
            try {
                const response = await axios.post(route('robag.get-data-by-date-range'), { date: this.searchDate });
                if (response.status === 200) {
                    this.data = response.data.data;
                    // console.log(this.data);
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
