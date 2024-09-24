<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-80">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>
        <div v-else>
            <div class="flex items-center space-x-1">
                <p class="text-[#6D6E72] font-bold text-sm">PRODUCCIÓN POR DIA</p>
                <el-tooltip placement="top">
                    <template #content>
                        <p>
                            Cantidad de bolsas producidas por día, representada <br>
                            en función de los días seleccionados. El eje X <br>
                            muestra los días según las fechas elegidas, mientras <br>
                            que el eje Y indica el total de bolsas producidas en cada día.
                        </p>
                    </template>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </el-tooltip>
            </div>
            <ColumnWithMarkers :series="updatedSeries" :chartOptions="chartOptions" :width="width" />
        </div>
    </main>
</template>

<script>
import ColumnWithMarkers from '@/MyComponents/Chart/Column/ColumnWithMarkers.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            chartOptions: {
                chart: {
                    height: 280,
                    type: 'bar'
                },
                yaxis: {
                    title: {
                        text: 'Bolsas',
                    },
                },
                plotOptions: {
                    bar: {
                        columnWidth: '40%'
                    },
                },
                colors: ['#D9D9D9'],
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    showForSingleSeries: true,
                    customLegendItems: ['Real', 'Esperado'],
                    markers: {
                        fillColors: ['#D9D9D9', '#077B27']
                    }
                }
            },
        }
    },
    components: {
        ColumnWithMarkers
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        },
        width: String,
        loading: Boolean, //estado de carga al obtener las datos
    },
    computed: {
        uniqueFormattedDates() {
            const dates = this.items.map(item => format(new Date(item.created_at), 'yyyy-MM-dd'));
            const uniqueDates = [...new Set(dates)];
            uniqueDates.sort((a, b) => new Date(a) - new Date(b));
            return uniqueDates;
        },
        maxFullBagsData() {
            return this.uniqueFormattedDates.map(date => {
                const filteredItems = this.items.filter(item => format(new Date(item.created_at), 'yyyy-MM-dd') === date);
                const maxFullBags = Math.max(...filteredItems.map(item => parseFloat(item.data['Bolsas llenas']) || 0));

                return {
                    x: format(new Date(date), "dd MMM yy", { locale: es }), // Formato con el mes en español
                    y: maxFullBags,
                    goals: [
                        {
                            name: 'Esperado',
                            value: 120 * 60 * 8,  // Aquí puedes personalizar o calcular el valor esperado
                            strokeHeight: 5,
                            strokeColor: '#077B27'
                        }
                    ]
                };
            });
        },
        updatedSeries() {
            return [{
                name: 'Real',
                data: this.maxFullBagsData
            }];
        },
    }
}
</script>
