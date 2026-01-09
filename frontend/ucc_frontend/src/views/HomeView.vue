<template>
  <div class="home p-4">
    <h1 class="text-2xl font-bold mb-4 text-stone-100">Események</h1>

    <div class="flex flex-col sm:flex-row items-center gap-3 mb-5" v-if="events.length !== 0">
      <label class="text-gray-100 font-medium whitespace-nowrap">
        Keresés:
      </label>



      <input
        type="text"
        placeholder="Esemény neve..."
        v-model="searchParam"
        
        class="px-3 py-2 rounded-md border border-gray-300 focus:outline-none bg-gray-50 text-gray-900 w-full sm:w-64"
      />
    </div>

    <div v-if="events.length === 0" class="text-stone-400">
      Még nincs esemény létrehozva
    </div>

    <div v-if="auth.isAuthenticated" class="flex justify-start mb-6">
      <button
        @click="showModal = true"
        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition"
      >
        Esemény hozzáadása
      </button>
    </div>

    <div
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 py-6 justify-items-center"
    >
      <EventCard
        v-for="event in events"
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
import { ref, onMounted, watch } from "vue";
import { Event } from "@/types/Event";
import { useRouter } from "vue-router";


const showModal = ref(false);
const eventStore = useEventsStore();
const auth = useAuthStore();

const searchParam = ref("");

const events = ref<Event[]>([]);

const router = useRouter();

function searchEvent(title: string): Event[] {
  return events.value.filter((e) =>
    e.title.toLowerCase().includes(title.toLowerCase())
  );
}

watch(searchParam, (newVal) => {
  events.value = searchEvent(newVal);
  if(newVal === '') events.value = eventStore.events;
});
onMounted(async () => {

  if(auth.user?.role === "agent"){
    router.push("/agent");
  }

  await eventStore.getEvents();
  events.value = eventStore.events;
});
</script>
