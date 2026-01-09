import { User } from "./User"

export interface Event{
    id?: number,
    title: string,
    occurence: string,
    description?: string
    creator_id: number
}