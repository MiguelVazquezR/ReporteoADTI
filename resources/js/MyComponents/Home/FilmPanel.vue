<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/2">
        <p class="text-[#6D6E72] font-bold text-sm">USO DE PELÍCULA</p>
        <SimpleDonut width="400" :series="Object.values(getFilmChartSeries)" :chartOptions="chartOptions" />
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
    },
    computed: {
        getFilmChartSeries() {
            return this.items.reduce((acc, item) => {
                acc.full_bags += parseInt(item.full_bags) || 0;
                acc.total_waste += parseInt(item.total_waste) || 0;
                return acc;
            }, { full_bags: 0, total_waste: 0 });
        }
    }
}
</script>