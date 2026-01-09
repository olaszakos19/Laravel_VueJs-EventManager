<template>
  <nav class="bg-slate-800 text-white px-6 py-4 flex items-center justify-between shadow-md">

    <div class="flex items-center gap-6">
      <router-link
        to="/"
        class="font-semibold text-lg hover:text-sky-400 transition"
      >
        Fő oldal
      </router-link>

      <template v-if="authStore.isAuthenticated">
        <router-link
          to="/myevents"
          class="hover:text-sky-400 transition"
        >
          Eseményeim
        </router-link>

        <router-link
          to="/profile"
          class="hover:text-sky-400 transition"
        >
          Profilom
        </router-link>
      </template>
    </div>


    <div>
      <template v-if="authStore.isAuthenticated">
        <button
          @click="logout"
          class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md transition font-medium"
        >
          Kijelentkezés
        </button>
      </template>

      <template v-else>
        <router-link
          to="/login"
          class="bg-sky-500 hover:bg-sky-600 px-4 py-2 rounded-md transition font-medium"
        >
          Bejelentkezés
        </router-link>
      </template>
    </div>
  </nav>
</template>

<script setup>
import router from "@/router";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();

const logout = () => {
  authStore.logout();
  router.push("/login");
};
</script>

<style></style>
