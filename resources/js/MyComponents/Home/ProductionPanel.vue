<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-3/4">
        <p class="text-[#6D6E72] font-bold text-sm">PRODUCCIÓN DIARIA</p>
        <ColumnWithMarkers :series="dayliProduccion" />
    </main>
</template>

<script>
import ColumnWithMarkers from '@/MyComponents/Chart/Column/ColumnWithMarkers.vue';

export default {
data() {
    return {
        dayliProduccion: [
                {
                    name: 'Actual',
                    data: [
                        {
                            x: '02 Ago',
                            y: 1292,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 1400,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '03 Ago',
                            y: 4432,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 5400,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '04 Ago',
                            y: 5423,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 5200,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '05 Ago',
                            y: 6653,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 6500,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '06 Ago',
                            y: 8133,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 6600,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '07 Ago',
                            y: 7132,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 7500,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '08 Ago',
                            y: 7332,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 8700,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        },
                        {
                            x: '09 Ago',
                            y: 6553,
                            goals: [
                                {
                                    name: 'Expected',
                                    value: 7300,
                                    strokeHeight: 5,
                                    strokeColor: '#077B27'
                                }
                            ]
                        }
                    ]
                }
            ],
    }
},
components:{
    ColumnWithMarkers
},
props:{

},
methods:{

}
}
</script>
