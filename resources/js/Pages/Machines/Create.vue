<template>
    <PublicLayout title="Crear nueva maquina">
        <main class="lg:py-10 lg:px-14">
            <Link :href="route('home')"
                class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
            <i class="fa-solid fa-chevron-left"></i>
            </Link>
            <section class="w-full">
                <form @submit.prevent="store" class="border border-grayD9 px-4 py-3 rounded-xl w-1/2 mx-auto">
                    <h1 class="font-bold">Crear nueva máquina</h1>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div>
                            <InputLabel value="Nombre *" />
                            <el-input v-model="form.name" placeholder="Ej. Robag sección 2" clearable />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div>
                            <InputLabel value="Nombre de la clase *" />
                            <el-input v-model="form.class_name" placeholder="Solo para desarrollador" clearable />
                            <InputError :message="form.errors.class_name" />
                        </div>
                        <div>
                            <InputLabel value="Clonar variables" />
                            <el-select v-model="form.machine_id_to_clone_vars" placeholder="Selecciona">
                                <el-option label="No clonar variables de otra máquina" :value="0" />
                                <el-option v-for="item in machines" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors.class_name" />
                        </div>
                        <div>
                            <InputLabel value="Imagen *" />
                            <input type="file" accept="image/*" @change="onFileChanged($event)" />
                            <InputError :message="form.errors.image" />
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-1 mt-6">
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
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    data() {
        const form = useForm({
            name: null,
            class_name: null,
            machine_id_to_clone_vars: 0,
            image: null,
        });

        return {
            form,
        }
    },
    props: {
        machines: {
            type: Array,
            required: true
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        InputError,
        InputLabel,
        Link
    },
    methods: {
        store() {
            this.form.post(route('machines.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Correcto',
                        type: 'success'
                    })
                },
                onError: (error) => {
                    console.log(error);
                },
            });
        },
        onFileChanged(event) {
            this.form.image = event.target.files[0];
        }
    },
}
</script>
