<template>
    <div id="chart">
        <apexchart type="bar" height="280" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>
<script>
export default {
    data() {
        return {
            // series: [{
            //     name: 'Bolsas',
            //     data: [0, 0, 1000, 6000, 3000, 2000, 3, 500]
            // }],
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
                categories: [-3, -2, -1, 0, 1, 2, 3, 4],
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
                            color: '#17A281',
                            opacity: 0.5
                        },
                        {
                            offset: 100,
                            color: '#19B38E',
                            opacity: 0.9
                        }
                    ],
                },
                }
            },
        }
    },
    props: {
        series: Array,
        // chartOptions: Object,
    }
}
</script>