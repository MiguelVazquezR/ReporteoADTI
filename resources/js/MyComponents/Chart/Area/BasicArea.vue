<template>
    <div id="chart">
        <apexchart type="area" height="280" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>
<script>
export default {
    data() {
        return {
            series: [{
                name: "STOCK ABC",
                data: [8100, 8300, 8900, 10500, 12000]
            }],
            chartOptions: {
                chart: {
                    type: 'area',
                    height: 280,
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },

                title: {
                    text: 'Fundamental Analysis of Stocks',
                    align: 'left'
                },
                subtitle: {
                    text: 'Price Movements',
                    align: 'left'
                },
                labels: ['uno', 'dos', 'tres', 'cuatro', 'cinco'],
                xaxis: {
                    type: 'datetime',
                },
                yaxis: {
                    opposite: true
                },
                legend: {
                    horizontalAlign: 'left'
                }
            },
        }
    },
    props: {
        // series: Array,
        // chartOptions: Object,
    }
}
</script>