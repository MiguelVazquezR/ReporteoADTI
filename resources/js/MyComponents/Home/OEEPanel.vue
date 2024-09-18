<template>
    <main class="bg-[#F2F2F2] rounded-[20px] grid grid-cols-4 p-4 mt-5 h-44">

        <!-- chart 1 -->
        <section class="border-r border-grayD9">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <div>
                    <p class="text-black font-bold">OEE</p>
                    <Semicircle :series="oee" />
                </div>
            </div>
            <!-- <p v-else-if="!loading" class="text-sm text-gray-600">No hay datos</p> -->
        </section>

        <!-- chart 2 -->
        <section class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <article>
                    <p class="text-black font-bold">Disponibilidad</p>
                    <div class="flex space-x-3 items-center justify-between">
                        <div class="text-center text-xs w-1/2">
                            <p class="text-gray9A">Disponible</p>
                            <p class="text-black">{{ formatNumber(totalTime) }} min</p>
                            <p class="text-gray9A mt-2">Producción</p>
                            <p class="text-black">{{ formatNumber(productionTime) }} min</p>
                        </div>
                        <Basic :series="availabilityPercentage" class="w-full" />
                    </div>
                </article>
            </div>
            <!-- <p v-else-if="!loading" class="text-sm text-gray-600">No hay datos</p> -->
        </section>

        <!-- chart 3 -->
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <div v-else>
                <section>
                    <p class="text-black font-bold">Rendimiento</p>
                    <div class="flex space-x-3 items-center justify-between">
                        <div class="text-center text-xs w-1/2">
                            <p class="text-gray9A">Prod. Teórica</p>
                            <p class="text-black">{{ formatNumber(teoricProduction) }} bpm</p>
                            <p class="text-gray9A mt-2">Prod. Real</p>
                            <p class="text-black">{{ formatNumber(realProduction) }} bpm</p>
                        </div>
                        <Basic :series="performancePercentage" class="w-full" />
                    </div>
                </section>
            </div>
            <!-- <p v-else-if="!loading" class="text-sm text-gray-600">No hay datos</p> -->
        </div>

        <!-- chart 4 -->
        <div class="px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <div v-else>
                <section>
                    <p class="text-black font-bold">Calidad</p>
                    <div class="text-center flex space-x-3 items-center justify-between">
                        <div class="text-xs w-1/2">
                            <p class="text-gray9A">Bolsas totales</p>
                            <p class="text-black ml-2">{{ formatNumber(totalBags) }}</p>
                            <p class="text-gray9A mt-1">Bolsas buenas</p>
                            <p class="text-black ml-2">{{ formatNumber(totalGoodBags) }}</p>
                            <p class="text-gray9A mt-1">Bolsas malas</p>
                            <p class="text-black ml-2">{{ formatNumber(totalWasteBags) }}</p>
                        </div>
                        <Basic :series="quality" class="w-full" />
                    </div>
                </section>
            </div>
            <!-- <p v-else-if="!loading" class="text-sm text-gray-600">No hay datos</p> -->
        </div>
    </main>
</template>

<script>
import Basic from '@/MyComponents/Chart/RadialBar/Basic.vue';
import Semicircle from '@/MyComponents/Chart/RadialBar/Semicircle.vue';

