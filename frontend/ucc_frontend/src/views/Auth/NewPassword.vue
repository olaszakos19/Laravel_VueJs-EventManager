<template>
  <div class="min-h-screen flex items-center justify-center bg-[#2c3e50]">
    <div class="w-full max-w-md p-6 rounded-xl bg-stone-600 border border-stone-700 shadow-lg">
      <h1 class="text-2xl font-semibold text-center text-stone-100 mb-6">
        Új jelszó beállítása
      </h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm text-stone-300 mb-1">Új jelszó</label>
          <input
            type="password"
            v-model="password"
            placeholder="Új jelszó"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 border border-stone-700 text-stone-100 focus:outline-none focus:border-emerald-500"
          />
        </div>

        <div>
          <label class="block text-sm text-stone-300 mb-1">Jelszó megerősítése</label>
          <input
            type="password"
            v-model="password_confirmation"
            placeholder="Jelszó megerősítése"
            required
            class="w-full px-3 py-2 rounded-lg bg-stone-900 border border-stone-700 text-stone-100 focus:outline-none focus:border-emerald-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-4 py-2 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
        >
          {{ loading ? "Feldolgozás..." : "Jelszó reset" }}
        </button>

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
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/api/axios';

const route = useRoute();
const router = useRouter();

const token = ref<string>('');
const email = ref<string>('');

const password = ref<string>('');
const password_confirmation = ref<string>('');

const loading = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);

// Amikor a komponens betöltődik, olvassa ki a query parametereket
onMounted(() => {
  token.value = route.query.token as string || '';
  email.value = route.query.email as string || '';
  if (!token.value || !email.value) {
    error.value = "Érvénytelen link.";
  }
});

const submit = async () => {
  error.value = null;
  success.value = null;
  if (password.value !== password_confirmation.value) {
    error.value = "A jelszavak nem egyeznek.";
    return;
  }

  loading.value = true;
  try {
    await api.post('/password/reset', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
    success.value = "Jelszó sikeresen módosítva!";
    setTimeout(() => router.push('/login'), 2000);
  } catch (e: any) {
    if (e.response && e.response.data?.message) {
      error.value = e.response.data.message;
    } else {
      error.value = "Hiba történt, próbáld újra.";
    }
  } finally {
    loading.value = false;
  }
};
</script>
