<template>
    <div id="chart">
        <apexchart type="bar" height="300" :width="width" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>
<script>
export default {
    data() {
        return {
            series: [{
                name: this.titles[1],
                data: this.dataY
            }],
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 300,
                    width: 1000
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        // columnWidth: '55%',
                        borderRadius: 5,
                        borderRadiusApplication: 'end'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    title: {
                        text: this.titles[0]
                    },
                    categories: this.dataX,
                },
                yaxis: {
                    title: {
                        text: this.titles[1]
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: this.color,
            },
        }
    },
    props: {
        color: String,
        titles: Array,
        dataY: Array,
        dataX: Array,
        width: String,
    }
}
</script>