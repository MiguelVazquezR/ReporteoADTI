<template>
    <div id="chart">
        <apexchart type="line" height="350" width="400" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>
<script>
export default {
    data() {
        return {
            // series: [
            //     {
            //         name: 'Blue',
            //         data: [
            //             {
            //                 x: 'Jan',
            //                 y: 43,
            //             },
            //             {
            //                 x: 'Feb',
            //                 y: 58,
            //             },
            //         ],
            //     },
            //     {
            //         name: 'Green',
            //         data: [
            //             {
            //                 x: 'Jan',
            //                 y: 33,
            //             },
            //             {
            //                 x: 'Feb',
            //                 y: 38,
            //             },
            //         ],
            //     },
            //     {
            //         name: 'Red',
            //         data: [
            //             {
            //                 x: 'Jan',
            //                 y: 55,
            //             },
            //             {
            //                 x: 'Feb',
            //                 y: 21,
            //             },
            //         ],
            //     },
            // ],
            // chartOptions: {
            //     chart: {
            //         height: 350,
            //         width: 400,
            //         type: 'line',
            //     },
            //     plotOptions: {
            //         line: {
            //             isSlopeChart: true,
            //         },
            //     }
            // },
        }
    },
    props: {
        chartOptions: Object,
        series: Array,
    }
}
</script>