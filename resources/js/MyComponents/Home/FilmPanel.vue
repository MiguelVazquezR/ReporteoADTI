<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/2">
        <p class="text-[#6D6E72] font-bold text-sm">USO DE PELÍCULA</p>
        <SimpleDonut width="300" :series="filmChart.series" :chartOptions="filmChart.chartOptions" />
    </main>
</template>

<script>
import SimpleDonut from '@/MyComponents/Chart/Pie/SimpleDonut.vue';

export default {
    data() {
        return {
            filmChart: {
                series: [],
                chartOptions: {
                    // labels: ["Bolsas llenas", "Bolsas vacias", "Bolsas movidas", "Bolsas desperdiciadas", "Bolsas de prueba"],
                    labels: ["Bolsas llenas", "Bolsas vacias"],
                    // colors: ["#17A281", "#F48B0F", "#F5B91F", "#A24917", "#373737"],
                    colors: ["#17A281", "#F48B0F"],
                    chart: {
                        type: 'donut',
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
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
                                    }
                                }
                            }
                        }
                    },
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
    },
    computed: {
        getFilmChartData() {
            this.filmChart.series.reduce((acc, item) => {
                acc.full_bags += parseInt(item.full_bags) || 0;
                acc.total_waste += parseInt(item.total_waste) || 0;
                return acc;
            }, { full_bags: 0, total_waste: 0 });
        }
    }
}
</script>