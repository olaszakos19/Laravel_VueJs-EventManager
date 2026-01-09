<template>
  <div class="backdrop" @click.self="closeModal">
    <div class="modal">
      <h2>Új esemény létrehozása</h2>

      <form class="form" @submit.prevent="createEvent">
        <label>
          <span>Esemény neve</span>
          <input type="text" v-model="form.title" />
        </label>

        <label>
          <span>Esemény neve</span>
          <input type="datetime-local" v-model="form.occurence" />
        </label>

        <label>
          <span>Leírás (opcionális)</span>
          <input type="text" v-model="form.description" />
        </label>

        <button type="submit">Létrehozás</button>
      </form>
    </div>
  </div>
</template>


<script setup lang="ts">
import { useEventsStore } from "@/stores/events";
import { reactive } from "vue";
import { EventPayload } from "@/types/Payloads/EventPayload";
import { useAuthStore } from "@/stores/auth";

const emit = defineEmits(["close"]);

const eventStore = useEventsStore();
const authStore = useAuthStore();

const form = reactive<EventPayload>({
  title: "",
  occurence: "",
  description: "",
});

function createEvent() {
  eventStore.addEvent(form)
  emit('close')
}

function closeModal() {
  emit("close");
}
</script>


