import { defineStore } from "pinia";
import api from "@/api/axios";
import { EventPayload } from "@/types/Payloads/EventPayload";
import { useAuthStore } from "./auth";
import { Event } from "@/types/Event";

export const useEventsStore = defineStore("events", {
  state: () => ({
    events: [] as Event[],
    loading: false,
  }),
  getters: {
    myEvents: (state) => (userId: number) =>
      state.events.filter((e) => e.creator_id === userId),
  },
  actions: {
    async getEvents() {
      try {
        this.loading = true;
        const { data } = await api.get("/events");
        this.events = data;
      } catch (error) {
      } finally {
        this.loading = false;
      }
    },
    async addEvent(payload: EventPayload) {
      const auth = useAuthStore();

      try {
        const { data } = await api.post("/event", payload, {
          headers: {
            Authorization: `Bearer ${auth.token}`,
          },
        });

        
        this.events.push(data.event);
        
        alert("Esemény létrehozva!");

      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async deleteEvent(id: number) {
      const auth = useAuthStore();
      try {
        this.events = this.events.filter((e) => {
          return e.id != id;
        });
        await api.delete(`/event/${id}`, {
          headers: { Authorization: `Bearer ${auth.token}` },
        });
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async updateEvent(id: number, description: string) {
      const auth = useAuthStore();

      try {
        const { data } = await api.put(
          `/event/${id}`,
          {
            description: description,
          },
          {
            headers: {
              Authorization: `Bearer ${auth.token}`,
              "Content-Type": "application/json",
              Accept: "application/json",
            },
          }
        );

        const event = this.events.find((e) => e.id === id);
        event!.description = description;
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
  },
});
