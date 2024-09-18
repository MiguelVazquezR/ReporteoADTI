<template>
    <PublicLayout title="Editar variable">
        <main class="lg:py-10 lg:px-14">
            <Link :href="route('machine-variables.index')"
                class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
            <i class="fa-solid fa-chevron-left"></i>
            </Link>
            <section class="w-full">
                <form @submit.prevent="update" class="border border-grayD9 px-4 py-3 rounded-xl w-1/2 mx-auto">
                    <h1 class="font-bold">Editar variable</h1>
                    <div class="mt-3">
                        <InputLabel value="Nombre *" />
                        <el-input v-model="form.variable_name" placeholder="Ej. Tiempo de trabajo" clearable />
                        <InputError :message="form.errors.variable_name" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Descripción" />
                        <el-input v-model="form.variable_description" :autosize="{ minRows: 3, maxRows: 5 }"
                            type="textarea" placeholder="Escribe una descripción para entender más la variable"
                            clearable />
                        <InputError :message="form.errors.variable_description" />
                    </div>
                    <div class="grid grid-cols-3 gap-3 mt-2">
                        <div>
                            <InputLabel value="Tipo de dato *" />
                            <el-select v-model="form.type" placeholder="Selecciona">
                                <el-option v-for="item in types" :key="item.value" :label="item.label" :value="item.value" />
                            </el-select>
                            <InputError :message="form.errors.type" />
                        </div>
                        <div class="text-center">
                            <InputLabel value="Dirección *" />
                            <el-input-number v-model="form.variable_address" :min="0" />
                            <InputError :message="form.errors.variable_address" />
                        </div>
                        <div class="text-center">
                            <InputLabel value="Longitud (palabras) *" />
                            <el-input-number v-model="form.words" :min="1" />
                            <InputError :message="form.errors.words" />
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-1 mt-6">
                        <PrimaryButton :disabled="form.processing">
                            <i v-if="form.processing" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                            Guardar cambios
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
            machine_name: 'Robag',
            variable_name: this.variable.variable_name,
            variable_description: this.variable.variable_description,
            variable_address: this.variable.variable_address,
            words: this.variable.words,
            type: this.variable.type,
        });

        return {
            // formularios
            form,
            // general
            types: [
                {
                    value: 'byte',
                    label: 'Byte (8 bits)',
                },
                {
                    value: 'int',
                    label: 'Entero',
                },
                {
                    value: 'uint',
                    label: 'Entero sin signo',
                },
                {
                    value: 'float',
                    label: 'Flotante',
                },
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
        variable: Object,
    },
    methods: {
        update() {
            this.form.put(route('machine-variables.update',this.variable), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Correcto',
                        message: '',
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
