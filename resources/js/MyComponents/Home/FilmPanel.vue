<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/2">
        <p class="text-[#6D6E72] font-bold text-sm">USO DE PELÍCULA</p>
        <SimpleDonut width="450" :series="filmChart.series" :chartOptions="filmChart.chartOptions" />
    </main>
</template>

<script>
import SimpleDonut from '@/MyComponents/Chart/Pie/SimpleDonut.vue';

export default {
data() {
    return {
        filmChart: {
            series: [219758, 25000, 6991, 5964, 1952],
            chartOptions: {
                labels: ["Bolsas llenas", "Bolsas vacias", "Bolsas movidas", "Bolsas desperdiciadas", "Bolsas de prueba"],
                colors: ["#17A281", "#F48B0F", "#F5B91F", "#A24917", "#373737"],
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
components:{
    SimpleDonut
},
props:{

},
methods:{

}
}
</script>