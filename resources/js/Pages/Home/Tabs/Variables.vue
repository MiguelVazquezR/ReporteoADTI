<template>
    <main class="min-h-screen">
        <Loading v-if="loading" class="mt-16" />
        <section v-else class="w-full">
            <p class="text-secondary text-sm">
                En esta sección, encontrarás todas las variables disponibles junto con sus respectivas gráficas. Puedes
                seleccionar las variables que necesites, ajustar la fecha o el rango de fechas, y elegir la resolución que
                mejor se adapte a tu análisis.
            </p>
            <article class="flex space-x-4">
                <!-- parte izquierda -->
                <div class="w-1/5 mt-6">
                    <h1 class="flex items-center justify-between text-sm">
                        <span class="text-gray37">Variables</span>
                        <button @click="$inertia.visit(route('machine-variables.index'))" type="button"
                            class="text-primary bg-grayF2 rounded-full px-2 py-1">
                            Configurar variables
                        </button>
                    </h1>
                    <div class="flex flex-col">
                        <el-checkbox v-for="variable in variables" :key="variable.id" v-model="selectedVariables"
                            :label="variable.variable_name" size="small" />
                    </div>
                </div>
    
                <!-- parte derecha -->
                <div class="w-4/5">

                </div>
            </article>
        </section>
    </main>
</template>
<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    data() {
        return {
            // cargas
            loading: true,
            // general
            variables: [],
            selectedVariables: [],
        }
    },
    components: {
        PrimaryButton,       
        Link,
        Loading,
    },
    computed: {
       
    },
    methods: {
      async fetchMachineVariables() {
        try {
            this.loading = true;

            const response = await axios.get(route('machine-variables.get-variables', 'Robag'));

            if (response.status === 200) {
                this.variables = response.data.items;
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
      }
    },
    async mounted() {
        await this.fetchMachineVariables();
    }
}
</script>