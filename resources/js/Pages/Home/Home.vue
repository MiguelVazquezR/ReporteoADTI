<template>
    <PublicLayout title="Inicio">
        <!-- Botones -->
        <section class="flex items-center justify-end space-x-2 mt-6 lg:px-14">
            <el-dropdown split-button type="primary" @click="exportReport" @command="handleDropdownCommand"
                :disabled="!searchDate.length">
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
                <template #dropdown>
                    <el-dropdown-menu>
                        <h2 class="flex items-center space-x-1 font-bold text-secondary mx-4 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
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
                            <el-slider v-model="bpm" :min="50" :max="150" :step="5" show-stops
                                :disabled="!searchDate.length" />
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
                                    <span v-for="(item, index) in schedule_settings.cco" :key="index">{{
                                        item }}</span>
                                    <span v-if="!schedule_settings.cco.length">No hay cuentas copiadas
                                        al correo</span>
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
                        <h2 class="flex items-center justify-between mt-2 mx-6">
                            <span class="font-bold">Configuración de Modbus</span>
                            <div v-if="editModbusConfig" class="flex items-center space-x-1">
                                <PrimaryButton @click="calncelEditingModbusConf"
                                    class="!text-[10px] !py-px !px-2 !bg-grayED !text-secondary"
                                    :disabled="modbusForm.processing">Cancelar</PrimaryButton>
                                <PrimaryButton @click="updateModbusConf" class="!text-[10px] !py-px !px-2"
                                    :disabled="modbusForm.processing || !modbusForm.port || !modbusForm.host">
                                    Guardar</PrimaryButton>
                            </div>
                            <button v-else @click="editModbusConfig = true"
                                class="size-5 flex items-center justify-center rounded-full bg-grayED text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </button>
                        </h2>
                        <section v-if="editModbusConfig" class=" mx-3 mb-2 px-4">
                            <article class="*:grid *:grid-cols-3 *:mb-1 mt-2">
                                <div>
                                    <span>IP</span>
                                    <el-input v-model="modbusForm.host" placeholder="Ej. 127.0.0.1"
                                        class="col-span-2" />
                                </div>
                                <div>
                                    <span>Puerto</span>
                                    <el-input v-model="modbusForm.port" placeholder="Ej. 502" class="col-span-2" />
                                </div>
                            </article>
                        </section>
                        <section v-else class="mx-3 mb-2 px-4">
                            <article class="*:grid *:grid-cols-3 *:mb-1 mt-2">
                                <div>
                                    <span>IP</span>
                                    <span>{{ modbusForm.host }}</span>
                                </div>
                                <div>
                                    <span>Puerto</span>
                                    <span>{{ modbusForm.port }}</span>
                                </div>
                            </article>
                        </section>
                        <p class="grid grid-cols-3 mx-3 mb-2 px-4">
                            <span>Variables</span>
                            <Link :href="route('machine-variables.index')" class="text-primary">
                            Configurar
                            </Link>
                            <button @click="openModbusMonitor" type="button" class="text-primary">
                                Lectura tiempo real
                            </button>
                        </p>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </section>
        <!-- pestañas -->
        <main class="lg:px-14">
            <el-tabs v-model="activeTab" @tab-click="handleClick">
                <el-tab-pane name="1">
                    <template #label>
                        <span>Reporte general</span>
                    </template>
                    <General @updated-dates="searchDate = $event" :bpm="bpm" />
                </el-tab-pane>
                <el-tab-pane name="2">
                    <template #label>
                        <span>Reporte de variables</span>
                    </template>
                    <Variables />
                </el-tab-pane>
            </el-tabs>
        </main>

        <DialogModal :show="showRealTimeModbusMonitor" @close="closeModbusMonitor">
            <template #title>
                <h1>Monitor de registros en tiempo real</h1>
            </template>
            <template #content>
                <section v-if="modbusData !== null" class="mt-5">
                    <div v-if="Object.keys(modbusData)[0] == 'error'"
                        class="flex flex-col items-center justify-center space-y-3 py-10">
                        <p class="text-center">
                            Error al intentar establecer conexión con {{ this.modbus_configurations.host }}:{{
                                this.modbus_configurations.port }}. <br>
                            Revisa que la IP y el puerto sean correctos. También que la red no presente ninguna falla
                        </p>
                        <!-- <i class="fa-solid fa-network-wired text-4xl"></i> -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-10">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.181 8.68a4.503 4.503 0 0 1 1.903 6.405m-9.768-2.782L3.56 14.06a4.5 4.5 0 0 0 6.364 6.365l3.129-3.129m5.614-5.615 1.757-1.757a4.5 4.5 0 0 0-6.364-6.365l-4.5 4.5c-.258.26-.479.541-.661.84m1.903 6.405a4.495 4.495 0 0 1-1.242-.88 4.483 4.483 0 0 1-1.062-1.683m6.587 2.345 5.907 5.907m-5.907-5.907L8.898 8.898M2.991 2.99 8.898 8.9" />
                        </svg>

                    </div>
                    <div v-else>
                        <PrimaryButton @click="pausedMonitor = !pausedMonitor" class="mb-3"
                            :class="pausedMonitor ? '!bg-green-600' : '!bg-blue-400'">
                            {{ pausedMonitor ? 'Descongelar' : 'Congelar' }}
                        </PrimaryButton>
                        <article v-for="(item, index) in variables" :key="index"
                            class="grid grid-cols-3 gap-x-6 gap-y-1">
                            <span>{{ item.variable_name }}:</span>
                            <span class="col-span-2">{{ modbusData[item.variable_original_name] }}</span>
                        </article>
                    </div>
                </section>
                <div v-else class="text-xs my-4 text-center mt-16">
                    Obteniendo datos... <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </div>
            </template>
            <template #footer>
                <!-- <CancelButton @click="showRealTimeModbusMonitor = false">
                    Cerrar
                </CancelButton> -->
            </template>
        </DialogModal>

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
import { Link, useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import General from './Tabs/General.vue';
import Variables from './Tabs/Variables.vue';

export default {
    data() {
        const emailForm = useForm({
            main_email: null,
            cco: [],
            subject: null,
            description: null,
        });

        const modbusForm = useForm({
            host: this.modbus_configurations.host,
            port: this.modbus_configurations.port,
            machine: 'Robag',
        });

        return {
            // formularios
            emailForm,
            modbusForm,
            bpm: 120, //bpm a maxima velocidad ajustable
            // modales
            showEmailModal: false,
            showRealTimeModbusMonitor: false,
            // cargas
            loading: false,
            // general
            editModbusConfig: false,
            searchDate: [],
            activeTab: '1',
            // monitor de modbus
            modbusData: null,
            intervalId: null,
            pausedMonitor: false,
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        DialogModal,
        InputError,
        InputLabel,
        Link,
        General,
        Variables,
    },
    props: {
        schedule_settings: {
            type: Object,
            default: null,
        },
        modbus_configurations: Object,
        variables: Array,
    },
    methods: {
        openModbusMonitor() {
            this.showRealTimeModbusMonitor = true;

            // Ejecutar fetchMachineModbusRegisters cada x mili segundos
            this.intervalId = setInterval(async () => {
                if (!this.pausedMonitor) {
                    await this.fetchMachineModbusRegisters();
                }
            }, 2000);
        },
        closeModbusMonitor() {
            this.showRealTimeModbusMonitor = false;
            this.pausedMonitor = false;

            // Limpiar el intervalo cuando se cierra el modal
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }
        },
        handleClick(tab) {
            // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
            const currentURL = new URL(window.location.href);
            currentURL.searchParams.set('currentTab', tab.props.name);
            // Actualiza la URL
            window.history.replaceState({}, document.title, currentURL.href);
        },
        calncelEditingModbusConf() {
            this.editModbusConfig = false;
            this.modbusForm.reset();
        },
        updateModbusConf() {
            this.modbusForm.put(route('modbus-configuration.update', this.modbus_configurations), {
                onSuccess: () => {
                    this.$notify({
                        title: "Configuraciones de modbus actualizadas",
                        message: "",
                        type: "success"
                    })
                },
                onFinish: () => {
                    this.editModbusConfig = false;
                }
            });
        },
        openScheduleSettings() {
            if (this.schedule_settings === null) {
                this.$inertia.get(route('schedule-email-settings.create'));
            } else {
                this.$inertia.get(route('schedule-email-settings.edit', this.schedule_settings));
            }
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
        handleDropdownCommand(command) {
            if (command == 'email') {
                this.showEmailModal = true;
            }
        },
        exportReport() {
            const url = route('robag.export-report', { dates: this.searchDate });
            window.open(url, '_blank');
        },
        async fetchMachineModbusRegisters() {
            try {
                const response = await axios.get(route('robag.get-modbus-registers'));

                if (response.status === 200) {
                    this.modbusData = response.data.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    computed: {
        isMobile() {
            return window.innerWidth < 768;
        }
    },
    mounted() {
        // Obtener la URL actual
        const currentURL = new URL(window.location.href);
        // Extraer el valor de 'currentTab' de los parámetros de búsqueda
        const currentTabFromURL = currentURL.searchParams.get('currentTab');

        if (currentTabFromURL) {
            this.activeTab = currentTabFromURL;
        }
    },
}
</script>
