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
                    <!-- pagination -->
                    <div class="flex space-x-5 items-center">
                        <!-- <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="users.length / itemsPerPage" /> -->
                        <div class="mt-2 lg:mt-0">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Continuar?" @confirm="deleteSelections">
                                <template #reference>
                                    <el-button type="danger" plain class="mb-3"
                                        :disabled="disableMassiveActions">Eliminar</el-button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </div>
                </div>
                <el-table :data="variables" @row-click="handleRowClick" max-height="520" style="width: 90%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="variable_name" label="Nombre" />
                    <el-table-column prop="variable_description" label="Descripción" width="450" />
                    <el-table-column prop="variable_address" label="Dirección" />
                    <el-table-column prop="words" label="Longitud (palabras)" />
                    <el-table-column prop="type" label="Tipo de dato" />
                </el-table>
            </section>
        </main>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

export default {
    data() {

        return {
            // tabla
            disableMassiveActions: true,
            search: '',

            // pagination
            itemsPerPage: 50,
            start: 0,
            end: 50,
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        Link
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
                        variable.variable_name.toLowerCase().includes(this.search.toLowerCase()) ||
                        variable.variable_address.toLowerCase().includes(this.search.toLowerCase()) ||
                        variable.type.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
    methods: {
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
            try {
                const items_ids = this.$refs.multipleTableRef.value.map(item => item.id);
                const response = await axios.post(route('machine-variables.massive-delete', {
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
            }
        },
    },
}
</script>
