<template>
    <PublicLayout title="Tutoriales">
        <main class="lg:py-10 lg:px-14">
            <section class="flex items-center justify-between mx-14">
                <Link :href="route('home')">
                <button class="bg-grayED text-secondary rounded-full size-6 text-xs flex items-center justify-center">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                </Link>
                <h1 class="text-2xl font-bold">Tutoriales</h1>
                <!-- <PrimaryButton @click="$inertia.visit(route('tutorials.create'))">Crear nuevo</PrimaryButton> -->
            </section>
            <section class="mx-16">
                <h1 class="font-bold text-4xl text-center mt-6 text-primary">Metabase</h1>
                <div class="grid grid-cols-4 gap-x-8 gap-y-3 mt-6">
                    <div v-for="video in videos" :key="video.id"
                        class="bg-white shadow-md rounded-lg overflow-hidden cursor-pointer hover:scale-105 transition duration-300"
                        @click="openVideo(video.path)">
                        <img :src="video.thumbnail" alt="Video Thumbnail" draggable="false" class="w-full h-48 object-cover select-none">
                        <div class="p-4">
                            <h2 class="font-bold text-lg">{{ video.title }}</h2>
                            <p class="text-gray-600 text-xs flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>{{ video.duration }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

import thumbnail1 from '@/../../public/images/thn1.png';
import thumbnail2 from '@/../../public/images/thn2.png';
import thumbnail3 from '@/../../public/images/thn3.png';
import thumbnail4 from '@/../../public/images/thn4.png';
import thumbnail5 from '@/../../public/images/thn5.png';

export default {
    data() {
        return {
            videos: [
                { id: 1, title: 'Conexión de base de datos', duration: '00:49', thumbnail: thumbnail1, path: '/videos/Conexión de base de datos.mp4' },
                { id: 2, title: 'Creación de preguntas', duration: '03:16', thumbnail: thumbnail2, path: '/videos/Creación de preguntas.mp4' },
                { id: 3, title: 'Creación de pregunta con sql', duration: '02:06', thumbnail: thumbnail3, path: '/videos/Creación de pregunta con sql.mp4' },
                { id: 4, title: 'Crear dashboard', duration: '03:24', thumbnail: thumbnail4, path: '/videos/Crear dashboard.mp4' },
                { id: 5, title: 'Envío de reportes por correo', duration: '01:21', thumbnail: thumbnail5, path: '/videos/Envío de reportes por correo.mp4' },
            ]
        }
    },
    components: {
        PublicLayout,
        PrimaryButton,
        Link,
    },
    methods: {
        openVideo(path) {
            window.open(path, '_blank');
        }
    },
}
</script>
