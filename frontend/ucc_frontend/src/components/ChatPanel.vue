<template>
  <div class="fixed bottom-4 right-4 flex flex-col items-end z-50">

    <button
      v-if="!chatStore.isOpen"
      @click="openChat"
      class="bg-blue-500 w-12 h-12 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-blue-600"
    >
      💬
    </button>

    <div
      v-else
      class="flex flex-col bg-white border rounded-lg shadow-lg w-80 h-96"
    >

      <div class="bg-blue-500 text-white px-4 py-2 flex justify-between items-center rounded-t-lg">
        <span>Chat</span>
        <button
          @click="chatStore.isOpen = false"
          class="text-white font-bold"
        >
          ×
        </button>
      </div>


      <div
        ref="messagesContainer"
        class="flex-1 p-4 overflow-y-auto"
      >
        <div
          v-for="(msg, index) in chatStore.messages"
          :key="msg.id ?? index"
          :class="msg.from === 'user' ? 'text-right' : 'text-left'"
          class="mb-2"
        >
          <span
            :class="[
              'inline-block px-3 py-1 rounded-lg',
              msg.from === 'user'
                ? 'bg-blue-500 text-white'
                : 'bg-gray-200 text-gray-800'
            ]"
          >
            {{ msg.text }}
          </span>
        </div>
      </div>

      <!-- Input -->
      <div class="border-t px-4 py-2 flex">
        <input
          v-model="inputMessage"
          @keyup.enter="sendMessage"
          type="text"
          placeholder="Írj üzenetet..."
          class="flex-1 border rounded px-2 py-1 focus:outline-none focus:ring focus:border-blue-300"
        />
        <button
          @click="sendMessage"
          :disabled="chatStore.loading"
          class="ml-2 bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600 disabled:opacity-50"
        >
          Küldés
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import { useChatStore } from '@/stores/chat'

const chatStore = useChatStore()
const inputMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)


const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop =
      messagesContainer.value.scrollHeight
  }
}

watch(
  () => chatStore.messages.length,
  () => scrollToBottom()
)

const openChat = () => {
  chatStore.openChat()
}

const sendMessage = async () => {
  if (!inputMessage.value.trim()) return

  await chatStore.sendMessage({
    message: inputMessage.value,
  })

  inputMessage.value = ''
}
</script>
