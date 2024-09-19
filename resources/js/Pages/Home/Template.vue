<template>

    <Head title="Reporte Robag" />
        <div class="flex space-x-3 justify-center">
            <!-- <button v-if="!loading && !printing" @click="print" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3">Descargar PDF</button> -->
            <button :disabled="loadingPDF" v-if="!loading && !printing" @click="generatePdf" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3 disabled:bg-gray-500 disabled:cursor-not-allowed">Descargar PDF</button>
            <button :disabled="loadingPDF" v-if="!loading && !printing" @click="showEmailModal = true" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3 disabled:bg-gray-500 disabled:cursor-not-allowed">Enviar por correo</button>
            <!-- <button v-if="!loading && !printing" @click="downloadPdf" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3 ml-5">Test ruta local</button> -->
        </div>
        <div class="text-center mt-4">
            <i v-if="loadingPDF" class="fa-solid fa-circle-notch fa-spin text-xl mr-2"></i>
        </div>
    <Loading v-if="loading" class="mt-16" />
    <main v-else class="px-10 min-h-screen my-0" id="pdf-content">
        <header class="text-center font-bold">
            <p>
                Reporte de Robag: {{ formatDateTime(dates[0]) ?? '' }} a {{ formatDateTime(dates[1]) ?? '' }}
            </p>
        </header>
        <section class="space-y-4">
            <!-- graficas en rectangulo negro -->
            <OEEPanel ref="oeePanel" :date="dates" :items="data" :loading="loading" :teoricProduction="bpm"
                width="65%" />

            <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
            <!-- primer fila -->
            <div class="grid grid-cols-3 gap-x-4 mb-10">
                <!-- Tiempos -->
                <TimePanel :date="dates" :items="data" :loading="loading" width="65%" />

                <!-- PRODUCCIÓN DIARIA -->
                <ProductionPanel :items="data" :loading="loading" width="65%" class="col-span-2" />
            </div>
        </section>
        <section class="space-y-4">
            <!-- Segunda fila -->
            <div class="flex space-x-4 mt-4">
                <!-- Velocidad -->
                <VelocityPanel :items="data" :loading="loading" class="w-1/2" width="65%" />

                <!-- HISTOGRAMA -->
                <DesviacionPanel :items="data" :loading="loading" class="w-1/2" width="65%" />
            </div>

            <!-- Tercera fila -->
            <div class="flex space-x-4 pt-0">
                <!-- PELICULA -->
                <FilmPanel :items="data" :loading="loading" class="w-[45%]" />

                <!-- BASCULA -->
                <ScalePanel :items="data" :loading="loading" class="w-[55%]" width="65%" />
            </div>
        </section>
        <!-- <section v-for="(page, index) in Math.ceil(selectedVariables.length / 4)" :key="page" class="mt-52 space-x-4">
            <h1 class="font-bold text-lg">Variables ({{ index+1 }})</h1>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div v-for="(variable, index2) in selectedVariables.filter((e, i) => i >= index * 4 && i < (index + 1) * 4)"
                    :key="index2">
                    <VariablePanel :variableName="variable" width="65%"
                        :data="mapItemsToTimeSlots(variables.find(v => v.variable_name == variable).variable_original_name)" />
                </div>
                <div v-if="!selectedVariables.length" class="col-span-full mt-20">
                    <p class="flex flex-col space-y-2 items-center justify-center text-gray-400">
                        <i class="fa-regular fa-hand-point-left text-4xl"></i>
                        <span>Para ver información, selecciona las variables que quieras de lado
                            izquierdo.</span>
                    </p>
                </div>
            </div>
        </section> -->
        <section v-if="selectedVariables.length" class="mt-44 space-x-4">
            <h1 class="font-bold text-lg">Variables</h1>
            <div class="mt-6 grid grid-cols-3 gap-3">
                <div v-for="(variable, index2) in selectedVariables"
                    :key="index2">
                    <VariablePanel :variableName="variable" height="180"
                        :data="mapItemsToTimeSlots(variables.find(v => v.variable_name == variable).variable_original_name)" />
                </div>
            </div>
        </section>
        <section v-if="selectedVariables.length" class="my-10 col-span-full">
            <table class="w-full text-[13px] table-fixed">
                <thead class="*:border">
                    <tr class="*:px-2 *:py-1 *:text-start">
                        <th class="w-[15%]">Tiempo</th>
                        <th v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">{{
                            variable }}</th>
                    </tr>
                </thead>
                <tbody class="*:border-x">
                    <tr v-for="(time, index) in timeSlots" :key="index"
                        class="*:px-2 *:py-1 *:text-start even:bg-gray-100 last:border-b">
                        <td class="w-[15%]">{{ time }}</td>
                        <td v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">
                            {{ mapItemsToTimeSlots(variables.find(v => v.variable_name ==
                                variable).variable_original_name)[time] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Email modal -->
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
                            :reserve-keyword="false" placeholder="Agrega cualquier correo y presiona enter">
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
                            placeholder="Escribe una descripción si es necesario" clearable />
                        <InputError :message="emailForm.errors.description" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Adjunto" />
                        <p class="text-xs flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 22 22" class="text-green-700"
                                id="Microsoft-Excel-Logo--Streamline-Ultimate" height="16" width="16">
                                <desc>Microsoft Excel Logo Streamline Icon: https://streamlinehq.com</desc>
                                <g id="Microsoft-Excel-Logo--Streamline-Ultimate.svg">
                                    <path
                                        d="M20.125 1.75H7.4375a0.875 0.875 0 0 0 -0.875 0.875v1.3125h1.75V3.5h4.375v2.9575h-0.875l0 0.105v1.58375h0.875v2.9575h-0.875v1.68875h0.875V15.75h-1.2425a2.625 2.625 0 0 1 -2.2575000000000003 1.3125h-2.625V18.375a0.875 0.875 0 0 0 0.875 0.875H20.125a0.875 0.875 0 0 0 0.875 -0.875V2.625a0.875 0.875 0 0 0 -0.875 -0.875Zm-0.875 14h-4.375v-2.9575h4.375Zm0 -4.646249999999999h-4.375V8.14625h4.375Zm0 -4.646249999999999h-4.375V3.5h4.375Z"
                                        fill="currentColor" stroke-width="1"></path>
                                    <path
                                        d="M8.3125 15.75h0.875a1.3125 1.3125 0 0 0 1.3125 -1.3125v-7.875A1.3125 1.3125 0 0 0 9.1875 5.25h-7.875A1.3125 1.3125 0 0 0 0 6.5625v7.875A1.3125 1.3125 0 0 0 1.3125 15.75ZM4.1125 7.4375 5.25 9.2575 6.3875 7.4375h1.54875L6.02 10.5l1.91625 3.0625H6.3875L5.25 11.7425 4.1125 13.5625H2.56375L4.48 10.5 2.56375 7.4375Z"
                                        fill="currentColor" stroke-width="1"></path>
                                </g>
                            </svg>
                            <span class="text-secondary">Reporte Robag</span>
                        </p>
                    </div>
                </form>
            </template>
            <template #footer>
                <PrimaryButton @click="savePdfInProject" :disabled="emailForm.processing">
                    <i v-if="emailForm.processing" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                    Enviar correo
                </PrimaryButton>
            </template>
        </DialogModal>
</template>
<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import OEEPanel from '@/MyComponents/Home/OEEPanel.vue';
import TimePanel from '@/MyComponents/Home/TimePanel.vue';
import ProductionPanel from '@/MyComponents/Home/ProductionPanel.vue';
import VelocityPanel from '@/MyComponents/Home/VelocityPanel.vue';
import DesviacionPanel from '@/MyComponents/Home/DesviacionPanel.vue';
import FilmPanel from '@/MyComponents/Home/FilmPanel.vue';
import ScalePanel from '@/MyComponents/Home/ScalePanel.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { format, parse, parseISO, differenceInMinutes } from "date-fns";
import Loading from '@/Components/MyComponents/Loading.vue';
import VariablePanel from '@/MyComponents/Home/VariablePanel.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import jsPDF from 'jspdf'; // generador de pdf
import html2canvas from 'html2canvas'; // generador de pdf

export default {
    data() {
        const emailForm = useForm({
            main_email: null,
            cco: [],
            subject: null,
            description: null,
        });

        return {
            //forms
            emailForm,
            // modales
            showEmailModal: false,
            // cargas
            loading: true,
            loadingPDF: false,
            // general
            editModbusConfig: false,
            data: [],
            printing: false,
            // para graficas de variables
            panelLoading: true,
            items: [],
            variables: [],
        }
    },
    components: {
        ProductionPanel,
        PrimaryButton,
        DesviacionPanel,
        VelocityPanel,
        ScalePanel,
        TimePanel,
        FilmPanel,
        OEEPanel,
        Head,
        Loading,
        VariablePanel,
        DialogModal,
        InputError,
        InputLabel,
    },
    emits: ['updated-dates'],
    props: {
        bpm: {
            default: 120,
            type: Number,
        },
        dates: Array,
        date: String,
        timeSlots: Array,
        selectedVariables: Array,
    },
    methods: {
        mapItemsToTimeSlots(variable) {
            const usedItems = new Set(); // Para almacenar los IDs de los items ya utilizados

            const mappedData = this.timeSlots.map(slot => {
                const slotTime = parse(slot, "HH:mm", new Date());
                let closestItem = null;
                let minDifference = Infinity;

                this.items.forEach(item => {
                    const itemDate = parseISO(item.created_at);
                    const difference = differenceInMinutes(slotTime, itemDate);

                    // Considerar solo los items dentro del rango de 10 minutos hacia arriba y hacia abajo
                    if (Math.abs(difference) <= 10 && !usedItems.has(item.id)) {
                        // Verificar si es el más cercano hasta ahora
                        if (Math.abs(difference) < Math.abs(minDifference)) {
                            minDifference = difference;
                            closestItem = item;
                        }
                    }
                });

                // Si hay un item cercano dentro de los 5 minutos, se usa, de lo contrario, se usa 0
                if (closestItem) {
                    usedItems.add(closestItem.id); // Marcar el item como usado
                }

                return { [slot]: closestItem ? parseFloat(closestItem[variable]) : 0 };
            });

            // Combinar el array de objetos en un solo objeto
            const mergedData = mappedData.reduce((acc, curr) => {
                return { ...acc, ...curr };
            }, {});

            return mergedData;
        },
        downloadPdf() {
            window.location.href = '/download-pdf'; // Redirige a la ruta para descargar el PDF
        },
        async generatePdf() {
            this.loadingPDF = true;
            const content = document.getElementById('pdf-content');

            // Capturar el contenido con html2canvas
            const canvas = await html2canvas(content, {
                scale: 2, // Ajusta la escala para mejorar la calidad
                windowWidth: document.documentElement.scrollWidth, // Ajusta el ancho del contenido
            });

            // Convertir el contenido capturado en imagen
            const imgData = canvas.toDataURL('image/png');

            // Crear una instancia de jsPDF con orientación "landscape" y formato A4
            const pdf = new jsPDF({
                orientation: 'landscape', // Cambia la orientación a landscape
                unit: 'mm',
                format: 'a4',
            });

            // Obtener las dimensiones de la página
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();

            // Calcular las dimensiones de la imagen dentro del PDF
            let imgWidth = pdfWidth;
            let imgHeight = (canvas.height * pdfWidth) / canvas.width;

            let heightLeft = imgHeight;
            let position = 0;

            // Agregar la primera parte de la imagen
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight, '', 'FAST'); // FAST usa compresión

            // Dividir en varias páginas si es necesario
            while (heightLeft > 0) {
                position -= pdfHeight;
                heightLeft -= pdfHeight;

                if (heightLeft > 0) {
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight, '', 'FAST'); // FAST usa compresión
                }
            }
            this.loadingPDF = false;
            // Descargar el PDF generado
            pdf.save('example.pdf');
        },
        async savePdfInProject() {
            this.loadingPDF = true;
            const content = document.getElementById('pdf-content');

            // Capturar el contenido con html2canvas
            const canvas = await html2canvas(content, {
                scale: 2, // Ajusta la escala para mejorar la calidad
                windowWidth: document.documentElement.scrollWidth,
            });

            // Convertir el contenido capturado en imagen
            const imgData = canvas.toDataURL('image/png');

            // Crear una instancia de jsPDF con orientación "landscape" y formato A4
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4',
            });

            // Obtener las dimensiones de la página
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();

            let imgWidth = pdfWidth;
            let imgHeight = (canvas.height * pdfWidth) / canvas.width;

            let heightLeft = imgHeight;
            let position = 0;

            // Agregar la primera parte de la imagen
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight, '', 'FAST'); // FAST usa compresión

            while (heightLeft > 0) {
                position -= pdfHeight;
                heightLeft -= pdfHeight;

                if (heightLeft > 0) {
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight, '', 'FAST'); // FAST usa compresión
                }
            }

            // Obtener el contenido del PDF en formato binario
            const pdfOutput = pdf.output('blob');

            // Crear un FormData para enviar el PDF al backend
            const formData = new FormData();
            formData.append('file', pdfOutput, 'example.pdf');

            // Enviar el PDF al backend con Axios o Fetch
            await axios.post('/save-pdf', formData, {
                headers: {
                'Content-Type': 'multipart/form-data',
                },
            });
            this.loadingPDF = false;
            this.$notify({
                title: 'Correcto',
                message: 'Correo enviado',
                type: 'success'
            })
        },
        formatDateTime(dateTime) {
            return format(dateTime, "dd MMM, yyyy - H:mm a");
        },
        async getDataByDateRange() {
            this.loading = true;
            try {
                const response = await axios.post(route('robag.get-data-by-date-range'), { date: this.dates });
                if (response.status === 200) {
                    this.data = response.data.data;
                    this.$emit(
                        'updated-dates',
                        this.data.length ? this.dates : []
                    );
                }

            } catch (error) {
                console.log(error)
            } finally {
                // this.loading = false;
            }
        },
        // variables independientes
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
        },
        async fetchMachineData() {
            try {
                this.loading = true;

                const response = await axios.post(route('robag.get-data-by-date-range', {
                    date: [`${this.date} ${this.timeSlots[0]}`, `${this.date} ${this.timeSlots[this.timeSlots.length - 1]}`],
                    subHours: 0,
                }));

                if (response.status === 200) {
                    this.items = response.data.data;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        print() {
            this.printing = true;
            setTimeout(() => {
                window.print();
            }, 100);
        },
        handleAfterPrint() {
            this.printing = false;
        },
    },
    async mounted() {
        await this.fetchMachineVariables();
        await this.getDataByDateRange(); // Recupera los registros del día de hoy
        await this.fetchMachineData();
        window.addEventListener('afterprint', this.handleAfterPrint);
    },
    beforeDestroy() {
        window.removeEventListener('afterprint', this.handleAfterPrint);
    }
}
</script>