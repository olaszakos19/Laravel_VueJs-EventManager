<template>
  <div class="min-h-screen flex items-center justify-center bg-[#2c3e50]">
    <div
      class="w-full max-w-md p-6 rounded-xl bg-stone-700 
             border border-stone-700 shadow-lg"
    >
      <h1 class="text-2xl font-semibold text-center text-stone-100 mb-6">
        Regisztráció
      </h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm text-stone-300 mb-1">Név</label>
          <input
            type="text"
            v-model="form.name"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 
                   border border-stone-700 text-stone-100
                   focus:outline-none focus:border-emerald-500"
          />
        </div>

        <div>
          <label class="block text-sm text-stone-300 mb-1">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 
                   border border-stone-700 text-stone-100
                   focus:outline-none focus:border-emerald-500"
          />
        </div>

        <div>
          <label class="block text-sm text-stone-300 mb-1">Jelszó</label>
          <input
            type="password"
            v-model="form.password"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 
                   border border-stone-700 text-stone-100
                   focus:outline-none focus:border-emerald-500"
          />
        </div>

        <div>
          <label class="block text-sm text-stone-300 mb-1">
            Jelszó megerősítése
          </label>
          <input
            type="password"
            v-model="form.password_confirmation"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 
                   border border-stone-700 text-stone-100
                   focus:outline-none focus:border-emerald-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-4 py-2 rounded-lg text-white
                 bg-emerald-600 hover:bg-emerald-700 
                 disabled:opacity-50 disabled:cursor-not-allowed
                 transition"
        >
          {{ loading ? "Regisztráció..." : "Regisztráció" }}
        </button>

<p v-if="errors.general" class="text-red-500 mt-2">{{ errors.general[0] }}</p>

      </form>
    </div>
  </div>
</template>


<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { RegisterPayload } from "@/types/Payloads/AuthPayload";
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const error = ref<string | null>(null);
const loading = ref(false);

const form = ref<RegisterPayload>({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});



const errors = ref<{ [key: string]: string[] }>({});

const submit = async () => {
  loading.value = true;
  errors.value = {}; // reseteljük az előző hibákat

  try {
    await authStore.register(form.value);
    alert("Sikeres regisztráció");
    router.push("/login");
  } catch (e: any) {
    if (e.response && e.response.status === 422) {

      errors.value = e.response.data.errors;
    } else {

      errors.value = { general: ["Sikertelen regisztráció, próbáld újra."] };
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>

.field{
  margin-top: 1%;
}
</style>