export default {
    data() {
        return {
            oee: [0], //OEE
            availabilityPercentage: [0], //Disponibilidad
            performancePercentage: [0], //porcentaje de Tiempo de producción
            quality: [0], //calidad
            totalTime: null, //tiempo en minutos tomado del intervalo seleccionado en filtro
            productionTime: null, //tiempo en minutos de el tiempo de produccion.
            realProduction: null, //bolsas por minuto reales en que trabaja la maquina
            totalBags: null, //total de bolsas producidas/empacadas
            totalWasteBags: null, //total de bolsas malas producidas/empacadas
            totalGoodBags: null, //total de bolsas buenas producidas/empacadas
        }
    },
    components: {
        Basic,
        Semicircle
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        }, //registros recuperados
        date: Array, //Intervalo de fechas buscadas
        loading: Boolean, //estado de carga al obtener las datos
        teoricProduction: Number, //bolsas por minuto a m{axima capacidad de la maquina (valor ajustable desde home)
    },
    methods: {
        updateOEEData() {
            this.calculateProductionTime(); //calcula las variables para el tiempo de produccion
            this.calculateOEE(); //calcula la OEE que depende de las variables antes calculadas
        },
        formatNumber(number) {
            return new Intl.NumberFormat().format(number);
        },
        calculateAvailability() {
            //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
            const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString();

            if (sameDay) {
                //si es el mismo dia toma el ultimo valor run_time de los registros obtenidos de ese dia.
                this.productionTime = parseFloat(this.items[this.items.length - 1].run_time) / 60;
            } else {
                //si son dias distintos en el intervalo de fechas se suman todos los run_time de esos dias para calcular el tiempo de produccion efectivo.
                this.productionTime = this.items.reduce((total, item) => total + parseFloat(item.run_time), 0) / 60;
            }

            this.availabilityPercentage = [((this.productionTime * 100) / this.totalTime).toFixed(1)];
        },
        calculateProductionTime() {
            //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
            const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString();

            if (sameDay) {
                //si es el mismo dia toma el ultimo valor bags_per_minute (bolsas) de los registros obtenidos de ese dia.
                this.realProduction = parseFloat(this.items[this.items.length - 1].bags_per_minute);
            } else {
                //si son dias distintos en el intervalo de fechas se suman todos los bags_per_minute de esos dias para calcular el promedio de bolsas por minuto.
                this.realProduction = this.items.reduce((total, item) => total + parseFloat(item.bags_per_minute), 0) / this.items.length;
            }

            this.performancePercentage = [((this.realProduction * 100) / this.teoricProduction).toFixed(1)];
        },
        calculateTotalBags() {
            //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
            const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString();

            if (sameDay) {
                //si es el mismo dia toma el ultimo valor total_bags (bolsas totales) y total_waste (desperdicio total) de los registros obtenidos de ese dia.
                this.totalBags = parseFloat(this.items[this.items.length - 1].total_bags);
                this.totalWasteBags = parseFloat(this.items[this.items.length - 1].total_waste);
                this.totalGoodBags = parseFloat(this.items[this.items.length - 1].total_bags) - parseFloat(this.items[this.items.length - 1].total_waste);
            } else {
                //si son dias distintos en el intervalo de fechas se suman todos los total_bags y total_waste del valor maximo de esos dias para calcular el total de bolsas buenas
                const uniqueDays = [...new Set(this.items.map(item => new Date(item.created_at).toDateString()))];

                this.totalBags = uniqueDays.reduce((total, day) => {
                    const maxBags = Math.max(...this.items
                        .filter(item => new Date(item.created_at).toDateString() === day)
                        .map(item => parseInt(item.total_bags))
                    );
                    return total + maxBags;
                }, 0);

                this.totalWasteBags = uniqueDays.reduce((total, day) => {
                    const maxBags = Math.max(...this.items
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
    watch: {
        items() {
            //revisa si el intervalo de fechas seleccionadas corresponde al mismo dia
            const sameDay = new Date(this.date[0]).toDateString() === new Date(this.date[1]).toDateString()

            if (sameDay) {
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

            if (this.items?.length) {

                this.calculateAvailability(); //calcula el porcentaje del tiempo disponible
                this.calculateProductionTime(); //calcula las variables para el tiempo de produccion
                this.calculateTotalBags(); //calcula las variables para la calidad
                this.calculateQuality(); //calcula las variables para la calidad
                this.calculateOEE(); //calcula la OEE que depende de las variables antes calculadas
            } else { //en caso de no haber informacion reinicia las graficas a 0%
                this.oee = [0]; //OEE
                this.availabilityPercentage = [0]; //Disponibilidad
                this.performancePercentage = [0]; //porcentaje de Tiempo de producción
                this.quality = [0]; //calidad
                this.productionTime = null; //tiempo en minutos de el tiempo de produccion.
                this.realProduction = null; //bolsas por minuto reales en que trabaja la maquina
                this.totalBags = null; //total de bolsas producidas/empacadas
                this.totalWasteBags = null; //total de bolsas malas producidas/empacadas
                this.totalGoodBags = null; //total de bolsas buenas producidas/empacadas
            }
        }
    },
};
</script>
