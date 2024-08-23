<template>
    <PublicLayout title="Inicio">
        <el-tabs v-model="activeTab" @tab-click="handleClick">
                <el-tab-pane name="1">
                    <template #label>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
                            </svg>
                            <span>Categorías</span>
                        </div>
                    </template>
                    <Categories />
                </el-tab-pane>
            </el-tabs>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';

export default {
    data() {
        return {
           activeTab: '1',
        }
    },
    components:{
        PublicLayout
    },
    props:{

    },
    methods: {
        handleClick(tab) {
            // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
            const currentURL = new URL(window.location.href);
            currentURL.searchParams.set('currentTab', tab.props.name);
            // Actualiza la URL
            window.history.replaceState({}, document.title, currentURL.href);
        }
    },
    mounted() {
        // Obtener la URL actual
        const currentURL = new URL(window.location.href);
        // Extraer el valor de 'currentTab' de los parámetros de búsqueda
        const currentTabFromURL = currentURL.searchParams.get('currentTab');

        if (currentTabFromURL) {
            this.activeTab = currentTabFromURL;
        }
    },
}
</script>