<template>
    <PublicLayout title="Inicio">
        <main class="lg:py-10 lg:px-14">

            <!-- Cuerpo del documento -->
            <section class="flex space-x-4 w-full">

                <!-- Imagen de la maquina (parte izquierda) -->
                <figure class="w-1/4">
                    <h1 class="font-bold text-xl mb-6 ml-4">Empacadora TNA</h1>
                    <img class="rounded-[20px] border border-grayD9 p-4 w-full" src="@/../../public/images/machine_1.png" alt="">
                </figure>

                <!-- Infomracion de producción (parte derecha) -->
                <article class="w-3/4">
                    <div class="flex items-center justify-between space-x-3">
                        <div class="lg:-left-40 z-10 w-80">
                            <div v-if="isMobile" class="flex items-center space-x-2">
                                <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                                    v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio" size="small" />
                                <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                                    v-model="finishDate" type="date" class="!w-1/2" placeholder="Final" size="small" />
                            </div>
                            <div v-else>
                                <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                                    start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin"
                                    class="!w-full" />
                            </div>
                        </div>

                        <!-- Boton para generar reporte -->
                        <div>
                            <el-dropdown split-button type="primary" @click="handleClick">
                                Generar reporte
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item>Enviar por email</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>
                    </div>

                    <!-- graficas en rectangulo negro -->
                    <OEEPanel />
                    
                    <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
                    <div class="mt-4 space-y-4">

                        <!-- primer fila -->
                        <div class="flex space-x-4">
                            <!-- Tiempos -->
                            <TimePanel />

                            <!-- PRODUCCIÓN DIARIA -->
                            <ProductionPanel />
                        </div>

                        <!-- Segunda fila -->
                        <div class="flex space-x-4">
                            <!-- Velocidad -->
                            <VelocityPanel />

                            <!-- HISTOGRAMA -->
                            <DesviacionPanel />
                        </div>

                        <!-- Tercera fila -->
                        <div class="flex space-x-4">
                            <!-- PELICULA -->
                            <FilmPanel />

                            <!-- BASCULA -->
                            <ScalePanel />
                        </div>
                    </div>
                </article>

            </section>
            <!-- <el-tabs v-model="activeTab" @tab-click="handleClick">
                <el-tab-pane name="1">
                    <template #label>
                        <div class="flex items-center">
                            <svg class="size-4 mr-1" stroke="currentColor" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.6875 1V11.0625M6.6875 11.0625C5.82883 11.0625 5.00633 11.2171 4.24625 11.5M6.6875 11.0625C7.54617 11.0625 8.36867 11.2171 9.12875 11.5M10.625 2.14917C9.3205 1.96606 8.00479 1.87445 6.6875 1.875C5.35108 1.875 4.03625 1.96833 2.75 2.14917M10.625 2.14917C11.2142 2.23258 11.7975 2.33408 12.375 2.4525M10.625 2.14917L12.1533 8.406C12.2245 8.69708 12.0915 9.00567 11.8097 9.10717C11.4297 9.24358 11.0288 9.31305 10.625 9.3125C10.2212 9.31305 9.82033 9.24358 9.44025 9.10717C9.1585 9.00567 9.0255 8.69708 9.09608 8.406L10.625 2.14975V2.14917ZM2.75 2.14917C2.16083 2.23258 1.5775 2.33408 1 2.4525M2.75 2.14917L4.27833 8.406C4.3495 8.69708 4.2165 9.00567 3.93475 9.10717C3.55467 9.24357 3.15382 9.31304 2.75 9.3125C2.34618 9.31304 1.94533 9.24357 1.56525 9.10717C1.2835 9.00567 1.1505 8.69708 1.22108 8.406L2.75 2.14975V2.14917Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Datos de Peso</span>
                        </div>
                    </template>
Aqui va el componente 1.
</el-tab-pane>
<el-tab-pane name="2">
    <template #label>
                        <div class="flex items-center">
                            <i class="fa-regular fa-clock mr-1"></i>
                            <span>Tiempos</span>
                        </div>
                    </template>
    Aqui va el componente 2.
