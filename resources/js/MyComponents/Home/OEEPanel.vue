<template>
    <main class="bg-[#F2F2F2] rounded-[20px] grid grid-cols-4 p-4 mt-5">

        <!-- chart 1 -->
        <div class="border-r border-grayD9">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <p class="text-black font-bold">OEE</p>
                <Semicircle :series="oee" />
            </div>
        </div>

        <!-- chart 2 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <section class="flex space-x-3 items-center" v-else>
                <div class="text-center text-sm">
                    <p class="text-gray9A">Disponible</p>
                    <p class="text-black">{{ formatNumber(totalTime) }} min</p>
                    <p class="text-gray9A mt-2">Producción</p>
                    <p class="text-black">{{ formatNumber(productionTime) }} min</p>
                </div>
                <Basic :series="availabilityPercentage" />
            </section>
        </div>

        <!-- chart 3 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <section class="flex space-x-3 items-center" v-else>
                <div class="text-center text-sm">
                    <p class="text-gray9A">Teórica</p>
                    <p class="text-black">{{ formatNumber(productionTime) }} min</p>
                    <p class="text-gray9A">Real</p>
                    <p class="text-black">{{ formatNumber(productionTime) }} min</p>
                </div>
                <Basic :series="productionPercentage" />
            </section>
        </div>

        <!-- chart 4 -->
        <div class="px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            
            <section v-else class="flex space-x-1 items-center">
                <div class="text-center">
                    <div class="text-left text-sm">
                        <div class="flex items-center space-x-1">
                            <p class="text-gray9A w-24">Bolsas totales:</p>
                            <p class="text-black ml-2">{{ formatNumber(totalBags) }}</p>
                        </div>
                        <div class="flex items-center space-x-1">
                            <p class="text-gray9A w-24">Bolsas buenas:</p>
                            <p class="text-black ml-2">{{ formatNumber(totalGoodBags) }}</p>
                        </div>
                        <div class="flex items-center space-x-1">
                            <p class="text-gray9A w-24">Bolsas malas:</p>
                            <p class="text-black ml-2">{{ formatNumber(totalWasteBags) }}</p>
                        </div>
                    </div>
                    <!-- <p class="text-gray9A">Calidad</p> -->
                </div>
                <Basic :series="quality" />
            </section>
        </div>
    </main>
</template>

<script>
import Basic from '@/MyComponents/Chart/RadialBar/Basic.vue';
import Semicircle from '@/MyComponents/Chart/RadialBar/Semicircle.vue';

export default {
data () {
    return {
        oee: [0], //OEE
        availabilityPercentage: [0], //Disponibilidad
        productionPercentage: [0], //porcentaje de Tiempo de producción
        quality: [0], //calidad
        totalTime: null, //tiempo en minutos tomado del intervalo seleccionado en filtro
        productionTime: null, //tiempo en minutos de el tiempo de produccion.
        totalBags: null, //total de bolsas producidas/empacadas
        totalWasteBags: null, //total de bolsas malas producidas/empacadas
        totalGoodBags: null, //total de bolsas buenas producidas/empacadas
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
    calculateAvailability() {
        const totalDowntime = this.data.reduce((total, item) => {
            return total + parseFloat(item.paused_time) + parseFloat(item.fault_time) + parseFloat(item.out_on_film_time);
        }, 0);
        
        //porcentaje de disponibilidad (tiempo total - paros no planificados)
        this.availabilityPercentage = [(((this.totalTime - (totalDowntime / 60)) / this.totalTime) * 100).toFixed(1)];
    },
    calculateProductionTime() {
        const totalDowntime = this.data.reduce((total, item) => {
            return total + parseFloat(item.paused_time) + parseFloat(item.fault_time) + parseFloat(item.out_on_film_time);
        }, 0);

        const productionTime = this.totalTime - (totalDowntime / 60); //se divide por 60 para convertir los segundos en minutos
        this.productionTime = productionTime;

        //calcular el porcentage de producción
        this.productionPercentage = [((this.productionTime / this.totalTime) * 100).toFixed(1)];
    },
    calculateTotalBags() {
        this.totalBags = this.data.reduce((total, item) => total + parseInt(item.total_bags), 0);
        this.totalWasteBags = this.data.reduce((total, item) => total + parseInt(item.total_waste), 0);
        this.totalGoodBags = this.totalBags - this.totalWasteBags;
    },
    calculateQuality() {
        // const quality = this.data.reduce((total, item) => {
        //     return total + (parseFloat(item.scale_good_bags) - parseFloat(item.empty_bags)) 
        //         / (parseFloat(item.full_bags) - parseFloat(item.empty_bags));
        // }, 0) / this.data.length;
        
        // this.quality = [(quality / 60).toFixed(1)]; //se divide por 60 para convertir los segundos en minutos

        this.quality = [((this.totalGoodBags / this.totalBags)*100).toFixed(1)] //porcentaje de piezas buenas
    },
    calculateOEE() {
        this.oee = [((this.availabilityPercentage * this.availabilityPercentage * this.quality) / 10000).toFixed(1)];
    },
    resetVariables() {
        this.oee = [0]
        this.availabilityPercentage = [0]
        this.productionPercentage = [0]
        this.quality = [0]
        this.totalTime = null
        this.productionTime = null
        this.totalBags = null
        this.totalWasteBags = null
        this.totalGoodBags = null
    }
},
watch:{
    date(newVal) {
        this.resetVariables();

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

        this.totalTime = totalMinutes; //tiempo en minutos tomado del intervalo seleccionado en filtro
        // console.log(`Total minutos: ${totalMinutes}`);

        this.calculateAvailability(); //calcula el porcentaje del tiempo disponible
        this.calculateProductionTime(); //calcula las variables para el tiempo de produccion
        this.calculateTotalBags(); //calcula las variables para la calidad
        this.calculateQuality(); //calcula las variables para la calidad
        this.calculateOEE(); //calcula la OEE que depende de las variables antes calculadas
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
    this.totalTime = differenceInMinutes;

    setTimeout(() => {
        this.calculateAvailability(); //calcula el porcentaje del tiempo disponible
        this.calculateProductionTime(); //calcula las variables para el tiempo de produccion
        this.calculateTotalBags(); //calcula las variables para la calidad
        this.calculateQuality(); //calcula las variables para la calidad
        this.calculateOEE(); //calcula la OEE que depende de las variables antes calculadas
    }, 500);
}

}
</script>
