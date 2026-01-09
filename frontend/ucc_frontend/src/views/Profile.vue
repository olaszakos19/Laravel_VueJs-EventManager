<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-4">Profilom</h1>

    <div v-if="authStore.user">

      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1" for="name">Név</label>
        <input
          id="name"
          type="text"
          v-model="form.name"
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400"
        >
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1" for="email">Email</label>
        <input
          id="email"
          type="email"
          readonly
          v-model="form.email"
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400"
        >
      </div>


      <h2 class="text-xl font-semibold mb-2 mt-6">Jelszó módosítás</h2>


      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1" for="newPassword">Új jelszó</label>
        <input
          id="newPassword"
          type="password"
          v-model="form.newPassword"
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400"
        >
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1" for="confirmPassword">Új jelszó megerősítése</label>
        <input
          id="password_confirm"
          type="password"
          v-model="form.password_confirmation"
          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-400"
        >
      </div>

      <div class="flex justify-end gap-2 mt-4">
        <button @click="updateProfile" class="px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600">
          Mentés
        </button>
      </div>
    </div>


  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { UserUpdatePayload } from '@/types/Payloads/UserUpdatePayload'


const authStore = useAuthStore()

const form = reactive<UserUpdatePayload>({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  newPassword: '',
  password_confirmation: ''
});

async function updateProfile() {
  if (!authStore.user) return

  try {
    // Backend hívás
    const updatedUser = await authStore.updateUser({
      name: form.name,
      email: form.email,
      newPassword: form.newPassword,
      password_confirmation: form.password_confirmation
    });

   
    form.name = updatedUser.name
    form.newPassword = ''
    form.password_confirmation = ''

    alert('Profil frissítve!')
  } catch (err) {
    console.error(err)
    alert('Hiba történt a frissítés során.')
  }
}

function resetForm() {
  form.name = authStore.user?.name || ''
  form.email = authStore.user?.email || ''
  form.newPassword = ''
  form.password_confirmation = ''
}
</script>
