<template>
  <div class="min-h-screen flex items-center justify-center bg-[#2c3e50]">
    <div
      class="w-full max-w-md p-6 rounded-xl bg-stone-600 border border-stone-700 shadow-lg"
    >
      <h1 class="text-2xl font-semibold text-center text-stone-100 mb-6">
        Új jelszó kérése
      </h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm text-stone-300 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            placeholder="Add meg az emailedet amivel regisztráltál"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 border border-stone-700 text-stone-100 focus:outline-none focus:border-emerald-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-4 py-2 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
        >
          {{ loading ? "Feldolgozás..." : "Jelszó kérése" }}
        </button>

        <router-link
          to="/login"
          class="block text-center mt-4 text-sm text-stone-300 hover:text-emerald-400 transition"
        >
          Vissza a bejelentkezéshez
        </router-link>

        <p v-if="error" class="text-sm text-red-400 text-center mt-3">
          {{ error }}
        </p>

        <p v-if="success" class="text-sm text-emerald-400 text-center mt-3">
          {{ success }}
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { ref } from "vue";

const authStore = useAuthStore();

const email = ref("");
const error = ref<string | null>(null);
const success = ref<string | null>(null);
const loading = ref(false);

const submit = async () => {
  error.value = null;
  success.value = null;
  loading.value = true;

  try {
    await authStore.newPasswordRequest(email.value);
    success.value = "Siker! Kérjük, ellenőrizd az emailed a jelszó visszaállításhoz.";
  } catch (e: any) {
    if (e.response && e.response.status === 422) {
      const errors = e.response.data.errors;
      if (errors.email) {
        error.value = errors.email[0];
      } else {
        error.value = "Érvénytelen adat.";
      }
    } else {
      error.value = "Hiba történt, próbáld újra később.";
    }
  } finally {
    loading.value = false;
  }
};
</script>
