<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/4">
        <p class="text-[#6D6E72] font-bold text-sm">TIEMPOS</p>
        <CircleCustomAngle :series="circleCustomAngle" />
    </main>
</template>

<script>
import CircleCustomAngle from '@/MyComponents/Chart/RadialBar/CircleCustomAngle.vue';
export default {
data() {
    return {
        circleCustomAngle: [76, 67, 61],
    }
},
components:{
    CircleCustomAngle
},
props:{

},
methods:{

}
}
</script>
