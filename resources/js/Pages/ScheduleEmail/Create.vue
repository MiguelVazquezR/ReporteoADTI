<template>
    <PublicLayout title="Crear Programación de correos">
        <main class="lg:py-10 lg:px-14">
            <Link :href="route('schedule-email-settings.index')"
                class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
            <i class="fa-solid fa-chevron-left"></i>
            </Link>
            <section class="w-full">
                <form @submit.prevent="store" class="border border-grayD9 px-4 py-3 rounded-xl w-1/2 mx-auto">
                    <h1 class="font-bold">Envío de reporte automático</h1>
                    <div class="mt-2">
                        <InputLabel value="Reporte de metabase" />
                        <el-select v-model="form.report_name" placeholder="Selecciona">
                            <el-option v-for="item in dashboards" :key="item" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.report_name" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Correo electrónico principal*" />
                        <el-input v-model="form.main_email" placeholder="Ej. admin@gmail.com" clearable />
                        <InputError :message="form.errors.main_email" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="CCO" />
                        <el-select v-model="form.cco" multiple filterable allow-create default-first-option
                            no-data-text="Escribe el correo y presiona entrer para guardarlo" :reserve-keyword="false"
                            placeholder="Enviar copia a los correos agregados">
                        </el-select>
                        <InputError :message="form.errors.cco" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Asunto*" />
                        <el-input v-model="form.subject" placeholder="Reporte de ..." clearable />
                        <InputError :message="form.errors.subject" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Descripción del correo" />
                        <el-input v-model="form.description" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                            placeholder="Escribe una descripción si es necesario" clearable />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Frecuencia de envío" />
                        <el-select v-model="form.frecuency" placeholder="Selecciona" class="!w-1/2">
                            <el-option v-for="item in frecuencyList" :key="item" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.frecuency" />
                    </div>
                    <div v-if="form.frecuency == 'Una vez a la semana'" class="mt-2">
                        <InputLabel value="Día de envío" />
                        <el-select v-model="form.weekday" placeholder="Selecciona" class="!w-1/2">
                            <el-option v-for="item in weekdays" :key="item" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.date" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Hora de envío" />
                        <el-time-select v-model="form.time" class="!w-1/2" start="00:00" step="00:30" end="23:30"
                            placeholder="Ej: 8:00" />
                        <InputError :message="form.errors.time" />
                    </div>
                    <div class="flex items-center justify-end space-x-1 mt-4">
                        <PrimaryButton :disabled="form.processing">
                            <i v-if="form.processing" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                            Crear
                        </PrimaryButton>
                    </div>
                </form>
            </section>
        </main>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    data() {
        const form = useForm({
            report_name: null,
            main_email: null,
            cco: [],
            subject: null,
            description: null,
            frecuency: 'Diariamente',
            weekday: null,
            time: null,
        });

        return {
            // formularios
            form,
            // general
            frecuencyList: [
                'Diariamente',
                'Una vez a la semana',
            ],
            weekdays: [
                'lunes',
                'martes',
                'miércoles',
                'jueves',
                'viernes',
                'sábado',
                'domingo',
            ],
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        DialogModal,
        InputError,
        InputLabel,
        Link
    },
    props: {
        dashboards: {
            type: Array,
            required: true,
        },
    },
    methods: {
        store() {
            this.form.post(route('schedule-email-settings.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Programación creada',
                        type: 'success'
                    })
                },
                onError: (error) => {
                    console.log(error);
                },
            });
        },
    },
}
</script>
