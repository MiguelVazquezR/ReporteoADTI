<template>
    <div>
        <Head :title="title" />

        <div class="overflow-hidden h-screen bg-white">
            <!-- resto de pagina -->
            <main class="w-full">
                <nav class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-20 space-x-4 borde items-center">
                            <!-- Logo -->
                            <Link :href="'/'">
                                <ApplicationMark class="md:w-32 w-20" />
                            </Link>

                            <!-- Login y navegación -->
                            <!-- <div class="lg:flex items-center space-x-9 hidden">
                                <button :class="route().current('home') ? 'text-primary' : ''" @click="$inertia.get(route('home'))">Inicio</button>
                                <PrimaryButton @click="$inertia.visit(route('login'))">Iniciar sesión</PrimaryButton>
                            </div> -->
                        </div>
                    </div>
                </nav>

                <!-- footer -->
                <div class="overflow-y-auto h-[calc(100vh-5rem)] flex flex-col justify-between bg-white">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
// import emitter from '@/eventBus.js';

export default {
    data() {
        return {

        }
    },
    components: {
        ApplicationMark,
        PrimaryButton,
        Head,
        Link
    },
    props: {
        title: String,
    },
    methods: {
    },
}
</script>
