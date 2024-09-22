<template>
    <PublicLayout title="Variables Modbus">
        <main class="lg:py-10 lg:px-14">
            <section class="flex items-center justify-between mx-14">
                <Link :href="route('home')">
                <button class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                </Link>
                <PrimaryButton @click="$inertia.visit(route('machine-variables.create'))">Crear nuevo</PrimaryButton>
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
                                <el-dropdown-item command="toggle-status">Cambiar estado</el-dropdown-item>
                                <el-dropdown-item command="delete">Eliminar</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
                <el-table :data="variables" @row-click="handleRowClick" max-height="520" style="width: 90%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="description" label="Descripción" width="450" />
                    <el-table-column prop="address" label="Dirección" />
                    <el-table-column prop="words" label="Longitud (palabras)" />
                    <el-table-column prop="type" label="Tipo de dato" />
                    <el-table-column prop="is_active" label="Estado">
                        <template #default="scope">
                            <p :class="scope.row.is_active ? 'text-green-600' : 'text-red-600'">
                                {{ scope.row.is_active ? 'Lectura activa' : 'Lectura inactiva' }}
                            </p>
                        </template>
                    </el-table-column>
                </el-table>
            </section>
        </main>
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Confirmación de eliminación</h1>
            </template>
            <template #content>
                <p>
                    Estas a punto de eliminar las variables seleccionadas. Una vez eliminadas ya no se pueden recuperar.
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
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CancelButton from '@/Components/CancelButton.vue';

export default {
    data() {

        return {
            // tabla
            disableMassiveActions: true,
            search: '',
            showDeleteConfirm: false,
            deleting: false,
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        Link,
        ConfirmationModal,
        CancelButton,
    },
    props: {
        variables: Array,
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.variables.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.variables.filter(
                    (variable) =>
                        variable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        variable.address.toLowerCase().includes(this.search.toLowerCase()) ||
                        variable.type.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
    methods: {
        handleCommand(command) {
            if (command == 'delete') {
                this.showDeleteConfirm = true;
            } else if (command == 'toggle-status') {
                this.toggleStatus();
            }
        },
        handleRowClick(row) {
            this.$inertia.visit(route('machine-variables.edit', row));
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
        async deleteSelections() {
            this.deleting = true;
            try {
                const items_ids = this.$refs.multipleTableRef.value.map(item => item.id);
                const response = await axios.post(route('machine-variables.massive-delete', {
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
                    this.variables.forEach((variable, index) => {
                        if (items_ids.includes(variable.id)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);
                    
                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.variables.splice(index, 1);
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
        async toggleStatus() {
            try {
                const items_ids = this.$refs.multipleTableRef.value.map(item => item.id);
                const response = await axios.post(route('machine-variables.massive-toggle-status', {
                    items_ids
                }));

                if (response.status === 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                        position: "bottom-right",
                    });

                    // update list
                    let updatedIndex = [];
                    this.variables.forEach((variable, index) => {
                        if (items_ids.includes(variable.id)) {
                            updatedIndex.push(index);
                        }
                    });

                    updatedIndex.forEach(element => {
                        this.variables[element].is_active = !this.variables[element].is_active;
                    });

                    // Limpiar selecciones de la tabla
                    if (this.$refs.multipleTableRef) {
                        // Si quieres limpiar todas las selecciones
                        this.$refs.multipleTableRef.clearSelection();
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
            }
        },
    },
}
</script>
