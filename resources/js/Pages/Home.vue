<template>
    <PublicLayout title="Inicio">
        <main class="lg:py-10 lg:px-14">

            <!-- Cuerpo del documento -->
            <section class="flex space-x-4 w-full">

                <!-- Imagen de la maquina (parte izquierda) -->
                <figure class="w-1/4">
                    <h1 class="font-bold text-xl mb-6 ml-4">Empacadora TNA</h1>
                    <img class="rounded-[20px] border border-grayD9 p-4 w-full"
                        src="@/../../public/images/machine_1.png" alt="">
                </figure>

                <!-- Infomracion de producción (parte derecha) -->
                <article class="w-3/4">
                    <div class="flex items-center justify-between space-x-3 w-full">
                        <div class="flex items-center space-x-4 lg:-left-40 z-10">
                            <div v-if="isMobile" class="flex items-center space-x-2">
                                <el-date-picker @change="handleStartDateChange" :disabled-date="disabledPrevDays"
                                    v-model="startDate" type="date" class="!w-1/2" placeholder="Inicio" size="small" />
                                <el-date-picker @change="handleFinishDateChange" :disabled-date="disabledNextDays"
                                    v-model="finishDate" type="date" class="!w-1/2" placeholder="Final" size="small" />
                            </div>
                            <div v-else>
                                <el-date-picker
                                    @change="filterData"
                                    v-model="searchDate"
                                    type="datetimerange"
                                    range-separator="A"
                                    start-placeholder="Fecha de inicio"
                                    end-placeholder="Fecha de fin"
                                    class="!w-full"
                                />
                            </div>
                        </div>

                        <!-- Boton para generar reporte -->
                        <div>
                            <el-dropdown split-button type="primary" @click="handleClick"
                                @command="handleDropdownCommand">
                                Generar reporte
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item command="email">Enviar por correo</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>
                    </div>

                    <!-- graficas en rectangulo negro -->
                    <OEEPanel :date="searchDate" />
                    
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
        </main>
        <DialogModal :show="showEmailModal" @close="showEmailModal = false">
            <template #title>
                <h1>Enviar reporte por correo</h1>
            </template>
            <template #content>
                <form @submit.prevent="sendEmail">
                    <div>
                        <InputLabel value="Correo electrónico detinatario*" />
                        <el-input v-model="emailForm.main_email" placeholder="Ej. admin@gmail.com" clearable />
                        <InputError :message="emailForm.errors.main_email" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="CCO" />
                        <el-select v-model="emailForm.cco" multiple filterable allow-create default-first-option
                            :reserve-keyword="false" placeholder="Agrega cualquier correo">
                            <el-option v-for="item in ccoList" :key="item.value" :label="item.label"
                                :value="item.value" />
                        </el-select>
                        <InputError :message="emailForm.errors.cco" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Asunto*" />
                        <el-input v-model="emailForm.subject" placeholder="Reporte de ..." clearable />
                        <InputError :message="emailForm.errors.subject" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Descripción del correo" />
                        <el-input v-model="emailForm.description" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                            placeholder="Escribe una descripción del producto si es necesario" clearable />
                        <InputError :message="emailForm.errors.description" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Adjunto" />
                        <p class="text-secondary text-xs">Reporte Robag 03_ago_2024 a 23_ago_2024.xls</p>
                    </div>
                </form>
            </template>
            <template #footer>
                <PrimaryButton @click="sendEmail">Enviar correo</PrimaryButton>
            </template>
        </DialogModal>
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
            // formularios
            emailForm,
            // modales
            showEmailModal: false,
            // general
            ccoList: [],
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
        sendEmail() {

        },
        handleStartDateChange(value) {
            this.startDate = value;
            // Si finishDate es nulo, aplica la regla de deshabilitación
            if (!this.finishDate) {
                this.disabledPrevDays();
            }
        },
        handleFinishDateChange(value) {
            this.finishDate = value;
            // Si startDate es nulo, aplica la regla de deshabilitación
            if (!this.startDate) {
                this.disabledNextDays();
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
        handleClick() {
            console.log('generar reporte');
        },
        filterData() {
            console.log('hola');
        }
    },
    computed: {
        isMobile() {
            return window.innerWidth < 768;
        }
    },
    mounted() {
        const start = new Date();
        start.setHours(0, 0, 0, 0); // 6:00 AM

        const end = new Date();
        end.setHours(14, 0, 0, 0); // 8:00 PM

        this.searchDate = [
            start.toISOString().slice(0, 19).replace('T', ' '),  // 'YYYY-MM-DD HH:mm:ss'
            end.toISOString().slice(0, 19).replace('T', ' ')    // 'YYYY-MM-DD HH:mm:ss'
        ]
    },
}
</script>