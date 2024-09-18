<template>
    <section class="w-full min-h-screen">
        <pre>
            {{ data }}
        </pre>
    </section>
</template>

<script>
export default {
    data() {
        return {
            data: null,
            intervalId: null // Para almacenar el ID del intervalo
        }
    },
    methods: {
        async fetchMachineModbusRegisters() {
            try {
                const response = await axios.get(route('robag.get-modbus-registers'));

                if (response.status === 200) {
                    this.data = response.data.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        // Ejecutar fetchMachineModbusRegisters cada segundo
        // this.intervalId = setInterval(() => {
        //     this.fetchMachineModbusRegisters();
        // }, 1000); // 1000 ms = 1 segundo
    },
    beforeUnmount() {
        // Limpiar el intervalo cuando se destruya el componente
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
    }
}
</script>
