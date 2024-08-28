<template>
    <main class="bg-[#F2F2F2] rounded-[20px] grid grid-cols-4 p-4 mt-5 h-44">

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

            <section v-else>
                <p class="text-black font-bold">Disponibilidad</p>
                <div class="flex space-x-3 items-center justify-between">
                    <div class="text-center text-sm">
                        <p class="text-gray9A">Disponible</p>
                        <p class="text-black">{{ formatNumber(totalTime) }} min</p>
                        <p class="text-gray9A mt-2">Producción</p>
                        <p class="text-black">{{ formatNumber(productionTime) }} min</p>
                    </div>
                    <Basic :series="availabilityPercentage" />
                </div>
            </section>
        </div>

        <!-- chart 3 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <section v-else>
                <p class="text-black font-bold">Rendimiento</p>
                <div class="flex space-x-3 items-center justify-between">
                    <div class="text-center text-sm">
                        <p class="text-gray9A w-24">Prod. Teórica</p>
                        <p class="text-black">{{ formatNumber(teoricProduction) }} bpm</p>
                        <p class="text-gray9A w-24 mt-2">Prod. Real</p>
                        <p class="text-black">{{ formatNumber(realProduction) }} bpm</p>
                    </div>
                    <Basic :series="performancePercentage" />
                </div>
            </section>
        </div>

        <!-- chart 4 -->
        <div class="px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            
            <section v-else>
                <p class="text-black font-bold">Calidad</p>
                <div class="text-center flex space-x-3 items-center justify-between">
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
                    <Basic :series="quality" />
                </div>
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
        performancePercentage: [0], //porcentaje de Tiempo de producción
        quality: [0], //calidad
        totalTime: null, //tiempo en minutos tomado del intervalo seleccionado en filtro
        productionTime: null, //tiempo en minutos de el tiempo de produccion.
        // teoricProduction: 120, //bolsas por minuto a maxima capacidad de la maquina
        realProduction: null, //bolsas por minuto reales en que trabaja la maquina
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
    loading: Boolean,
    teoricProduction: Number //bolsas por minuto a m{axima capacidad de la maquina (valor ajustable desde home)
},

methods:{
    formatNumber(number) {
        return new Intl.NumberFormat().format(number);
    },
    calculateAvailability() {
        //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
        const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString();
        
        if (sameDay) {
            //si es el mismo dia toma el ultimo valor run_time de los registros obtenidos de ese dia.
            this.productionTime = parseFloat(this.data[this.data.length - 1].run_time) / 60;
        } else {
            //si son dias distintos en el intervalo de fechas se suman todos los run_time de esos dias para calcular el tiempo de produccion efectivo.
            this.productionTime = this.data.reduce((total, item) => total + parseFloat(item.run_time), 0) / 60;
        }

        this.availabilityPercentage = [((this.productionTime * 100) / this.totalTime).toFixed(1)];
    },
    calculateProductionTime() {
        //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
        const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString();
        
        if (sameDay) {
            //si es el mismo dia toma el ultimo valor bags_per_minute (bolsas) de los registros obtenidos de ese dia.
            this.realProduction = parseFloat(this.data[this.data.length - 1].bags_per_minute);
        } else {
            //si son dias distintos en el intervalo de fechas se suman todos los bags_per_minute de esos dias para calcular el promedio de bolsas por minuto.
            this.realProduction = this.data.reduce((total, item) => total + parseFloat(item.bags_per_minute), 0) / this.data.length;
        }

        this.performancePercentage = [((this.realProduction * 100) / this.teoricProduction).toFixed(1)];
    },
    calculateTotalBags() {
        //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
        const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString();
        
        if (sameDay) {
            //si es el mismo dia toma el ultimo valor total_bags (bolsas totales) y total_waste (desperdicio total) de los registros obtenidos de ese dia.
            this.totalBags = parseFloat(this.data[this.data.length - 1].total_bags);
            this.totalWasteBags = parseFloat(this.data[this.data.length - 1].total_waste);
            this.totalGoodBags = parseFloat(this.data[this.data.length - 1].total_bags) - parseFloat(this.data[this.data.length - 1].total_waste);
        } else {
            //si son dias distintos en el intervalo de fechas se suman todos los total_bags y total_waste del valor maximo de esos dias para calcular el total de bolsas buenas
            const uniqueDays = [...new Set(this.data.map(item => new Date(item.created_at).toDateString()))];

            this.totalBags = uniqueDays.reduce((total, day) => {
                const maxBags = Math.max(...this.data
                    .filter(item => new Date(item.created_at).toDateString() === day)
                    .map(item => parseInt(item.total_bags))
                );
                return total + maxBags;
            }, 0);

            this.totalWasteBags = uniqueDays.reduce((total, day) => {
                const maxBags = Math.max(...this.data
                    .filter(item => new Date(item.created_at).toDateString() === day)
                    .map(item => parseInt(item.total_waste))
                );
                return total + maxBags;
            }, 0);

            this.totalGoodBags = parseInt(this.totalBags - this.totalWasteBags);
        }
    },
    calculateQuality() {
        this.quality = [((this.totalGoodBags / this.totalBags) * 100).toFixed(1)] //porcentaje de piezas buenas
    },
    calculateOEE() {
        this.oee = [((this.availabilityPercentage * this.performancePercentage * this.quality) / 10000).toFixed(1)];
    },
},
watch:{
    data() {

        //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
        const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString()

        if ( sameDay ) {
            const start = new Date(this.date[0]);
            const end = new Date(this.date[1]);

            // Calcular la diferencia en milisegundos
            const differenceInMilliseconds = end - start;

            // Convertir la diferencia de milisegundos a minutos
            const differenceInMinutes = Math.floor(differenceInMilliseconds / (1000 * 60));

            // Guardar el resultado en el componente
            this.totalTime = differenceInMinutes;
        } else {
            const start = new Date(this.date[0]);
            const end = new Date(this.date[1]);

            // Calcular la diferencia en días
            const daysDifference = Math.floor((end - start) / (1000 * 60 * 60 * 24));

            // Minutos en días completos
            const minutesInDays = daysDifference * 24 * 60; // 24 horas * 60 minutos

            // Calcular la diferencia de tiempo en minutos en el mismo día
            const startMinutes = start.getUTCHours() * 60 + start.getUTCMinutes();
            const endMinutes = end.getUTCHours() * 60 + end.getUTCMinutes();

            const sameDayMinutesDifference = endMinutes - startMinutes;

            // Sumar ambos resultados para obtener la diferencia total en minutos
            const totalMinutes = minutesInDays + sameDayMinutesDifference;

            this.totalTime = totalMinutes; //tiempo en minutos tomado del intervalo seleccionado en filtro
            // console.log(`Total minutos: ${totalMinutes}`);
            }

        this.calculateAvailability(); //calcula el porcentaje del tiempo disponible
        this.calculateProductionTime(); //calcula las variables para el tiempo de produccion
        this.calculateTotalBags(); //calcula las variables para la calidad
        this.calculateQuality(); //calcula las variables para la calidad
        this.calculateOEE(); //calcula la OEE que depende de las variables antes calculadas
    }
},
};
</script>
