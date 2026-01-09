<template>
  <div
    class="chat-card p-4 rounded-lg shadow-md border hover:shadow-lg transition cursor-pointer"
    :class="statusClass"
    @click="$emit('select', chat.id)"
  >
    <div class="flex justify-between items-start mb-2">
      <h2 class="font-semibold text-lg">{{ chatTitle }} Kezdeményező:{{ chat.user.name }}</h2>
      <span
        class="text-xs font-medium px-2 py-1 rounded"
        :class="chat.status === 'closed' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'"
      >
        {{ chat.status === 'closed' ? 'Lezárt' : 'Folyamatban' }}
      </span>
    </div>

    <p v-if="chat.last_message" class="text-sm text-gray-700 mb-1">
      <span class="font-medium">{{ lastMessageSender }}:</span> {{ chat.last_message.text }}
    </p>
    <p v-if="chat.last_message" class="text-xs text-gray-500">{{ lastMessageTime }}</p>
    
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Chat } from "@/types/Chat";

const props = defineProps<{ chat: Chat }>();

const statusClass = computed(() => {
  return props.chat.status === 'closed' ? 'opacity-60' : 'opacity-100';
});

const lastMessageSender = computed(() => {
  if (!props.chat.last_message) return '';
  const from = props.chat.last_message.from;
  if (from === 'user') return 'Felhasználó';
  if (from === 'bot') return 'Bot';
  return 'Ügynök';
});

const lastMessageTime = computed(() => {
  if (!props.chat.last_message?.created_at) return '';
  return new Date(props.chat.last_message.created_at).toLocaleString();
});

const chatTitle = computed(() => {

  return `Chat #${props.chat.id}`;
});
</script>

<style scoped>
.chat-card {
  background-color: white;
}
</style>
