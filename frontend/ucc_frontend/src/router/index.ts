import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import HomeView from "../views/HomeView.vue";
import Login from "@/views/Auth/Login.vue";
import NotFound from "@/views/NotFound.vue";

import { useAuthStore } from "@/stores/auth";
import type { NavigationGuardNext, RouteLocationNormalized } from "vue-router";
import Profile from "../views/Profile.vue";
import MyEvents from "@/views/MyEvents.vue";
import Register from "@/views/Auth/Register.vue";
import ForgetPassword from "@/views/Auth/ForgetPassword.vue";
import NewPassword from "@/views/Auth/NewPassword.vue";
import HelpdeskHome from "@/views/HelpdeskHome.vue";

const routes: Array<RouteRecordRaw> = [
   {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/agent",
    name: "agent",
    meta: { requiresAuth: true, role: "agent" },
    component: HelpdeskHome,
  },
  {
    path: "/about",
    name: "about",
    meta: { requiresAuth: true },

    component: () =>
      import(/* webpackChunkName: "about" */ "../views/Profile.vue"),
  },
  {
    path: "/login",
    name: "login",

    component: Login,
  },
  {
    path: "/forgetpassword",
    name: "forgetpassword",

    component: ForgetPassword,
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: NewPassword,
  },

  {
    path: "/register",
    name: "register",

    component: Register,
  },
  {
    path: "/profile",
    name: "profile",
    meta: { requiresAuth: true },
    component: Profile,
  },
  {
    path: "/myevents",
    name: "my_events",
    meta: { requiresAuth: true },
    component: MyEvents,
  },
  {
    path: "/:catchAll(.*)",
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach((to: RouteLocationNormalized) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return "/login";
  }
});

export default router;
