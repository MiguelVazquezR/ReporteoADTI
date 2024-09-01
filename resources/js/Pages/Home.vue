<template>
    <PublicLayout title="Inicio">
        <main class="lg:py-10 lg:px-14">

            <!-- Cuerpo del documento -->
            <section class="flex space-x-4 w-full">

                <!-- Imagen de la maquina (parte izquierda) -->
                <figure class="w-1/4">
                    <h1 class="font-bold text-xl mb-6 ml-4">ROBAG</h1>
                    <img class="rounded-[20px] border border-grayD9 p-4 w-full"
                        src="@/../../public/images/machine_1.png" alt="">
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
                                    range-separator="A" start-placeholder="Fecha de inicio"
                                    end-placeholder="Fecha de fin" class="!w-full" format="DD MMM, YY h:mmA" />
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-2">
                            <el-dropdown split-button type="primary" @click="exportReport"
                                @command="handleDropdownCommand" :disabled="!data.length">
                                Generar reporte
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item command="email">Enviar por correo</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                            <el-dropdown trigger="click">
                                <button
                                    class="flex items-center justify-center text-secondary rounded-full bg-grayED size-8 focus:border-0 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <h2 class="flex items-center space-x-1 font-bold text-secondary mx-4 mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>Configuraciones</span>
                                        </h2>
                                        <div class="mx-3 mt-4">
                                            <h3 class="font-bold mx-3">
                                                Producción teórica ({{ bpm }} BPM)
                                            </h3>
                                            <el-slider @change="bpmUpdated = true" v-model="bpm" :min="50" :max="150"
                                                :step="5" show-stops :disabled="!data.length" />
                                        </div>
                                        <h2 class="flex items-center space-x-2 font-bold mt-2 mx-6">
                                            Envio de reporte automático
                                        </h2>
                                        <section @click="openScheduleSettings"
                                            class="group w-96 rounded-[10px] bg-[#E5F4FB] px-4 py-2 mx-3 mb-2 grid grid-cols-10 cursor-pointer hover:bg-[#daeef8]">
                                            <article v-if="schedule_settings" class="col-span-9">
                                                <div>
                                                    <h4 class="font-bold text-gray37">Correo electrónico</h4>
                                                    <span>{{ schedule_settings.main_email }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <h4 class="font-bold text-gray37">CCO</h4>
                                                    <span v-for="(item, index) in schedule_settings.cco" :key="index">{{ item }}</span>
                                                    <span v-if="!schedule_settings.cco.length">No hay cuentas copiadas al correo</span>
                                                </div>
                                                <hr class="border border-grayD9 my-3">
                                                <div>
                                                    <h4 class="font-bold text-gray37">Frecuencia de envío</h4>
                                                    <span>{{ schedule_settings.frecuency }}</span>
                                                </div>
                                            </article>
                                            <article v-else class="col-span-9">
                                                <p class="text-center">
                                                    No hay programación de correos aún. <br>
                                                    Da Clic para crear una
                                                </p>
                                            </article>
                                            <article
                                                class="text-center flex items-center justify-end transform transition-transform group-hover:scale-110 group-hover:translate-x-1">
                                                <i class="fa-solid fa-chevron-right text-primary text-[10px]"></i>
                                            </article>
                                        </section>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
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
                            :reserve-keyword="false" placeholder="Agrega cualquier correo y presiona enter">
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
                            placeholder="Escribe una descripción si es necesario" clearable />
                        <InputError :message="emailForm.errors.description" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Adjunto" />
                        <p class="text-xs flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 22 22" class="text-green-700"
                                id="Microsoft-Excel-Logo--Streamline-Ultimate" height="16" width="16">
                                <desc>Microsoft Excel Logo Streamline Icon: https://streamlinehq.com</desc>
                                <g id="Microsoft-Excel-Logo--Streamline-Ultimate.svg">
                                    <path
                                        d="M20.125 1.75H7.4375a0.875 0.875 0 0 0 -0.875 0.875v1.3125h1.75V3.5h4.375v2.9575h-0.875l0 0.105v1.58375h0.875v2.9575h-0.875v1.68875h0.875V15.75h-1.2425a2.625 2.625 0 0 1 -2.2575000000000003 1.3125h-2.625V18.375a0.875 0.875 0 0 0 0.875 0.875H20.125a0.875 0.875 0 0 0 0.875 -0.875V2.625a0.875 0.875 0 0 0 -0.875 -0.875Zm-0.875 14h-4.375v-2.9575h4.375Zm0 -4.646249999999999h-4.375V8.14625h4.375Zm0 -4.646249999999999h-4.375V3.5h4.375Z"
                                        fill="currentColor" stroke-width="1"></path>
                                    <path
                                        d="M8.3125 15.75h0.875a1.3125 1.3125 0 0 0 1.3125 -1.3125v-7.875A1.3125 1.3125 0 0 0 9.1875 5.25h-7.875A1.3125 1.3125 0 0 0 0 6.5625v7.875A1.3125 1.3125 0 0 0 1.3125 15.75ZM4.1125 7.4375 5.25 9.2575 6.3875 7.4375h1.54875L6.02 10.5l1.91625 3.0625H6.3875L5.25 11.7425 4.1125 13.5625H2.56375L4.48 10.5 2.56375 7.4375Z"
                                        fill="currentColor" stroke-width="1"></path>
                                </g>
                            </svg>
                            <span class="text-secondary">Reporte Robag</span>
                        </p>
                        <!-- <p class="text-secondary text-xs">{{ getFileName() }}</p> -->
                    </div>
                </form>
            </template>
            <template #footer>
                <PrimaryButton @click="sendEmail" :disabled="emailForm.processing">
                    <i v-if="emailForm.processing" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                    Enviar correo
                </PrimaryButton>
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
        schedule_settings: {
            type: Object,
            default: null,
        }
    },
    methods: {
        openScheduleSettings() {
            if (this.schedule_settings === null) {
                this.$inertia.get(route('schedule-email-settings.create'));
            } else {
                this.$inertia.get(route('schedule-email-settings.edit', this.schedule_settings));
            }
        },
        getFileName() {
            const now = new Date();
            return `reporte_robag_${now.getFullYear()}_${String(now.getMonth() + 1).padStart(2, '0')}_${String(now.getDate()).padStart(2, '0')}_${String(now.getHours()).padStart(2, '0')}_${String(now.getMinutes()).padStart(2, '0')}_${String(now.getSeconds()).padStart(2, '0')}.xlsx`;
        },
        sendEmail() {
            this.emailForm.transform(data => ({
                ...data,
                dates: this.searchDate,
            })).post(route('robag.email-report'), {
                onSuccess: () => {
                    this.showEmailModal = false;
                    this.emailForm.reset();
                    this.$notify({
                        title: 'Correo enviado',
                        message: '',
                        type: 'success'
                    })
                },
                onError: (error) => {
                    console.log(error);
                },
            });
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
