<template>
  <div class="min-h-screen flex items-center justify-center bg-[#2c3e50]">
    <div
      class="w-full max-w-md p-6 rounded-xl bg-stone-600 border border-stone-700 shadow-lg"
    >
      <h1 class="text-2xl font-semibold text-center text-stone-100 mb-6">
        Bejelentkezés
      </h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm text-stone-300 mb-1"> Email </label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 border border-stone-700 text-stone-100 focus:outline-none focus:border-emerald-500"
          />
        </div>

        <div>
          <label class="block text-sm text-stone-300 mb-1"> Jelszó </label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 border border-stone-700 text-stone-100 focus:outline-none focus:border-emerald-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-4 py-2 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
        >
          {{ loading ? "Bejelentkezés..." : "Bejelentkezés" }}
        </button>

        <router-link
          to="/register"
          class="block text-center mt-4 text-sm text-stone-300 hover:text-emerald-400 transition"
        >
          Még nincs fiókod? Regisztráció
        </router-link>

        <p v-if="error" class="text-sm text-red-400 text-center mt-3">
          {{ error }}
        </p>

        <router-link
          to="/forgetpassword"
          class="block text-center mt-4 text-sm text-stone-300 hover:text-emerald-400 transition"
        >
          Elfelejtetted jelszavad?
        </router-link>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { LoginPayload } from "@/types/Payloads/AuthPayload";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();
const error = ref<string | null>(null);
const loading = ref(false);

const form = reactive<LoginPayload>({
  email: "",
  password: "",
});

const submit = async () => {
  error.value = null;
  loading.value = true;

  try {
    await authStore.login(form);
    if(authStore.user?.role === "agent"){
      router.push("/agent");
    }
    else{
       router.push("/");
    }
   
  } catch (e: any) {
    if (e.response && e.response.status === 422) {
      const errors = e.response.data.errors;
      if (errors.email) {
        error.value = errors.email[0];
      }
    } else {
      error.value = "Hiba történt, próbáld újra.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style></style>
