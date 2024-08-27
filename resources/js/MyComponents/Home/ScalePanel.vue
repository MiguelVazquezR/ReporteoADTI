<template>
    <main class="rounded-[20px] border border-grayD9 p-4 w-1/2">
        <p class="text-[#6D6E72] font-bold text-sm">ESTADÍSTICAS DE LA BÁSCULA </p>
        <div v-for="(item, index) in scaleStatistics" :key="index"
            class="flex items-center justify-between mt-3">
            <p class="flex items-center space-x-2">
                <i class="fa-regular fa-circle text-[5px]"></i>
                <span class="text-sm">{{ item.name }}</span>
            </p>
            <hr class="flex-1 border-dashed border-black mx-4">
            <el-tag :color="item.tagColor" style="color: #373737; border: transparent;"
                effect="light" round>
                {{ item.value }}
            </el-tag>
        </div>
    </main>
</template>

<script>
export default {
data() {
    return {
        scaleStatistics: [
            {
                name: 'Peso medio',
                value: '142.52',
                tagColor: '#E6EFFC',
            },
            {
                name: 'Desviación estándar',
                value: '16.05',
                tagColor: '#F7E7FD',
            },
            {
                name: 'Peso total de descarga',
                value: '65153.82',
                tagColor: '#FAFDE6',
            },
            {
                name: 'Total regalado',
                value: '1249.30',
                tagColor: '#FFF2DE',
            },
            {
                name: 'Porcentaje regalado',
                value: '1.82',
                tagColor: '#FFDEDE',
            },
        ],
    }
},
components:{
    
},
props:{

},
methods:{

}
}
</script>
