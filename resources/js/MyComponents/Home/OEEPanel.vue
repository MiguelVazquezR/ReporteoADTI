<template>
    <main class="bg-[#F2F2F2] rounded-[20px] grid grid-cols-5 p-4 mt-5">

        <!-- chart 1 -->
        <div class="border-r border-grayD9">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <p class="text-black">OEE</p>
                <Semicircle :series="OEE" />
            </div>
        </div>

        <!-- chart 2 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <section class="flex space-x-3 items-center" v-else>
                <div class="text-center">
                    <p class="text-gray9A">Tiempo disponible</p>
                    <p class="text-black">{{ formatNumber(timeAvailable) }} min</p>
                </div>
                <Basic :series="basicSeries1" />
            </section>
        </div>

        <!-- chart 3 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <section class="flex space-x-3 items-center" v-else>
                <div class="text-center">
                    <p class="text-gray9A">Tiempo de producción</p>
                    <p class="text-black">{{ productionTime }} min</p>
                </div>
                <Basic :series="basicSeries2" />
            </section>
        </div>

        <!-- chart 4 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <section v-else class="flex space-x-3 items-center">
                <div class="text-center">
                    <p class="text-gray9A">Calidad</p>
                </div>
                <Basic :series="basicSeries3" />
            </section>
        </div>

        <!-- chart 5 -->
        <div class="flex flex-col justify-center space-y-3 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else class="text-left">
                <div class="flex items-center space-x-1">
                    <p class="text-gray9A w-28">Bolsas totales:</p>
                    <strong class="text-black ml-2">{{ '7,000'}}</strong>
                </div>
                <div class="flex items-center space-x-1">
                    <p class="text-gray9A w-28">Bolsas buenas:</p>
                    <strong class="text-black ml-2">{{ '6,000'}}</strong>
                </div>
                <div class="flex items-center space-x-1">
                    <p class="text-gray9A w-28">Bolsas malas:</p>
                    <strong class="text-black ml-2">{{ '1,000'}}</strong>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import Basic from '@/MyComponents/Chart/RadialBar/Basic.vue';
import Semicircle from '@/MyComponents/Chart/RadialBar/Semicircle.vue';

export default {
data () {
    return {
        OEE: [80], //OEE
        basicSeries1: [75], //tiempo disponible
        basicSeries2: [25], //Tiempo de producción
        basicSeries3: [50], //calidad
        timeAvailable: null, //tiempo en minutos tomado del intervalo seleccionado en filtro
        productionTime: null, //tiempo en minutos de el tiempo de produccion.
    }
},
components:{
    Basic,
    Semicircle
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
    formatNumber(number) {
      return new Intl.NumberFormat().format(number);
    },
    calculateProductionTime() {
        const totalDowntime = this.data.reduce((total, item) => {
            return total + parseFloat(item.paused_time) + parseFloat(item.fault_time) + parseFloat(item.out_on_film_time);
        }, 0);

        const productionTime = this.timeAvailable - totalDowntime;
        this.productionTime = productionTime;
    },
},
watch:{
    date(newVal) {
        const start = new Date(newVal[0]);
        const end = new Date(newVal[1]);

        // Calcular la diferencia en días
        const daysDifference = Math.floor((end - start) / (1000 * 60 * 60 * 24));

        // Minutos en días completos
        const minutesInDays = daysDifference * 14 * 60; // 14 horas * 60 minutos

        // Calcular la diferencia de tiempo en minutos en el mismo día
        const startMinutes = start.getUTCHours() * 60 + start.getUTCMinutes();
        const endMinutes = end.getUTCHours() * 60 + end.getUTCMinutes();

        const sameDayMinutesDifference = endMinutes - startMinutes;

        // Sumar ambos resultados para obtener la diferencia total en minutos
        const totalMinutes = minutesInDays + sameDayMinutesDifference;

        this.timeAvailable = totalMinutes; //tiempo en minutos tomado del intervalo seleccionado en filtro
        // console.log(`Total minutos: ${totalMinutes}`);

        this.calculateProductionTime();
    }
},
mounted() {
    const start = new Date(this.date[0]);
    const end = new Date(this.date[1]);

    // Calcular la diferencia en milisegundos
    const differenceInMilliseconds = end - start;

    // Convertir la diferencia de milisegundos a minutos
    const differenceInMinutes = Math.floor(differenceInMilliseconds / (1000 * 60));

    // Guardar el resultado en el componente
    this.timeAvailable = differenceInMinutes;

    // this.calculateProductionTime();
}

}
</script>
