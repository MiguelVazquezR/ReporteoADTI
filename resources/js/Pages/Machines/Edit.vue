<template>
    <PublicLayout title="Editar maquina">
        <main class="lg:py-10 lg:px-14">
            <Link :href="route('home')"
                class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
            <i class="fa-solid fa-chevron-left"></i>
            </Link>
            <section class="w-full">
                <form @submit.prevent="update" class="border border-grayD9 px-4 py-3 rounded-xl w-1/2 mx-auto">
                    <h1 class="font-bold">Editar máquina</h1>
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
                        <!-- <div>
                            <InputLabel value="Imagen *" />
                            <input type="file" accept="image/*" @change="onFileChanged($event)" />
                            <InputError :message="form.errors.image" />
                        </div> -->
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
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    data() {
        const form = useForm({
            name: this.machine.name,
            class_name: this.machine.class_name.split('Models\\')[1],
            image: null,
        });

        return {
            form,
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        InputError,
        InputLabel,
        Link
    },
    props: {
        machine: Object
    },
    methods: {
        update() {
            this.form.put(route('machines.update', this.machine), {
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
