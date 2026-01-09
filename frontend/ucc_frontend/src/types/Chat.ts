import { User } from "./User"

export interface ChatMessage {
  id?: number
  from: 'user' | 'bot' | 'agent'
  text: string
  created_at?: string
}

export interface Chat {
  id: number
  type: 'bot' | 'agent'
  status: 'open' | 'closed'          
  created_at: string
  user: User
  last_message?: ChatMessage         
}
