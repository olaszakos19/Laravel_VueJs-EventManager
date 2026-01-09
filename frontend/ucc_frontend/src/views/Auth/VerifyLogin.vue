<script setup lang="ts">
import api from "@/api/axios";
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const code = ref("");
const error = ref<string | null>(null);

const submit = async () => {
  try {
    const { data } = await api.post("/login/verify", {
      email: route.query.email,
      code: code.value,
    });

    authStore.token = data.token;
    localStorage.setItem("token", data.token);
    authStore.user = data.user;

    router.push("/");
  } catch {
    error.value = "Hibás vagy lejárt kód";
  }
};
</script>
