<template>
    <main class="bg-[#F2F2F2] rounded-[20px] grid grid-cols-4 p-4 mt-5">
        <section class="border-r border-grayD9">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <div>
                    <p class="text-black font-bold">OEE</p>
                    <Basic :series="oee" :bad="65" :regular="85" />
                </div>
            </div>
        </section>
        <section class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <article>
                    <p class="text-black font-bold">Disponibilidad</p>
                    <div class="flex space-x-3 items-center justify-between">
                        <Basic :series="availability" :bad="70" :regular="85" />
                    </div>
                </article>
            </div>
        </section>
        <div class="border-r border-grayD9 px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>

            <div v-else>
                <section>
                    <p class="text-black font-bold">Rendimiento</p>
                    <div class="flex space-x-3 items-center justify-between">
                        <Basic :series="performance" :bad="70" :regular="90" />
                    </div>
                </section>
            </div>
        </div>
        <div class="px-2">
            <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div v-else>
                <section>
                    <p class="text-black font-bold">Calidad</p>
                    <div class="text-center flex space-x-3 items-center justify-between">
                        <Basic :series="quality" :bad="95" :regular="98" />
                    </div>
                </section>
            </div>
        </div>
    </main>
</template>

<script>
import Basic from '@/MyComponents/Chart/RadialBar/Basic.vue';
import axios from 'axios';

export default {
    data() {
        return {
            availability: [0],
            quality: [0],
            performance: [0],
            oee: [0],
            // cargas
            loading: false,
        }
    },
    components: {
        Basic,
    },
    props: {
    },
    methods: {
        async fetchOEE() {
            this.loading = true;
            try {
                const response = await axios.get(route('oee.get-metrics'));

                if (response.status === 200) {
                    this.availability = Object.values(response.data.items[0][0]);
                    this.quality = Object.values(response.data.items[1][0]);
                    this.performance = Object.values(response.data.items[2][0]);
                    this.oee = Object.values(response.data.items[3][0]);
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },

    },
    mounted() {
        this.fetchOEE();
    },
};
</script>
