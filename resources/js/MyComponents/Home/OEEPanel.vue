<template>
    <main class="bg-[#F2F2F2] rounded-[20px] grid grid-cols-5 p-4 mt-5">

        <!-- chart 1 -->
        <div class="border-r border-grayD9">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <p class="text-black">OEE</p>
                <Semicircle :series="semicircleSeries" />
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
                    <p class="text-black">390 min</p>
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
                    <p class="text-black">170 min</p>
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
import axios from 'axios';

export default {
data () {
    return {
        loading: true,
        semicircleSeries: [80],
        basicSeries1: [75],
        basicSeries2: [25],
        basicSeries3: [50],
    }
},
components:{
    Basic,
    Semicircle
},
props:{
    date: Array
},
watch: {
    date() {
        this.getDataByDateRange();
    }
},
methods:{
    async getDataByDateRange() {
        this.loading = true;
        try {
            const response = await axios.post(route('robag.get-data-by-date-range'), {date: this.date} );
            if ( response.status === 200 ) {
                console.log(response.data.data);
            }

        } catch (error) {
            console.log(error)
        } finally {
            this.loading = false;
        }
    }
},
mounted() {
    this.getDataByDateRange();
}
}
</script>