</el-tab-pane>
<el-tab-pane name="3">
    <template #label>
                        <div class="flex items-center">
                            <i class="fa-solid fa-arrow-up-short-wide mr-1"></i>
                            <span>Uso de película</span>
                        </div>
                    </template>
    Aqui va el componente 3.
</el-tab-pane>
<el-tab-pane name="4">
    <template #label>
                        <div class="flex items-center">
                            <svg class="size-4 mr-1" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.875 1V7.5625C1.875 7.9106 2.01328 8.24444 2.25942 8.49058C2.50556 8.73672 2.8394 8.875 3.1875 8.875H4.5M1.875 1H1M1.875 1H11.5M4.5 8.875H8.875M4.5 8.875L3.91667 10.625M11.5 1H12.375M11.5 1V7.5625C11.5 7.9106 11.3617 8.24444 11.1156 8.49058C10.8694 8.73672 10.5356 8.875 10.1875 8.875H8.875M8.875 8.875L9.45833 10.625M3.91667 10.625H9.45833M3.91667 10.625L3.625 11.5M9.45833 10.625L9.75 11.5M4.0625 6.25L5.8125 4.5L7.0655 5.753C7.6542 4.90792 8.42126 4.2024 9.3125 3.68625" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Estadística de escala</span>
                        </div>
                    </template>
    Aqui va el componente 4.
</el-tab-pane>
<el-tab-pane name="5">
    <template #label>
                        <div class="flex items-center">
                            <svg class="size-4 mr-1" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.5 6.85433L5.8125 8.16683L8 5.10433M6.25 1C4.92918 2.2543 3.17015 2.94225 1.34884 2.91683C1.11716 3.62265 0.999403 4.36088 1 5.10375C1 8.36575 3.23067 11.1062 6.25 11.8838C9.26934 11.1068 11.5 8.36633 11.5 5.10433C11.5 4.34017 11.3775 3.60458 11.1512 2.91625H11.0625C9.19817 2.91625 7.50417 2.18825 6.25 1Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>OEE</span>
                        </div>
                    </template>
    Aqui va el componente 5.
</el-tab-pane>
</el-tabs> -->
        </main>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import OEEPanel from '@/MyComponents/Home/OEEPanel.vue';
import TimePanel from '@/MyComponents/Home/TimePanel.vue';
import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';

export default {
    data() {
        return {
            activeTab: '1',
            searchDate: null,
            startDate: null, //vista movil
            finishDate: null, //vista movil
            }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        ProductionPanel,
        DesviacionPanel,
        VelocityPanel,
        ScalePanel,
        TimePanel,
        FilmPanel,
        OEEPanel,
    },
    props: {

    },
    methods: {
        handleStartDateChange(value) {
            this.startDate = value;
            // Si finishDate es nulo, aplica la regla de deshabilitación
            if (!this.finishDate) {
                this.disabledPrevDays();
            }
        },
        disabledPrevDays(date) {
            if (this.finishDate) {
                return date.getTime() > new Date(this.finishDate).getTime();
            }
            return false;
        },
        disabledNextDays(date) {
            if (this.startDate) {
                return date.getTime() < new Date(this.startDate).getTime();
            }
            return false;
        },
        handleClick () {
            console.log('generar reporte');
        }
        // handleClick(tab) {
        //     // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
        //     const currentURL = new URL(window.location.href);
        //     currentURL.searchParams.set('currentTab', tab.props.name);
        //     // Actualiza la URL
        //     window.history.replaceState({}, document.title, currentURL.href);
        // }
    },
    computed: {
        isMobile() {
            return window.innerWidth < 768;
        }
    },
    // mounted() {
    //     // Obtener la URL actual
    //     const currentURL = new URL(window.location.href);
    //     // Extraer el valor de 'currentTab' de los parámetros de búsqueda
    //     const currentTabFromURL = currentURL.searchParams.get('currentTab');

    //     if (currentTabFromURL) {
    //         this.activeTab = currentTabFromURL;
    //     }
    // },
}
</script>