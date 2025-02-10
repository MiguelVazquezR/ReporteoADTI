<template>
    <PublicLayout title="Reportes programados">
        <main class="lg:py-10 lg:px-14">
            <section class="flex items-center justify-between mx-14">
                <Link :href="route('home')">
                <button class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                </Link>
                <h1 class="text-2xl font-bold">Reportes programados</h1>
                <PrimaryButton @click="$inertia.visit(route('schedule-email-settings.create'))">Crear nuevo
                </PrimaryButton>
            </section>
            <section class="flex flex-col items-center">
                <div class="lg:flex justify-between mb-2 mt-6">
                    <el-dropdown :disabled="disableMassiveActions" @command="handleCommand">
                        <el-button type="primary" :disabled="disableMassiveActions">
                            Acciones masivas
                            <i class="fa-solid fa-chevron-down ml-2 text-xs"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item command="edit">Editar</el-dropdown-item>
                                <el-dropdown-item command="delete">Eliminar</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
                <el-table :data="schedules" @row-click="handleRowClick" max-height="520"
                    style="width: 90%; margin-top: 30px" @selection-change="handleSelectionChange"
                    ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="report_name" label="Reporte" />
                    <el-table-column prop="main_email" label="Correo principal" />
                    <el-table-column prop="cco" label="Con copia a" width="230">
                        <template #default="scope">
                            <p v-for="(item, index) in scope.row.cco" :key="index"
                                class="bg-primarylight text-gray-500 mt-1 rounded-md px-2 py-1 text-xs">
                                {{ item }}
                            </p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="subject" label="Asunto" />
                    <el-table-column prop="frecuency" label="Frecuencia" />
                    <el-table-column prop="weekday" label="Día de la semana">
                        <template #default="scope">
                            <p>{{ scope.row.frecuency == 'Diariamente' ? 'Todos' : scope.row.weekday }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="time" label="Hora" />
                </el-table>
            </section>
        </main>
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Confirmación de eliminación</h1>
            </template>
            <template #content>
                <p>
                    Estas a punto de eliminar los reportes programados seleccionados. Una vez eliminados ya no se pueden
                    recuperar.
                    ¿Continuar?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showDeleteConfirm = false" :disabled="deleting">Cancelar</CancelButton>
                    <PrimaryButton @click="deleteSelections" :disabled="deleting">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
        <DialogModal :show="showMassiveEditModal" @close="showMassiveEditModal = false" maxWidth="lg">
            <template #title>
                <h1 class="font-bold text-left">Editar programación de reportes masivamente</h1>
            </template>
            <template #content>
                <form @submit.prevent="massiveUpdate" class="mb-32">
                    <div>
                        <InputLabel value="Reporte de metabase" />
                        <el-select :teleported="false" v-model="form.report_name" placeholder="Selecciona">
                            <el-option v-for="item in dashboards" :key="item" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.report_name" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="CCO" />
                        <el-select :teleported="false" v-model="form.cco" multiple filterable allow-create
                            default-first-option no-data-text="Escribe el correo y presiona entrer para guardarlo"
                            :reserve-keyword="false" placeholder="Enviar copia a los correos agregados">
                        </el-select>
                        <InputError :message="form.errors.cco" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Hora de envío" />
                        <el-time-select :teleported="false" v-model="form.time" class="!w-1/3" start="00:00"
                            step="00:30" end="23:30" placeholder="Ej: 8:00" />
                        <InputError :message="form.errors.time" />
                    </div>
                    <p class="text-gray37 text-sm mt-3">
                        Todos los ingresos seleccionados <span class="text-primary">({{
                            $refs.multipleTableRef.value.length
                            }})</span> se actualizarán con los valores establecidos.
                    </p>
                </form>
            </template>
            <template #footer>
                <CancelButton @click="showMassiveEditModal = false" class="mr-1">Cancelar</CancelButton>
                <PrimaryButton @click="massiveUpdate" :disabled="!form.cco || !form.time || form.processing">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Guardar cambios
                </PrimaryButton>
            </template>
        </DialogModal>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CancelButton from '@/Components/CancelButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import DialogModal from '@/Components/DialogModal.vue';

export default {
    data() {
        const form = useForm({
            report_name: null,
            cco: [],
            time: null,
            selections: [],
        });

        return {
            form,
            // tabla
            disableMassiveActions: true,
            search: '',
            showDeleteConfirm: false,
            deleting: false,
            showMassiveEditModal: false,
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        Link,
        ConfirmationModal,
        CancelButton,
        InputLabel,
        InputError,
        DialogModal,
    },
    props: {
        schedules: Array,
        dashboards: Array,
    },
    methods: {
        openMassiveEditModal() {
            this.showMassiveEditModal = true;
            this.form.selections = this.$refs.multipleTableRef.value;
        },
        handleCommand(command) {
            if (command == 'delete') {
                this.showDeleteConfirm = true;
            } else if (command == 'edit') {
                this.openMassiveEditModal();
            }
        },
        handleRowClick(row) {
            this.$inertia.visit(route('schedule-email-settings.edit', row));
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        massiveUpdate() {
            this.form.post(route('schedule-email-settings.massive-update'), {
                onSuccess: () => {
                    this.showMassiveEditModal = false;
                    // iterar items seleccionados y actualizar datos
                    this.$refs.multipleTableRef.value.forEach((schedule) => {
                        schedule.report_name = this.form.report_name;
                        schedule.cco = this.form.cco;
                        schedule.time = this.form.time;
                    });
                    //limpiar selecciones
                    this.$refs.multipleTableRef.clearSelection();
                    this.$refs.multipleTableRef.value = [];
                    this.disableMassiveActions = true;
                    this.form.reset();
                },
                onError: (error) => {
                    console.log(error);
                }
            });
        },
        async deleteSelections() {
            this.deleting = true;
            try {
                const items_ids = this.$refs.multipleTableRef.value.map(item => item.id);
                const response = await axios.post(route('schedule-email-settings.massive-delete', {
                    items_ids
                }));

                if (response.status === 200) {
                    this.showDeleteConfirm = false;
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                        position: "bottom-right",
                    });

                    // update list
                    let deletedIndexes = [];
                    this.schedules.forEach((schedule, index) => {
                        if (items_ids.includes(schedule.id)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.schedules.splice(index, 1);
                    }
                }
            } catch (err) {
                this.$notify({
                    title: 'No se pudo completar la solicitud',
                    message: '',
                    type: 'error',
                    position: "bottom-right",
                });
                console.log(err);
            } finally {
                this.deleting = false;
            }
        },
    },
}
</script>
