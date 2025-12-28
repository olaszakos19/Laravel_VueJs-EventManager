<script setup>
import Header from "@/Components/Header.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- NAVBAR -->
         <Header />

        <!-- CONTENT -->
        <div class="max-w-5xl mx-auto py-10 px-4">
            <h2 class="text-2xl font-semibold mb-6">Közelgő események</h2>
        </div>
    </div>
</template>
