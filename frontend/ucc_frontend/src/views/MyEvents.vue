<template>
  <div class="home p-4">
       <h1 class="text-2xl font-bold mb-4 text-stone-100">
      Eseményeim
    </h1>


    <div v-if="eventStore.events.length === 0" class="text-gray-500">
      Még nincs esemény létrehozva
    </div>
    <div
      v-if="auth.isAuthenticated"
      class="flex justify-start mb-6"
    >
      <button
        @click="showModal = true"
        class="px-4 py-2 bg-emerald-600 text-white rounded-lg 
               hover:bg-emerald-700 transition"
      >
        Esemény hozzáadása
      </button>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 
             gap-3 py-6 justify-items-center">
      <EventCard
       v-for="event in eventStore.events.filter(e => e.creator_id === auth.user?.id)"
        :key="event.id"
        :eventProp="event"
      />
    </div>

    <CreateEventModal v-if="showModal" @close="showModal = false" />
  </div>
</template>

<script setup lang="ts">
import EventCard from "@/components/EventCard.vue";
import CreateEventModal from "@/components/Modals/CreateEventModal.vue";
import { useAuthStore } from "@/stores/auth";
import { useEventsStore } from "@/stores/events";
import { ref, onMounted } from "vue";

const showModal = ref(false);
const eventStore = useEventsStore();
const auth = useAuthStore();

onMounted(async () => {
  await eventStore.getEvents();
});
</script>
