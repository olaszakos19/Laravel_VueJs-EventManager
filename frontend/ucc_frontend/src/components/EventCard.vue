<template>
  <div
    class="max-w-sm w-full event-card group relative p-5 rounded-xl border border-[#38ca44] bg-[#23313f] shadow-md hover:shadow-xl transition"
  >
    <h3 class="text-lg font-semibold mb-1 text-stone-100">
      {{ eventProp.title }}
    </h3>

    <p class="text-sm text-stone-300 mb-2">
      👤 Szervező:
      <span class="font-medium text-stone-200">
        {{ eventProp.creator?.name ?? "N/A" }}
      </span>
    </p>

    <div
      class="inline-block text-sm bg-stone-700 text-stone-200 px-3 py-1 rounded mb-3"
    >
      📅 {{ formattedDate }}
    </div>

    <p class="text-stone-300 mb-4">
      {{ eventProp.description ?? "Nincs leírás megadva." }}
    </p>

    <div
      v-if="useAuth.user?.id === eventProp.creator?.id"
      class="flex justify-center gap-3 mt-4 opacity-0 group-hover:opacity-100 transition"
    >

  <button
    class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
    @click.prevent="openDeleteModal(eventProp.id)"
  >
    Törlés
  </button>

  <DeleteModal
    v-if="showDeleteModal"
    @close="showDeleteModal = false"
    @confirm="confirmDelete"
  />
      <button
        @click="showModal = true"
        class="px-3 py-1 text-sm bg-emerald-600 text-white rounded hover:bg-emerald-700"
      >
        Módosítás
      </button>
    </div>

    <UpdateEventModal
      v-if="showModal"
      @close="showModal = false"
      :id="eventProp.id"
    />
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { Event } from "@/types/Event";
import UpdateEventModal from "./Modals/UpdateEventModal.vue";
import { ref, computed } from "vue";
import { useEventsStore } from "@/stores/events";
import DeleteModal from "./Modals/DeleteModal.vue";

const props = defineProps<{ eventProp: Event }>();

const showModal = ref(false);
const useAuth = useAuthStore();
const eventStore = useEventsStore();

const formattedDate = computed(() => {
  return new Date(props.eventProp.occurence).toLocaleString("hu-HU", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});


const showDeleteModal = ref(false);
const deleteEventId = ref<number | null>(null);

function openDeleteModal(id: number) {
  deleteEventId.value = id;
  showDeleteModal.value = true;
}

function confirmDelete() {
  if (deleteEventId.value !== null) {
    eventStore.deleteEvent(deleteEventId.value);
  }
  showDeleteModal.value = false;
  deleteEventId.value = null;
}
</script>
