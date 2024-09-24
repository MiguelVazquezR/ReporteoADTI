<template>
    <!-- Estado de carga de pdf -->
    <div v-if="loadingPDF" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="flex flex-col justify-center items-center text-center">
            <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-32 w-32 mb-4"></div>
            <h2 class="text-white text-2xl font-semibold">Generando PDF...</h2>
            <p class="text-white text-lg mt-2">Por favor espera un momento</p>
        </div>
    </div>

    <Head title="Reporte Robag" />
    <div v-if="!loadingTemplate && !printing" class="flex space-x-3 justify-end mx-20 mt-5">
        <el-dropdown split-button type="primary" @click="handleActionPdf('download')">
            Descargar PDF
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item @click="showEmailModal = true">Enviar por correo</el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
        <!-- <button v-if="!loading && !printing" @click="print" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3">Descargar PDF</button> -->
        <!-- <button :disabled="loadingPDF" v-if="!loadingTemplate && !printing" @click="generatePdf" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3 disabled:bg-gray-500 disabled:cursor-not-allowed">Descargar PDF</button>
            <button :disabled="loadingPDF" v-if="!loadingTemplate && !printing" @click="showEmailModal = true" class="bg-primary text-white font-bold py-1 px-3 rounded-md text-sm mt-3 disabled:bg-gray-500 disabled:cursor-not-allowed">Enviar por correo</button> -->
    </div>
    <div class="text-center mt-4">
        <i v-if="loadingPDF" class="fa-solid fa-circle-notch fa-spin text-xl mr-2"></i>
    </div>
    <Loading v-if="loadingTemplate" class="mt-16" />
    <main v-else class="px-10 min-h-screen my-4" id="pdf-content">
        <header class="text-center font-bold">
            <p>
                Reporte de Robag 1: {{ formatDateTime(dates[0]) ?? '' }} a {{ formatDateTime(dates[1]) ?? '' }}
            </p>
        </header>
        <section class="space-y-4">
            <!-- graficas en rectangulo negro -->
            <OEEPanel ref="oeePanel" :date="dates" :items="data" :loading="loadingCharts" :teoricProduction="bpm" />

            <!-- Contenedor de gráficas parte inferior (debajo de rectangulo negro) -->
            <!-- primer fila -->
            <div class="mt-4 grid grid-cols-3 gap-4">
                <!-- Tiempos -->
                <TimePanel :date="dates" :items="data" :loading="loadingCharts" />

                <!-- PRODUCCIÓN DIARIA -->
                <ProductionPanel :items="data" :loading="loadingCharts" />

                <!-- Velocidad -->
                <VelocityPanel :items="data" :loading="loadingCharts" />

                <!-- HISTOGRAMA -->
                <DesviacionPanel :items="data" :loading="loadingCharts" />

                <!-- PELICULA -->
                <FilmPanel :items="data" :loading="loadingCharts" />

                <!-- BASCULA -->
                <ScalePanel :items="data" :loading="loadingCharts" />
            </div>
        </section>
        <!-- <section class="space-y-4">
            <div class="flex space-x-4 mt-4">
                <VelocityPanel :items="data" :loading="loadingCharts" class="w-1/2" />
                <DesviacionPanel :items="data" :loading="loadingCharts" class="w-1/2" />
            </div>
            <div class="flex space-x-4 pt-36">
                <FilmPanel :items="data" :loading="loadingCharts" class="w-[45%]" />
                <ScalePanel :items="data" :loading="loadingCharts" class="w-[55%]" />
            </div>
        </section> -->
        <section v-if="selectedVariables.length" class="mt-6 space-x-4">
            <h1 class="font-bold text-lg">Variables</h1>
            <div class="mt-6 grid grid-cols-3 gap-3">
                <!-- <div v-for="(variable, index2) in selectedVariables" :key="index2">
                    <VariablePanel :variableName="variable" height="180"
                        :class="index2 > 8 && index2 < 12 ? 'mt-16' : null"
                        :data="mapItemsToTimeSlots(variables.find(v => v.name == variable).name)" />
                </div> -->
                <div v-for="(variable, index) in selectedVariables" :key="index">
                    <VariablePanel :variableName="variable" height="180" :data="variablesMapped ? variablesMapped[variable] : {}"
                        :class="index > 8 && index < 12 ? 'mt-16' : null" />
                </div>
            </div>
        </section>
        <section v-if="selectedVariables.length" class="my-10 col-span-full">
            <table class="w-full text-[13px] table-fixed">
                <thead class="*:border">
                    <tr class="*:px-2 *:py-1 *:text-start">
                        <th class="w-[15%]">Tiempo</th>
                        <th v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">
                            {{ variable }}
                        </th>
                    </tr>
                </thead>
                <tbody class="*:border-x">
                    <tr v-for="(time, index) in timeSlots" :key="index"
                        class="*:px-2 *:py-1 *:text-start even:bg-gray-100 last:border-b">
                        <td class="w-[15%]">{{ time.split(' ')[1] }}</td>
                        <td v-for="(variable, index) in selectedVariables" :key="index" class="w-[15%]">
                            {{ variablesMapped ? variablesMapped[variable][time.split(' ')[1]] : '' }}
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
                        <i class="fa-solid fa-file-pdf text-lg text-red-600"></i>
                        <span class="text-secondary">Reporte Robag</span>
                    </p>
                </div>
            </form>
        </template>
        <template #footer>
            <PrimaryButton @click="handleActionPdf('email')" :disabled="emailForm.processing || loadingPDF">
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
            loadingTemplate: true,
            loadingCharts: false,
            loadingPDF: false,
            // general
            editModbusConfig: false,
            data: [],
            printing: false,
            // para graficas de variables
            items: [],
            variables: [],
            variablesMapped: null,
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
        // downloadPdf() {
        //     window.location.href = '/download-pdf'; // Redirige a la ruta para descargar el PDF
        // },
        // print() {
        //     this.printing = true;
        //     setTimeout(() => {
        //         window.print();
        //     }, 100);
        // },
        // handleAfterPrint() {
        //     this.printing = false;
        // },
        async handleActionPdf(action) {
            this.loadingPDF = true;
            if ( action === 'download' ) {
                await this.generatePdf();
            } else if ( action === 'email' ) {
                this.showEmailModal = false;
                await this.savePdfInProjectAndSend();
            }
            this.loadingPDF = false;
        },
        sendEmail(pdfPath) {
            this.emailForm.transform(data => ({
                ...data,
                dates: this.searchDate,
                pdf_path: pdfPath // Incluir la ruta del PDF en la petición
            })).post(route('robag.email-report'), {
                onSuccess: () => {
                    this.showEmailModal = false;
                    this.emailForm.reset();
                    this.$notify({
                        title: 'Correo enviado',
                        message: '',
                        type: 'success'
                    });
                },
                onError: (error) => {
                    console.log(error);
                },
            });
        },
        formatDateTime(dateTime) {
            return format(dateTime, "dd MMM, yyyy - H:mm a");
        },
        async getDataByDateRange() {
            this.loadingCharts = true;
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
                this.loadingCharts = false;
            }
        },
        async generatePdf() {
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
            // Descargar el PDF generado
            pdf.save('example.pdf');
        },
        async savePdfInProjectAndSend() {
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

            try {
                // Enviar el PDF al backend y obtener la ruta del archivo guardado
                const response = await axios.post('/save-pdf', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                const filePath = response.data.path; // La ruta donde se guardó el PDF

                // Pasar la ruta del PDF a la función sendEmail
                this.sendEmail(filePath);

            } catch (error) {
                console.error("Error guardando el PDF:", error);
            }
        },
        // variables independientes
        mapAllVariables() {
            let variablesMapped = {};

            this.variables.forEach(variable => {
                const variableName = variable.name;
                variablesMapped[variableName] = this.mapItemsToTimeSlots(variableName);
            });

            this.variablesMapped = variablesMapped;
        },
        mapItemsToTimeSlots(variable) {
            const usedItems = new Set(); // Para almacenar los IDs de los items ya utilizados

            const mappedData = this.timeSlots.map(slot => {
                // Convertir el slot en una fecha completa (fecha + hora)
                const slotTime = parse(slot, "yyyy-MM-dd H:mm", new Date());
                let closestItem = null;
                let minDifference = Infinity;

                this.items.forEach(item => {
                    // Asegúrate de que la fecha de 'item.created_at' también incluya la fecha
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

                // Si hay un item cercano dentro de los 10 minutos, se usa, de lo contrario, se usa 0
                if (closestItem) {
                    usedItems.add(closestItem.id); // Marcar el item como usado
                }

                return { [slot.split(' ')[1]]: closestItem ? parseFloat(parseFloat(closestItem.data[variable]).toFixed(2)) : 0 };
            });

            // Combinar el array de objetos en un solo objeto
            const mergedData = mappedData.reduce((acc, curr) => {
                return { ...acc, ...curr };
            }, {});

            return mergedData;
        },
        async fetchMachineVariables() {
            try {
                this.loadingTemplate = true;

                const response = await axios.get(route('machine-variables.get-variables', 'Robag1'));

                if (response.status === 200) {
                    this.variables = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingTemplate = false;
            }
        },
        async fetchMachineData() {
            try {
                this.loadingCharts = true;

                // Enviar el rango de fechas correctamente
                const response = await axios.post(route('robag.get-data-by-date-range', {
                    date: [`${this.date} ${this.timeSlots[0].split(' ')[1]}`, `${this.date} ${this.timeSlots[this.timeSlots.length - 1].split(' ')[1]}`],
                    subHours: 0,
                }));

                if (response.status === 200) {
                    this.items = response.data.data;
                    this.mapAllVariables();
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingCharts = false;
            }
        },
    },
    async mounted() {
        this.fetchMachineVariables();
        await this.getDataByDateRange(); // Recupera los registros del día de hoy
        await this.fetchMachineData();
        // window.addEventListener('afterprint', this.handleAfterPrint);
    },
    beforeDestroy() {
        // window.removeEventListener('afterprint', this.handleAfterPrint);
    }
}
</script>

<style>
.loader {
    border-top-color: #1676A2; /* Cambia el color del spinner aquí */
    animation: spinner 1.5s infinite linear;
}

@keyframes spinner {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>