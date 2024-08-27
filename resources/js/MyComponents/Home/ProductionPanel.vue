<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-3/4">
        <p class="text-[#6D6E72] font-bold text-sm">PRODUCCIÓN POR DIA</p>
        <ColumnWithMarkers :series="updatedSeries" :chartOptions="chartOptions" />
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
                colors: ['#1A1A1A'],
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    showForSingleSeries: true,
                    customLegendItems: ['Real', 'Esperado'],
                    markers: {
                        fillColors: ['#373737', '#077B27']
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
                const maxFullBags = Math.max(...filteredItems.map(item => parseFloat(item.full_bags) || 0));

                return {
                    x: format(new Date(date), "dd MMM yy", { locale: es }), // Formato con el mes en español
                    y: maxFullBags,
                    goals: [
                        {
                            name: 'Esperado',
                            value: 8000,  // Aquí puedes personalizar o calcular el valor esperado
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
