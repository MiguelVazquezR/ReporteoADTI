<template>
    <main class="rounded-[20px] border border-grayD9 p-4 h-60">
        <div v-if="loading" class="text-xs my-4 text-center">
            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </div>

        <div v-else>
            <p class="text-[#6D6E72] font-bold text-sm">ESTADÍSTICAS DE LA BÁSCULA </p>
            <div v-for="(item, index) in getScaleStatistics" :key="index" class="flex items-center justify-between mt-3">
                <p class="flex items-center space-x-2">
                    <i class="fa-regular fa-circle text-[5px]"></i>
                    <span class="text-sm">{{ item.name }}</span>
                </p>
                <hr class="flex-1 border-dashed border-black mx-4">
                <el-tag :color="item.tagColor" style="color: #373737; border: transparent;" effect="light" round>
                    {{ item.value }}
                </el-tag>
            </div>
        </div>
    </main>
</template>

<script>
export default {
    data() {
        return {
        }
    },
    computed: {
        getScaleStatistics() {
            const items = this.items; // Suponiendo que `items` es el array de objetos que recibes
            const itemCount = items.length;

            const totals = items.reduce((acc, item) => {
                acc.mean_weight += parseFloat(item.mean_weight) || 0;
                acc.standard_deviation += parseFloat(item.standard_deviation) || 0;
                acc.total_dump_weight += parseFloat(item.total_dump_weight) || 0;
                acc.total_giveaway += parseFloat(item.total_giveaway) || 0;
                acc.giveaway_percentage += parseFloat(item.giveaway_percentage) || 0;
                return acc;
            }, {
                mean_weight: 0,
                standard_deviation: 0,
                total_dump_weight: 0,
                total_giveaway: 0,
                giveaway_percentage: 0
            });

            const averages = {
                mean_weight: itemCount ? (totals.mean_weight / itemCount).toFixed(2) + ' g' : 'Sin datos',
                standard_deviation: itemCount ? (totals.standard_deviation / itemCount).toFixed(2) : 'Sin datos',
                total_dump_weight: itemCount ? (totals.total_dump_weight / itemCount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") + ' g' : 'Sin datos',
                total_giveaway: itemCount ? (totals.total_giveaway / itemCount).toFixed(2) + ' g' : 'Sin datos',
                giveaway_percentage: itemCount ? (totals.giveaway_percentage / itemCount).toFixed(2) + '%' : 'Sin datos'
            };

            return [
                {
                    name: 'Peso medio',
                    value: averages.mean_weight,
                    tagColor: '#E6EFFC',
                },
                {
                    name: 'Desviación estándar',
                    value: averages.standard_deviation,
                    tagColor: '#F7E7FD',
                },
                {
                    name: 'Peso total de descarga',
                    value: averages.total_dump_weight,
                    tagColor: '#FAFDE6',
                },
                {
                    name: 'Total regalado',
                    value: averages.total_giveaway,
                    tagColor: '#FFF2DE',
                },
                {
                    name: 'Porcentaje regalado',
                    value: averages.giveaway_percentage,
                    tagColor: '#FFDEDE',
                },
            ];
        }
    },
    props: {
        items: {
            default: [],
            required: true,
            type: Array,
        },
        loading: Boolean, //estado de carga al obtener las datos
    },
    methods: {

    }
}
</script>
