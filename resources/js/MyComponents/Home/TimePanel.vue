<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/4">
        <div v-if="loading" class="text-sm my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <p class="text-[#6D6E72] font-bold text-sm">TIEMPOS</p>
            <CircleCustomAngle :series="circleCustomAngle" />
        </div>
    </main>
</template>

<script>
import CircleCustomAngle from '@/MyComponents/Chart/RadialBar/CircleCustomAngle.vue';

export default {
data() {
    return {
        circleCustomAngle: [76, 67, 61],
        totalTime: null, //tiempo en minutos tomado del intervalo seleccionado en filtro
    }
},
components:{
    CircleCustomAngle
},
props:{
    data: Array, //registros recuperados
    date: Array, //Intervalo de fechas buscadas
    loading: {
        type: Boolean,
        default: false
    }
},
methods:{

},
mounted() {
    const start = new Date(this.date[0]);
    const end = new Date(this.date[1]);

    // Calcular la diferencia en milisegundos
    const differenceInMilliseconds = end - start;

    // Convertir la diferencia de milisegundos a minutos
    const differenceInMinutes = Math.floor(differenceInMilliseconds / (1000 * 60));

    // Guardar el resultado en el componente
    this.totalTime = differenceInMinutes;
}
}
</script>
