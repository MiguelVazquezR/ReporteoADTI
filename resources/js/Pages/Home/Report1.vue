<template>

    <Head :title="'Reporte'" />

    <main class="px-10 min-h-screen my-4" id="pdf-content">
        <header class="text-center font-bold">
            <p>
                Reporte: {{ dashboard }}
                <span v-if="dataLoaded" id="data-loaded">.</span>
            </p>
        </header>
        <OEEPanelV2 />
        <section class="mt-20 *:space-y-6">
            <article v-for="item in cards" :key="item.card_id">
                <h1 class="font-bold">{{ item.card_name }}</h1>
                <div>
                    <Basic :color="item.color" :titles="[item.x_name, item.y_name]" :dataX="item.rows.map(row => row[0])"
                        :dataY="item.rows.map(row => row[1])" width="1000" />
                </div>
            </article>
        </section>
    </main>
</template>
<script>
import OEEPanelV2 from '@/MyComponents/Home/OEEPanelV2.vue';
import Basic from '@/MyComponents/Chart/Column/Basic.vue';
import { Head, Link } from '@inertiajs/vue3';
import VariablePanel from '@/MyComponents/Home/VariablePanel.vue';

export default {
    data() {
        return {
            cards: [],
            dashboard: '',
            dataLoaded: false,
        }
    },
    components: {
        OEEPanelV2,
        Basic,
        Head,
        VariablePanel,
        Link,
    },
    props: {
        dates: Array,
        date: String,
        timeSlots: Array,
        selectedVariables: Array,
        machine: Object,
    },
    methods: {
        async fetchMetabaseDashboard() {
            try {
                // obtener parametro de url llamado dashboard
                const currentURL = new URL(window.location.href);
                // Extraer el valor de 'currentTab' de los parámetros de búsqueda
                this.dashboard = currentURL.searchParams.get('dashboard');
                // this.dashboard = 'Robag1';

                const response = await axios.get(route('metabase.get-dashboard', this.dashboard));

                if (response.status === 200) {
                    this.cards = response.data.cardsData;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.dataLoaded = true;
            }
        },
    },
    mounted() {
        this.fetchMetabaseDashboard();
    },
}
</script>