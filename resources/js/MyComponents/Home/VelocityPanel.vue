<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-3/5">
        <p class="text-[#6D6E72] font-bold text-sm">VELOCIDAD</p>
        <BasicArea :series="velocityData" />
    </main>
</template>

<script>
import BasicArea from '@/MyComponents/Chart/Area/BasicArea.vue';

export default {
data() {
    return {
        velocityData: [{
            name: "Bolsas por minuto",
            data: [34, 44, 54, 21, 12, 43, 33, 23, 66, 66, 58] // Datos de ejemplo
        }],
    }
},
components:{
    BasicArea
},
props:{

},
methods:{

}
}
</script>
