<template>
    <main class="min-h-screen">
        <Loading v-if="loading" class="mt-16" />
        <section v-else class="w-full">
            <p class="text-secondary text-sm">
                En esta sección, encontrarás todas las variables disponibles junto con sus respectivas gráficas. Puedes
                seleccionar las variables que necesites, ajustar la fecha o el rango de fechas, y elegir la resolución
                que
                mejor se adapte a tu análisis.
            </p>
            <article class="flex space-x-6">
                <!-- parte izquierda -->
                <div class="w-1/5 mt-6">
                    <h1 class="flex items-center justify-between text-sm">
                        <span class="text-gray37">Variables</span>
                        <button @click="$inertia.visit(route('machine-variables.index'))" type="button"
                            class="text-primary bg-grayF2 rounded-full px-2 py-1">
                            Configurar variables
                        </button>
                    </h1>
                    <div class="flex flex-col">
                        <el-checkbox v-for="variable in variables" :key="variable.id" v-model="selectedVariables"
                            :label="variable.variable_name" size="small" />
                    </div>
                </div>

                <!-- parte derecha -->
                <div class="w-4/5">
                    <header class="flex items-center space-x-2 mt-7 px-2">
                        <div class="w-1/4">
                            <InputLabel value="Fecha" />
                            <el-date-picker v-model="date" type="date" placeholder="Selecciona un día"
                                format="DD MMM, YY" value-format="YYYY-MM-DD" class="!w-full" />
                        </div>
                        <div class="w-1/4">
                            <InputLabel value="Rango de horas" />
                            <div class="flex items-center space-x-2">
                                <el-time-select v-model="startTime" :max-time="endTime" placeholder="Inicio"
                                    start="06:00" step="00:15" end="23:30" />
                                <el-time-select v-model="endTime" :min-time="startTime" placeholder="Fin" start="06:00"
                                    step="00:15" end="23:30" />
                            </div>
                        </div>
                        <div class="w-1/4">
                            <InputLabel>
                                <div class="flex items-center space-x-3">
                                    <span>Resolución</span>
                                    <el-tooltip content="La resolución define la cantidad de registros en las gráficas"
                                        placement="top">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                        </svg>
                                    </el-tooltip>
                                </div>
                            </InputLabel>
                            <el-select v-model="resolution" placeholder="Selecciona">
                                <el-option v-for="item in resolutions" :key="item.value" :label="item.label"
                                    :value="item.value" />
                            </el-select>
                        </div>
                    </header>
                    <!-- graficas -->
                    <div>
                        <div v-if="panelLoading" class="text-xs my-4 text-center mt-16">
                            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                        </div>
                        <div v-else class="mt-6 grid grid-cols-2 gap-3">
                            <div v-for="(variable, index) in selectedVariables" :key="index">
                                <VariablePanel :ref="'varPan' + index" :variableName="variable" :date="date"
                                    :timeLabels="timeSlots" />
                            </div>
                            <div v-if="!selectedVariables.length" class="col-span-full mt-20">
                                <p class="flex flex-col space-y-2 items-center justify-center text-gray-400">
                                    <i class="fa-regular fa-hand-point-left text-4xl"></i>
                                    <span>Para ver información, selecciona las variables que quieras de lado
                                        izquierdo.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </main>
    {{ mapItemsToTimeSlots('status') }}
</template>
<script>
import InputLabel from '@/Components/InputLabel.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BasicArea from '@/MyComponents/Chart/Area/BasicArea.vue';
import VariablePanel from '@/MyComponents/Home/VariablePanel.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { format, addMinutes, isBefore, isEqual, parse, parseISO, differenceInMinutes, isSameDay } from "date-fns";

export default {
    data() {
        return {
            // cargas
            loading: true,
            panelLoading: false,
            // general
            items: [],
            variables: [],
            selectedVariables: [],
            resolutions: [
                {
                    label: "Cada 5 minutos",
                    value: 5
                },
                {
                    label: "Cada 10 minutos",
                    value: 10
                },
                {
                    label: "Cada 15 minutos",
                    value: 15
                },
                {
                    label: "Cada 30 minutos",
                    value: 30
                },
                {
                    label: "Cada hora",
                    value: 60
                },
            ],
            // filtros
            date: format(new Date(), "yyyy-MM-dd"), // Establece la fecha de hoy por defecto
            startTime: "6:00",
            endTime: "8:00",
            resolution: 30,
            timeSlots: [], // Aquí se almacenarán las horas generadas
        }
    },
    components: {
        PrimaryButton,
        Link,
        Loading,
        InputLabel,
        BasicArea,
        VariablePanel,
    },
    watch: {
        date(newVal, oldVal) {
            this.generateTimeSlots();
            this.fetchMachineData();
        },
        startTime(newVal, oldVal) {
            this.generateTimeSlots();
            this.fetchMachineData();
        },
        endTime(newVal, oldVal) {
            this.generateTimeSlots();
            this.fetchMachineData();
        },
        resolution(newVal, oldVal) {
            this.generateTimeSlots();
            this.fetchMachineData();
        }
    },
    computed: {

    },
    methods: {
        mapItemsToTimeSlots() {
            const usedItems = new Set(); // Para almacenar los IDs de los items ya utilizados

            const mappedData = this.timeSlots.map(slot => {
                const slotTime = parse(slot, "HH:mm", new Date());
                let closestItem = null;
                let minDifference = Infinity;

                this.items.forEach(item => {
                    const itemDate = parseISO(item.created_at);

                    const difference = differenceInMinutes(slotTime, itemDate);

                    // Considerar solo los items dentro del rango de 5 minutos hacia arriba y hacia abajo
                    if (Math.abs(difference) <= 5 && !usedItems.has(item.id)) {
                        // Verificar si es el más cercano hasta ahora
                        if (Math.abs(difference) < Math.abs(minDifference)) {
                            minDifference = difference;
                            closestItem = item;
                        }
                    }
                });

                // Si hay un item cercano dentro de los 5 minutos, se usa, de lo contrario, se usa 0
                if (closestItem) {
                    usedItems.add(closestItem.id); // Marcar el item como usado
                }

                return { [slot]: closestItem ? closestItem[this.variable] : 0 };
            });

            return mappedData;
        },
        generateTimeSlots() {
            // Verifica que todos los valores estén definidos
            if (this.date && this.startTime && this.endTime && this.resolution) {
                const slots = [];
                let currentTime = parse(this.startTime, "H:mm", new Date());
                const endTime = parse(this.endTime, "H:mm", new Date());
                const step = parseInt(this.resolution); // Resolución en minutos

                // Genera los intervalos de tiempo
                while (isBefore(currentTime, endTime) || isEqual(currentTime, endTime)) {
                    slots.push(format(currentTime, "H:mm"));
                    currentTime = addMinutes(currentTime, step);
                }

                // Actualiza el array de timeSlots
                this.timeSlots = slots;
            }
        },
        async fetchMachineVariables() {
            try {
                this.loading = true;

                const response = await axios.get(route('machine-variables.get-variables', 'Robag'));

                if (response.status === 200) {
                    this.variables = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchMachineData() {
            try {
                this.panelLoading = true;

                const response = await axios.post(route('robag.get-data-by-date-range', {
                    date: [`${this.date} ${this.timeSlots[0]}`, `${this.date} ${this.timeSlots[this.timeSlots.length - 1]}`],
                }));

                if (response.status === 200) {
                    this.items = response.data.data;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.panelLoading = false;
            }
        }
    },
    async mounted() {
        await this.fetchMachineVariables();
        this.generateTimeSlots();
        this.fetchMachineData();
    },
}
</script>