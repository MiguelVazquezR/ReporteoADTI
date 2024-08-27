<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-2/5">
        <p class="text-[#6D6E72] font-bold text-sm">HISTOGRAMA</p>
        <WithRotatedLabels :series="deviacionData" :chartOptions="chartOptions" />
    </main>
</template>

<script>
import WithRotatedLabels from '@/MyComponents/Chart/Column/WithRotatedLabels.vue';

export default {
    data() {
        return {
            deviacionData: [{
                name: 'Bolsas',
                data: [300, 0, 0, 1000, 6000, 3000, 2000, 3, 500]
            }],
            chartOptions: {
                annotations: {
                    points: [{
                        x: 'Desviación',
                        seriesIndex: 0,
                        label: {
                            borderColor: '#000',
                            offsetY: 0,
                            style: {
                                color: '#fff',
                                background: '#775DD0',
                            },
                            text: 'Bolsas buenas',
                        }
                    }]
                },
                chart: {
                    height: 280,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 0
                },
                grid: {
                    row: {
                        colors: ['#fff', '#f2f2f2']
                    }
                },
                xaxis: {
                    title: {
                        text: 'Desviación',
                    },
                    labels: {
                        rotate: -45
                    },
                    categories: [-4, -3, -2, -1, 0, 1, 2, 3, 4],
                    tickPlacement: 'on'
                },
                yaxis: {
                    title: {
                        text: 'Bolsas',
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.85,
                        opacityTo: 0.85,
                        colorStops: [
                            {
                                offset: 0,
                                color: '#19B38E',
                                opacity: 0.5
                            },
                            {
                                offset: 50,
                                color: '#17A281',
                                opacity: 0.9
                            }
                        ],
                    },
                }
            },
        }
    },
    components: {
        WithRotatedLabels
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        },
    },
    computed: {
        getChartSeries() {
            return this.items.reduce((acc, item) => {
                acc.full_bags += parseInt(item.full_bags) || 0;
                acc.total_waste += parseInt(item.total_waste) || 0;
                return acc;
            }, { full_bags: 0, total_waste: 0 });
        }
    },
}
</script>
