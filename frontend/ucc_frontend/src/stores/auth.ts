import { Auth } from "@/types/Auth";
import { LoginPayload, RegisterPayload } from "@/types/Payloads/AuthPayload";
import { User } from "@/types/User";
import api from "@/api/axios";
import { defineStore } from "pinia";
import { UserUpdatePayload } from "@/types/Payloads/UserUpdatePayload";

export const useAuthStore = defineStore("auth", {
  state: (): Auth => ({
    user: null,
    token: localStorage.getItem("token"),
  }),
  getters: {
    isAuthenticated: (state): boolean => !!state.token,
  },

  actions: {
    async login(payload: LoginPayload) {
      const { data } = await api.post<{ token: string }>("/login", payload);

      this.token = data.token;
      localStorage.setItem("token", data.token);

      await this.fetchUser();
    },
    async register(payload: RegisterPayload) {
      await api.post("/register", payload);
    },
    async fetchUser() {
      if (!this.token) return;

      try {
        const { data } = await api.get<User>("/user");
        this.user = data;
      } catch (error) {
        this.logout();
      }
    },
    async updateUser(payload: UserUpdatePayload) {
      if (!this.token) return;
      try {
        const { data } = await api.put("/user", payload);
        return data;
      } catch (error) {
        console.log(error);
      }
    },
    async newPasswordRequest(email: string) {
      await api.post("/password/email", { email });
    },
    logout() {
      (this.user = null), (this.token = null), localStorage.removeItem("token");
    },
  },
});
