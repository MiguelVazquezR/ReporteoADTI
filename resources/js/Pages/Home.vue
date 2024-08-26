<template>
    <PublicLayout title="Inicio">
        <main class="lg:py-10 lg:px-14">

            <!-- Cuerpo del documento -->
            <section class="flex space-x-1 w-full">

                <!-- Imagen de la maquina (parte izquierda) -->
                <figure class="w-1/4">
                    <h1 class="font-bold text-xl mb-6 ml-4">Empacadora TNA</h1>
                    <img class="rounded-xl border border-grayD9 p-4" src="@/../../public/images/machine_1.png" alt="">
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
                            <PrimaryButton class="!py-[6px]">Generar reporte</PrimaryButton>
                        </div>
                    </div>

                    <!-- graficas en rectangulo negro -->
                    <div class="bg-black rounded-xl grid grid-cols-5 p-4 mt-5">
                        <!-- chart 1 -->
                        <div class="border-r border-white">
                            <p class="text-white">OEE</p>
                            <Semicircle :series="[80]" />
                        </div>

                        <!-- chart 2 -->
                        <div class="flex space-x-3 items-center border-r border-white px-2">
                            <div class="text-center">
                                <p class="text-gray9A">Tiempo disponible</p>
                                <p class="text-white">390 min</p>
                            </div>
                            <Basic :series="[75]" />
                        </div>

                        <!-- chart 3 -->
                        <div class="flex space-x-3 items-center border-r border-white px-2">
                            <div class="text-center">
                                <p class="text-gray9A">Tiempo de producción</p>
                                <p class="text-white">170 min</p>
                            </div>
                            <Basic :series="[25]" />
                        </div>

                        <!-- chart 4 -->
                        <div class="flex space-x-3 items-center border-r border-white px-2">
                            <div class="text-center">
                                <p class="text-gray9A">Calidad</p>
                            </div>
                            <Basic :series="[50]" />
                        </div>

                        <!-- chart 5 -->
                        <div class="flex flex-col justify-center space-y-3 px-2">
                            <div class="text-left">
                                <p class="text-gray9A">Bolsas totales: <strong class="text-white ml-2">{{ '7,000'
                                        }}</strong></p>
                                <p class="text-gray9A">Bolsas buenas: <strong class="text-white ml-2">{{ '6,000'
                                        }}</strong></p>
                                <p class="text-gray9A">Bolsas malas: <strong class="text-white ml-2">{{ '1,000'
                                        }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
                    <div class="mt-5 space-y-4">

                        <!-- primer fila -->
                        <div class="flex space-x-4">
                            <!-- Tiempos -->
                            <div class="rounded-xl border border-grayD9 p-4 w-1/4">
                                <p class="text-[#6D6E72] text-sm">TIEMPOS</p>
                                <CircleCustomAngle :series="[76, 67, 61]" />
                            </div>

                            <!-- PRODUCCIÓN DIARIA -->
                            <div class="rounded-xl border border-grayD9 p-4 w-3/4">
                                <p class="text-[#6D6E72] text-sm">PRODUCCIÓN DIARIA</p>
                                <ColumnWithMarkers />
                            </div>
                        </div>

                        <!-- Segunda fila -->
                        <div class="flex space-x-4">
                            <!-- Velocidad -->
                            <div class="rounded-xl border border-grayD9 p-4 w-3/5">
                                <p class="text-[#6D6E72] text-sm">VELOCIDAD</p>
                                <BasicArea />
                            </div>

                            <!-- HISTOGRAMA -->
                            <div class="rounded-xl border border-grayD9 p-4 w-2/5">
                                <p class="text-[#6D6E72] text-sm">HISTOGRAMA</p>
                                <!-- <ColumnWithMarkers /> -->
                            </div>
                        </div>

                        <!-- Tercera fila -->
                        <div class="flex space-x-4">
                            <!-- PELICULA -->
                            <div class="rounded-[20px] border border-grayD9 p-4 w-3/5">
                                <p class="text-[#6D6E72] font-bold text-sm">USO DE PELÍCULA</p>
                                <SimpleDonut width="450" :series="filmChart.series" :chartOptions="filmChart.chartOptions" />
                            </div>

                            <!-- BASCULA -->
                            <div class="rounded-[20px] border border-grayD9 p-4 w-2/5">
                                <p class="text-[#6D6E72] font-bold text-sm">ESTADÍSTICAS DE LA BÁSCULA </p>
                                <div v-for="(item, index) in scaleStatistics" :key="index"
                                    class="flex items-center justify-between mt-3">
                                    <p class="flex items-center space-x-2">
                                        <i class="fa-regular fa-circle text-[5px]"></i>
                                        <span class="text-sm">{{ item.name }}</span>
                                    </p>
                                    <hr class="flex-1 border-dashed border-black mx-4">
                                    <el-tag :color="item.tagColor" style="color: #373737; border: transparent;"
                                        effect="light" round>
                                        {{ item.value }}
                                    </el-tag>
                                </div>
                            </div>
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
import Semicircle from '@/MyComponents/Chart/RadialBar/Semicircle.vue';
import Basic from '@/MyComponents/Chart/RadialBar/Basic.vue';
import CircleCustomAngle from '@/MyComponents/Chart/RadialBar/CircleCustomAngle.vue';
import ColumnWithMarkers from '@/MyComponents/Chart/Column/ColumnWithMarkers.vue';
import BasicArea from '@/MyComponents/Chart/Area/BasicArea.vue';
import SimpleDonut from '@/MyComponents/Chart/Pie/SimpleDonut.vue';

export default {
    data() {


        return {
            activeTab: '1',
            searchDate: null,
            startDate: null, //vista movil
            finishDate: null, //vista movil
            scaleStatistics: [
                {
                    name: 'Peso medio',
                    value: '142.52',
                    tagColor: '#E6EFFC',
                },
                {
                    name: 'Desviación estándar',
                    value: '16.05',
                    tagColor: '#F7E7FD',
                },
                {
                    name: 'Peso total de descarga',
                    value: '65153.82',
                    tagColor: '#FAFDE6',
                },
                {
                    name: 'Total regalado',
                    value: '1249.30',
                    tagColor: '#FFF2DE',
                },
                {
                    name: 'Porcentaje regalado',
                    value: '1.82',
                    tagColor: '#FFDEDE',
                },
            ],
            filmChart: {
                series: [219758, 25000, 6991, 5964, 1952],
                chartOptions: {
                    labels: ["Bolsas llenas", "Bolsas vacias", "Bolsas movidas", "Bolsas desperdiciadas", "Bolsas de prueba"],
                    colors: ["#17A281", "#F48B0F", "#F5B91F", "#A24917", "#373737"],
                    chart: {
                        type: 'donut',
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }],
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        show: true
                                    }
                                }
                            }
                        }
                    },
                },
            },
        }
    },
    components: {
        Basic, //chart
        BasicArea, //chart
        Semicircle, //chart
        PublicLayout,
        PrimaryButton,
        CircleCustomAngle, //chart
        ColumnWithMarkers, //chart
        SimpleDonut, //chart
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