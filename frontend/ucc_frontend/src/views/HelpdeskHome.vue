<template>
  <div class="home p-4">
    <main class="flex-1 p-6">
      <h1 class="text-2xl font-semibold mb-4 text-white">
        Üdvözöllek a Helpdesk felületen
      </h1>

      <div class="flex flex-col sm:flex-row items-center gap-3 mb-5">
        <label class="text-gray-100 font-medium whitespace-nowrap">
          Keresés:
        </label>

        <input
          type="text"
          placeholder="Keresett beszélgetés neve..."
          v-model="searchParam"
          class="px-3 py-2 rounded-md border border-gray-300 focus:outline-none bg-gray-50 text-gray-900 w-full sm:w-64"
        />
      </div>

      <p class="mb-6">
        Itt találhatók az aktuális jegyek, ügynökök és riportok.
      </p>


      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <ChatCard
          v-for="chat in filteredChats"
          :key="chat.id"
          :chat="chat"
          @select="loadChat"
        />
      </div>

      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useChatStore } from "@/stores/chat";
import ChatCard from "@/components/ChatCard.vue";

const searchParam = ref("");
const chatStore = useChatStore();


onMounted(async () => {
  await chatStore.getChats();
  console.log(chatStore.chats);
});


const filteredChats = computed(() => {
  if (!searchParam.value.trim()) return chatStore.chats;
  return chatStore.chats.filter(chat =>
    chat.title.toLowerCase().includes(searchParam.value.toLowerCase())
  );
});

// Chat kiválasztás és üzenetek betöltése
function loadChat(chatId: number) {
  chatStore.loadChatMessages(chatId);
  chatStore.openChat();
}
</script>
