<template>
  <div>
    <div v-if="!authStore.user || authStore.user.role !== 'agent'">
      <AppHeader />
    </div>

    <div v-else>
      <HelpdeskAppHeader />
    </div>

    <router-view />
    <div v-if="authStore.user">
      <Chat />
    </div>
  </div>
</template>

<script setup>
import AppHeader from "./components/AppHeader.vue";
import Chat from "./components/ChatPanel.vue";
import HelpdeskAppHeader from "./components/HelpdeskAppHeader.vue";
import { useAuthStore } from "./stores/auth";

const authStore = useAuthStore();
authStore.fetchUser();
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  background-color: #2c3e50;
  min-height: 100vh;
}

nav a.router-link-exact-active {
  color: #42b983;
}

.backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);

  display: flex;
  align-items: center;
  justify-content: center;

  z-index: 9999;
}

.modal {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  padding: 2rem;
  width: 90%;
  max-width: 500px;
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.modal h2 {
  margin-bottom: 16px;
  font-size: 1.4rem;
  text-align: center;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.form button {
  padding: 0.7rem 1.2rem;
  border: none;
  border-radius: 8px;
  background-color: #059669;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.1s ease;
}

.form button:hover {
  background-color: #047854;
  transform: translateY(-1px);
}

label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 0.9rem;
}

input {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1rem;
}

input:focus {
  outline: none;

  border: 1px solid #38ca44;
}
</style>
