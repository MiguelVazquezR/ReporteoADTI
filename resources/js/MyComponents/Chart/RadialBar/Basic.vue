<template>
    <div id="chart">
        <apexchart type="radialBar" height="150" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>
<script>
export default {
    data() {
        return {
            // series: [70],
            chartOptions: {
                chart: {
                    height: 150,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '60%',
                        },
                        dataLabels: {
                            value: {
                                offsetY: -10,
                                color: '#FFF', // Cambia el color de la etiqueta del porcentaje
                                fontSize: '14px', // Puedes ajustar el tamaño si es necesario
                            },
                        },
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        shadeIntensity: 0.4,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 53, 91],
                        // Colores iniciales; se actualizarán dinámicamente
                        colorStops: []
                    }
                },
                labels: [''],
            },
        }
    },
    props: {
        series: {
            type: Array,
            default: 0
        }
        // chartOptions: Object,
    },
    computed: {
        gradientColors() {
            const value = this.series[0]; // Se asume que 'series' es un array con un solo valor
            if (value <= 30) {
                return [
                    { offset: 0, color: '#FF0000' }, // Rojo en la parte superior
                    { offset: 100, color: '#FF6347' } // Un rojo más claro en la parte inferior
                ];
            } else if (value > 30 && value <= 60) {
                return [
                    { offset: 0, color: '#FFA500' }, // Naranja en la parte superior
                    { offset: 100, color: '#FFD700' } // Amarillo en la parte inferior
                ];
            } else {
                return [
                    { offset: 0, color: '#008000' }, // Verde en la parte superior
                    { offset: 100, color: '#00FF00' } // Verde más claro en la parte inferior
                ];
            }
        }
    },
    watch: {
        // Actualiza los colores del gradiente cuando cambie el valor de 'series'
        gradientColors(newColors) {
            this.chartOptions.fill.gradient.colorStops = newColors;
        }
    },
    mounted() {
        // Asigna el gradiente inicial al montarse el componente
        this.chartOptions.fill.gradient.colorStops = this.gradientColors;
    }
}
</script>