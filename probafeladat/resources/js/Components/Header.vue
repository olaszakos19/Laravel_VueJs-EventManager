<script setup>
import { Link, usePage } from "@inertiajs/vue3";

// usePage hook a Inertia oldal adataihoz
const { props } = usePage();
const auth = props.value.auth;
</script>

<template>
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Mini Event App</h1>

        <div class="space-x-4" v-if="!auth?.user">
            <Link :href="route('login')" class="text-blue-600 hover:underline">Bejelentkezés</Link>
            <Link :href="route('register')" class="text-blue-600 hover:underline">Regisztráció</Link>
        </div>

        <div v-else>
            <span class="mr-4">Szia, {{ auth.user.name }}!</span>
            <Link :href="route('logout')" method="post" as="button" class="text-red-600 hover:underline">
                Kilépés
            </Link>
        </div>
    </nav>
</template>
