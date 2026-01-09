import { defineStore } from 'pinia'
import api from '@/api/axios'
import { Chat, ChatMessage } from '@/types/Chat'
import { ChatPayload } from '@/types/Payloads/ChatPayload'

export const useChatStore = defineStore('chat', {
  state: () => ({
    isOpen: false as boolean,
    loading: false as boolean,

    chats: [] as Chat[],            
    activeChatId: null as number | null,

    messages: [] as ChatMessage[],
  }),

  actions: {

    openChat() {
      this.isOpen = true

      if (this.messages.length === 0) {
        this.messages.push({
          from: 'bot',
          text: 'Üdvözöllek! Ha kérdésed van, írj nyugodtan 😊',
        })
      }
    },

  
    async sendMessage(payload: ChatPayload) {
      if (!payload.message.trim()) return

      this.loading = true

      // user message
      this.messages.push({
        from: 'user',
        text: payload.message,
      })

      try {
        const response = await api.post(
          '/chat',
          payload,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          }
        )

        this.messages.push({
          from: 'bot',
          text: response.data.message,
        })

        // chat lezárás kezelése
        if (response.data.closed) {
          this.activeChatId = null
        }

      } catch (e: any) {
        if (e.response?.status === 401) {
          this.messages.push({
            from: 'bot',
            text: 'Előbb be kell jelentkezned.',
          })
        } else {
          this.messages.push({
            from: 'bot',
            text: 'Hiba történt a szerverrel való kommunikáció során.',
          })
        }
      } finally {
        this.loading = false
      }
    },


    async getChats() {
      this.loading = true

      try {
        const response = await api.get('/chat/history', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        this.chats = response.data
      } finally {
        this.loading = false
      }
    },

   
    async loadChatMessages(chatId: number) {
      this.loading = true
      this.activeChatId = chatId

      try {
        const response = await api.get(`/chat/${chatId}/messages`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })

        this.messages = response.data.map((m: any) => ({
          id: m.id,
          from: m.sender_type,
          text: m.message,
          created_at: m.created_at,
        }))
      } finally {
        this.loading = false
      }
    },

   
    reset() {
      this.isOpen = false
      this.messages = []
      this.chats = []
      this.activeChatId = null
    },
  },
})
