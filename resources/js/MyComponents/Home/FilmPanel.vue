<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-60">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <p class="text-[#6D6E72] font-bold text-sm">USO DE PELÍCULA</p>
            <SimpleDonut width="400" :series="getFilmChartSeries" :chartOptions="chartOptions" />
        </div>
    </main>
</template>

<script>
import SimpleDonut from '@/MyComponents/Chart/Pie/SimpleDonut.vue';

export default {
    data() {
        return {
            chartOptions: {
                labels: ["Bolsas llenas", "Bolsas desperdiciadas"],
                colors: ["#17A281", "#F48B0F"],
                chart: {
                    type: 'donut',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 400
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    showAlways: true,
                                    show: true
                                },
                            }
                        }
                    }
                },
            },
        }
    },
    components: {
        SimpleDonut
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        },
        loading: Boolean, //estado de carga al obtener las datos
    },
    computed: {
        getFilmChartSeries() {
            // return this.items.reduce((acc, item) => {
            //     acc.full_bags += parseInt(item.full_bags) || 0;
            //     acc.total_waste += parseInt(item.total_waste) || 0;
            //     return acc;
            // }, { full_bags: 0, total_waste: 0 });

            // delvolver el ultimo registro
            let lastItem = this.items[this.items.length - 1];
            const fullBags = parseFloat(lastItem?.full_bags ?? 0.0).toFixed(1);
            const totalWaste = parseFloat(lastItem?.total_waste ?? 0.0).toFixed(1);

            return [parseFloat(fullBags), parseFloat(totalWaste)];
        }
    }
}
</script>