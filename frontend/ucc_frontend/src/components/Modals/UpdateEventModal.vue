<template>
  <div class="backdrop" @click.self="closeModal">
    <div class="modal">
     <h2>Esemény módosítása</h2>
        <form class="form" @submit.prevent="updateEvent">

          <label>
                      <span>Esemény neve</span>
          <input
            type="text"
            v-model="form.title"
            placeholder="Esemény neve"
            readonly
          />
          </label>

          <label>
            <span>Esemény időpontja</span> 
            <input type="datetime-local" v-model="form.occurence" readonly />
          </label>


          <label>
            <span>Esemény leírása</span> 
                     
          <input
            type="text"
            placeholder="Esemény leírása (opcionális)"
            v-model="form.description"
          />
          </label>


          <button>Mentés</button>
        </form>
     
    </div>
  </div>
</template>

<script setup lang="ts">
import { useEventsStore } from "@/stores/events";
import { onMounted, reactive } from "vue";
import { EventPayload } from "@/types/Payloads/EventPayload";

const emit = defineEmits(["close"]);

const props = defineProps<{ id: number }>();

const eventStore = useEventsStore();

const form = reactive<EventPayload>({
  title: "",
  occurence: "",
  description: "",
});

onMounted(async () => {
  const event = eventStore.events.find((e) => e.id === props.id);
  console.log(event);
  form.title = event.title;
  form.occurence = event.occurence;
  form.description = event.description || "";
});

function updateEvent() {
  eventStore.updateEvent(props.id,form.description ?? '');
  emit("close");
}

function closeModal() {
  emit("close");
}
</script>
