<template>

    <Head title="Reporte Robag" />
    <header class="py-7 text-center font-bold">
        <p>
            Reporte de Robag: {{ formatDateTime(dates[0]) }} a {{ formatDateTime(dates[1]) }}
        </p>
        <!-- <button @click="downloadPdf" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3">Descargar PDF</button> -->
    </header>
    <Loading v-if="loading" class="mt-16" />
    <main v-else class="px-10 min-h-screen">
        <section class="h-screen space-y-4">
            <!-- graficas en rectangulo negro -->
            <OEEPanel ref="oeePanel" :date="dates" :items="data" :loading="loading" :teoricProduction="bpm"
                width="65%" />

            <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
            <!-- primer fila -->
            <div class="grid grid-cols-3 gap-x-4 mb-10">
                <!-- Tiempos -->
                <TimePanel :date="dates" :items="data" :loading="loading" width="65%" />

                <!-- PRODUCCIÓN DIARIA -->
                <ProductionPanel :items="data" :loading="loading" width="65%" class="col-span-2" />
            </div>
        </section>
        <section class="h-screen space-y-4">
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
        </section>
        <section v-for="page in Math.ceil(selectedVariables.length / 4)" :key="page" class="h-screen space-x-4">
            <h1 class="font-bold text-lg">Variables</h1>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div v-for="(variable, index) in selectedVariables.filter((e, index) => index > page * 3 && index < page * 4)"
                    :key="index">
                    <VariablePanel :variableName="variable" width="65%"
                        :data="mapItemsToTimeSlots(variables.find(v => v.variable_name == variable).variable_original_name)" />
                </div>
                <div v-if="!selectedVariables.length" class="col-span-full mt-20">
                    <p class="flex flex-col space-y-2 items-center justify-center text-gray-400">
                        <i class="fa-regular fa-hand-point-left text-4xl"></i>
                        <span>Para ver información, selecciona las variables que quieras de lado
                            izquierdo.</span>
                    </p>
                </div>
            </div>
        </section>
        <section v-if="selectedVariables.length" class="my-10 col-span-full">
            <table class="w-full text-[13px] table-fixed">
                <thead class="*:border">
                    <tr class="*:px-2 *:py-1 *:text-start">
                        <th class="w-[15%]">Tiempo</th>
                        <th v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">{{
                            variable }}</th>
                    </tr>
                </thead>
                <tbody class="*:border-x">
                    <tr v-for="(time, index) in timeSlots" :key="index"
                        class="*:px-2 *:py-1 *:text-start even:bg-gray-100 last:border-b">
                        <td class="w-[15%]">{{ time }}</td>
                        <td v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">
                            {{ mapItemsToTimeSlots(variables.find(v => v.variable_name ==
                                variable).variable_original_name)[time] }}
                        </td>
                    </tr>
                </tbody>
            </table>
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
import { format, parse, parseISO, differenceInMinutes } from "date-fns";
import Loading from '@/Components/MyComponents/Loading.vue';
import VariablePanel from '@/MyComponents/Home/VariablePanel.vue';

export default {
    data() {
        return {
            // modales
            showEmailModal: false,
            // cargas
            loading: true,
            // general
            editModbusConfig: false,
            data: [],
            // para graficas de variables
            panelLoading: true,
            items: [],
            variables: [],
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
        Head,
        Loading,
        VariablePanel,
    },
    emits: ['updated-dates'],
    props: {
        bpm: {
            default: 120,
            type: Number,
        },
        dates: Array,
        date: String,
        timeSlots: Array,
        selectedVariables: Array,
    },
    methods: {
        mapItemsToTimeSlots(variable) {
            const usedItems = new Set(); // Para almacenar los IDs de los items ya utilizados

            const mappedData = this.timeSlots.map(slot => {
                const slotTime = parse(slot, "HH:mm", new Date());
                let closestItem = null;
                let minDifference = Infinity;

                this.items.forEach(item => {
                    const itemDate = parseISO(item.created_at);
                    const difference = differenceInMinutes(slotTime, itemDate);

                    // Considerar solo los items dentro del rango de 10 minutos hacia arriba y hacia abajo
                    if (Math.abs(difference) <= 10 && !usedItems.has(item.id)) {
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

                return { [slot]: closestItem ? parseFloat(closestItem[variable]) : 0 };
            });

            // Combinar el array de objetos en un solo objeto
            const mergedData = mappedData.reduce((acc, curr) => {
                return { ...acc, ...curr };
            }, {});

            return mergedData;
        },
        downloadPdf() {
            window.location.href = '/download-pdf'; // Redirige a la ruta para descargar el PDF
        },
        formatDateTime(dateTime) {
            return format(dateTime, "dd MMM, yyyy - H:mm a");
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
                // this.loading = false;
            }
        },
        // variables independientes
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
                this.loading = true;

                const response = await axios.post(route('robag.get-data-by-date-range', {
                    date: [`${this.date} ${this.timeSlots[0]}`, `${this.date} ${this.timeSlots[this.timeSlots.length - 1]}`],
                    subHours: 0,
                }));

                if (response.status === 200) {
                    this.items = response.data.data;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        }
    },
    async mounted() {
        await this.fetchMachineVariables();
        await this.getDataByDateRange(); // Recupera los registros del día de hoy
        await this.fetchMachineData();
    }
}
</script>