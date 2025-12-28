<template>
    <div class="max-w-6xl mx-auto py-12 px-6">
        <!-- NAVBAR -->
        <div class="flex justify-end mb-8 space-x-4">
            <!-- Ha nincs belépve -->
            <template v-if="!$page.props.auth || !$page.props.auth.user">
                <Link
                    href="/login"
                    class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow transition duration-300"
                >
                    Bejelentkezés
                </Link>

                <Link
                    href="/register"
                    class="px-5 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg shadow transition duration-300"
                >
                    Regisztráció
                </Link>
            </template>

            <!-- Ha be van lépve -->
            <template v-else>
                <form @submit.prevent="logout">
                    <button
                        type="submit"
                        class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg shadow transition duration-300"
                    >
                        Kilépés
                    </button>
                </form>
            </template>
        </div>

        <!-- Cím + Add Event gomb -->
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-800">Események</h1>

            <!-- Hozzáadás gomb, csak ha be van lépve -->
            <template v-if="$page.props.auth && $page.props.auth.user">
                <Link
                    href="/events/create"
                    class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-lg transition transform hover:-translate-y-1 hover:scale-105"
                >
                    + Új esemény
                </Link>
            </template>
        </div>

        <!-- Event grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <EventCard
                v-for="event in events.data"
                :key="event.id"
                :event="event"
                :auth="$page.props.auth"
            />
        </div>
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue3";
import EventCard from "@/Components/EventCard.vue";

export default {
    name: "EventsIndex",
    components: { EventCard, Link },
    props: {
        events: Object,
    },
    methods: {
        logout() {
            router.post("/logout");
        },
    },
};
</script>
