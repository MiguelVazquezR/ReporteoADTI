<template>
    <PublicLayout title="Inicio">
        <!-- Botones -->
        <section class="flex items-center justify-end space-x-2 mt-6 lg:px-14">
            <el-dropdown trigger="click" class="mr-6">
                <button class="flex items-center space-x-2 text-black focus:border-0 focus:outline-none">
                    <h1 class="font-bold text-2xl">{{ machines.find(m => m.in_view).name }}</h1>
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item v-for="machine in machines" @click="changeMachineInView(machine)"
                            :key="machine.id"
                            :class="machines.find(m => m.in_view).name === machine.name ? '!text-primary font-bold' : ''">
                            {{ machine.name }}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
            <PrimaryButton :disabled="!searchDate.length" @click="openReport">Generar reporte</PrimaryButton>
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
                            <h3 class="font-bold mx-3 w-72">
                                Producción teórica ({{ bpm }} BPM)
                            </h3>
                            <el-slider v-model="bpm" :min="50" :max="150" :step="5" show-stops
                                :disabled="!searchDate.length" />
                        </div>
                        <div @click="$inertia.visit(route('machine-variables.index'))"
                            class="flex items-center justify-between mx-3 my-2 px-4 border-t border-grayD9 pt-1 cursor-pointer">
                            <span>Variables</span>
                            <i class="fa-solid fa-chevron-right text-primary text-[10px]"></i>
                        </div>
                        <div @click="$inertia.visit(route('schedule-email-settings.index'))"
                            class="flex items-center justify-between mx-3 mb-2 px-4 border-t border-grayD9 pt-1 cursor-pointer">
                            <span>Reportes por correo</span>
                            <i class="fa-solid fa-chevron-right text-primary text-[10px]"></i>
                        </div>
                        <div @click="openMetabase()"
                            class="flex items-center justify-between mx-3 mb-2 px-4 border-t border-grayD9 pt-1 cursor-pointer">
                            <span>Ir a análisis de datos en Metabase</span>
                            <i class="fa-solid fa-chevron-right text-primary text-[10px]"></i>
                        </div>
                        <div @click="$inertia.visit(route('tutorials.index'))"
                            class="flex items-center justify-between mx-3 mb-2 px-4 border-t border-grayD9 pt-1 cursor-pointer">
                            <span>Tutoriales</span>
                            <i class="fa-solid fa-chevron-right text-primary text-[10px]"></i>
                        </div>
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
                    <General ref="general" @updated-dates="searchDate = $event" :bpm="bpm"
                        :machine="machines.find(m => m.in_view)" />
                </el-tab-pane>
                <el-tab-pane name="2">
                    <template #label>
                        <span>Reporte de variables</span>
                    </template>
                    <Variables ref="variables" />
                </el-tab-pane>
            </el-tabs>
        </main>
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

        return {
            // formularios
            emailForm,
            // modbusForm,
            bpm: 120, //bpm a maxima velocidad ajustable
            // cargas
            loading: false,
            // general
            searchDate: [],
            activeTab: '1',
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
        machines: Array,
    },
    methods: {
        openMetabase() {
            window.open('http://localhost:3000', '_blank');
        },
        changeMachineInView(machine) {
            if (machine.in_view) {
                return;
            }
            this.emailForm.put(route('machines.update-in-view', machine), {
                onSuccess: () => {
                    this.$notify({
                        title: "Máquina cambiada",
                        type: "success"
                    });

                    this.$refs.variables.fetchMachineVariables();
                    this.$refs.variables.generateTimeSlots();
                    this.$refs.variables.fetchMachineData();
                    this.$refs.general.getDataByDateRange();
                }
            });
        },
        handleClick(tab) {
            // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
            const currentURL = new URL(window.location.href);
            currentURL.searchParams.set('currentTab', tab.props.name);
            // Actualiza la URL
            window.history.replaceState({}, document.title, currentURL.href);
        },
        openReport() {
            const url = route('machine-data.pdf-template', {
                dates: this.searchDate,
                bpm: this.bpm,
                date: this.$refs.variables.date,
                timeSlots: this.$refs.variables.timeSlots,
                selectedVariables: this.$refs.variables.selectedVariables,
            });
            this.$inertia.visit(url);
        },
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
