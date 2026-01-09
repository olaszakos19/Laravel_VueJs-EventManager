<template>
  <header class="bg-slate-800 text-white px-6 py-4 flex items-center justify-between shadow-md">
   
    <div class="flex items-center gap-6">
      <router-link to="/agent" class="font-bold text-xl hover:text-sky-400 transition">
        Helpdesk
      </router-link>
    </div>

    
    <div class="flex items-center gap-4">


      
      <div v-if="authStore.isAuthenticated" class="flex items-center gap-2">
        <span>{{ authStore.user?.name ?? 'N/A' }}</span>
        <button @click="logout" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md transition">
          Kijelentkezés
        </button>
      </div>

      <router-link v-else to="/login" class="bg-sky-500 hover:bg-sky-600 px-3 py-1 rounded-md transition">
        Bejelentkezés
      </router-link>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();


const router = useRouter();

function logout() {
  authStore.logout();
  router.push('/');
}
</script>


