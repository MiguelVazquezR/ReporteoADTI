<template>

    <Head :title="'Reporte'" />

    <main class="px-10 min-h-screen my-4" id="pdf-content">
        <header class="text-center font-bold">
            <p>
                Reporte de
            </p>
        </header>
        <section class="space-y-4">
            <OEEPanelV2 />
            <!-- <div class="mt-4 grid grid-cols-3 gap-4">
                <ProductionPanel class="col-span-full" :items="data" :loading="loadingCharts" width="900" height="350" />
                <TimePanel width="250" :date="dates" :items="data" :loading="loadingCharts" />
                <VelocityPanel class="col-span-2" :items="data" :loading="loadingCharts" height="250" width="550" />
                <DesviacionPanel class="col-span-full" :items="data" :loading="loadingCharts" width="900" />
                <FilmPanel class="col-span-2" :items="data" :loading="loadingCharts" />
                <ScalePanel :items="data" :loading="loadingCharts" />
            </div> -->
        </section>
        <section v-if="selectedVariables.length" class="mt-20 space-x-4">
            <h1 class="font-bold text-lg">Variables</h1>
            <div class="mt-6 space-y-7">
                <div v-for="(variable, index) in selectedVariables" :key="index">
                    <VariablePanel :variableName="variable" height="270" width="900"
                        :data="variablesMapped ? variablesMapped[variable] : {}"
                        :class="index > 8 && index < 12 ? 'mt-16' : null" />
                </div>
            </div>
        </section>
        <section v-if="selectedVariables.length" class="my-10 col-span-full">
            <table class="w-full text-[13px] table-fixed">
                <thead class="*:border">
                    <tr class="*:px-2 *:py-1 *:text-start">
                        <th class="w-[15%]">Tiempo</th>
                        <th v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">
                            {{ variable }}
                        </th>
                    </tr>
                </thead>
                <tbody class="*:border-x">
                    <tr v-for="(time, index) in timeSlots" :key="index"
                        class="*:px-2 *:py-1 *:text-start even:bg-gray-100 last:border-b">
                        <td class="w-[15%]">{{ time.split(' ')[1] }}</td>
                        <td v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">
                            {{ variablesMapped ? variablesMapped[variable][time.split(' ')[1]] : '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</template>
<script>
import OEEPanelV2 from '@/MyComponents/Home/OEEPanelV2.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { format, parse, parseISO, differenceInMinutes } from "date-fns";
import VariablePanel from '@/MyComponents/Home/VariablePanel.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TimePanel from '@/MyComponents/Home/TimePanel.vue';
// import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
// import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
// import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
// import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
// import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';

export default {
    data() {
        return {
            //forms
            // emailForm,
            // modales
            showEmailModal: false,
            // cargas
            loadingTemplate: true,
            loadingCharts: false,
            loadingPDF: false,
            sendingEmail: false,
            // general
            editModbusConfig: false,
            data: [],
            printing: false,
            // para graficas de variables
            items: [],
            variables: [],
            variablesMapped: null,
        }
    },
    components: {
        // ProductionPanel,
        // PrimaryButton,
        // DesviacionPanel,
        // VelocityPanel,
        // ScalePanel,
        // TimePanel,
        // FilmPanel,
        OEEPanelV2,
        Head,
        VariablePanel,
        Link,
    },
    props: {
        dates: Array,
        date: String,
        timeSlots: Array,
        selectedVariables: Array,
        machine: Object,
    },
    methods: {
        formatDateTime(dateTime) {
            return format(dateTime, "dd MMM, yyyy - H:mm a");
        },
        mapAllVariables() {
            let variablesMapped = {};

            this.variables.forEach(variable => {
                const variableName = variable.name;
                variablesMapped[variableName] = this.mapItemsToTimeSlots(variable.original_name);
            });

            this.variablesMapped = variablesMapped;
        },
        mapItemsToTimeSlots(variable) {
            const usedItems = new Set(); // Para almacenar las fechas de creación de los items ya utilizados

            const mappedData = this.timeSlots.map(slot => {
                // Convertir el slot en una fecha completa (fecha + hora)
                const slotTime = parse(slot, "yyyy-MM-dd H:mm", new Date());
                let closestItem = null;
                let minDifference = Infinity;

                this.items.forEach(item => {
                    // Asegúrate de que la fecha de 'item.created_at' también incluya la fecha
                    const itemDate = parseISO(item.created_at);
                    const difference = differenceInMinutes(slotTime, itemDate);
                    // console.log(Math.abs(difference) <= 10 && !usedItems.has(item.created_at));

                    // Considerar solo los items dentro del rango de 10 minutos hacia arriba y hacia abajo
                    if (Math.abs(difference) <= 10 && !usedItems.has(item.created_at)) {
                        // Verificar si es el más cercano hasta ahora
                        if (Math.abs(difference) < Math.abs(minDifference)) {
                            minDifference = difference;
                            closestItem = item;
                        }
                    }
                });

                // Si hay un item cercano dentro de los 10 minutos, se usa, de lo contrario, se usa 0
                if (closestItem) {
                    usedItems.add(closestItem.created_at); // Marcar el item como usado
                    return { [slot.split(' ')[1]]: parseFloat(parseFloat(closestItem[variable]).toFixed(2)) };
                }

                return { [slot.split(' ')[1]]: 0 };
            });

            // Combinar el array de objetos en un solo objeto
            const mergedData = mappedData.reduce((acc, curr) => {
                return { ...acc, ...curr };
            }, {});

            return mergedData;
        },
        async fetchMachineVariables() {
            try {
                this.loadingTemplate = true;

                const response = await axios.get(route('machine-variables.get-variables'));

                if (response.status === 200) {
                    this.variables = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingTemplate = false;
            }
        },
        async getDataByDateRange() {
            this.loadingCharts = true;
            try {
                const response = await axios.post(route('machine-data.get-data-by-date-range'), { date: this.dates });
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
                this.loadingCharts = false;
            }
        },
        // variables independientes
        async fetchMachineData() {
            try {
                this.loadingCharts = true;

                // Enviar el rango de fechas correctamente
                const response = await axios.post(route('machine-data.get-data-by-date-range', {
                    date: [`${this.date} ${this.timeSlots[0].split(' ')[1]}`, `${this.date} ${this.timeSlots[this.timeSlots.length - 1].split(' ')[1]}`],
                    subHours: 0,
                }));

                if (response.status === 200) {
                    this.items = response.data.data;
                    this.mapAllVariables();
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingCharts = false;
            }
        },
    },
    async mounted() {
        this.fetchMachineVariables();
        await this.getDataByDateRange(); // Recupera los registros del día de hoy
        await this.fetchMachineData();
    },
}
</script>