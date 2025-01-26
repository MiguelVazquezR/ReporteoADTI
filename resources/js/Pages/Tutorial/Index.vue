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
                <div class="grid grid-cols-4 gap-x-8 mt-6">
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

import thumbnail1 from '@/../../public/images/metabase1.png';
import thumbnail2 from '@/../../public/images/metabase2.png';

export default {
    data() {
        return {
            videos: [
                { id: 1, title: 'Sección de configuraciones', duration: '10:00', thumbnail: thumbnail1, path: '/videos/video1.mp4' },
                { id: 2, title: 'Creación de graficas y dashboards', duration: '15:30', thumbnail: thumbnail2, path: '/videos/video2.mp4' },
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
