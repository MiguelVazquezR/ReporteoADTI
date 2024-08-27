<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-2/5">
        <p class="text-[#6D6E72] font-bold text-sm">HISTOGRAMA</p>
        <WithRotatedLabels :series="deviacionData" />
    </main>
</template>

<script>
import WithRotatedLabels from '@/MyComponents/Chart/Column/WithRotatedLabels.vue';

export default {
data() {
    return {
        deviacionData: [{
            name: 'Bolsas',
            data: [0, 0, 1000, 6000, 3000, 2000, 3, 500]
        }],
    }
},
components:{
    WithRotatedLabels
},
props:{

},
methods:{

}
}
</script>
