<template>
  <div>
    <Head title="Welcome" />

    <main class="container mx-auto px-4 pt-16 pb-20 text-center">
      <h1 class="mb-6 text-5xl font-bold text-white">
        Meal Planning,<br />
        <span class="text-emerald-400">Simplified for Families</span>
      </h1>
      <p class="mx-auto mb-10 max-w-2xl text-xl text-gray-300">
        Plan meals, generate shopping lists, and stay organized with your family. Share recipes and collaborate with
        family members in real-time.
      </p>
      <div class="flex justify-center gap-4">
        <Button label="Get Started - Free Now - Free Forever" size="large" @click="openRegister = true" />
      </div>
    </main>

    <section class="container mx-auto px-4 py-16">
      <div class="grid gap-8 md:grid-cols-3">
        <div class="rounded-xl bg-gray-800/50 p-6 text-center">
          <div class="mb-4 flex justify-center">
            <ClipboardDocumentListIcon class="h-12 w-12 text-emerald-400" />
          </div>
          <h3 class="mb-2 text-xl font-semibold text-white">Smart Shopping Lists</h3>
          <p class="text-gray-400">
            Automatically generate shopping lists from your meal plans, organized by store aisle.
          </p>
        </div>
        <div class="rounded-xl bg-gray-800/50 p-6 text-center">
          <div class="mb-4 flex justify-center">
            <UserGroupIcon class="h-12 w-12 text-emerald-400" />
          </div>
          <h3 class="mb-2 text-xl font-semibold text-white">Family Sharing</h3>
          <p class="text-gray-400">Invite family members to view and co-manage recipes and meal plans together.</p>
        </div>
        <div class="rounded-xl bg-gray-800/50 p-6 text-center">
          <div class="mb-4 flex justify-center">
            <BookOpenIcon class="h-12 w-12 text-emerald-400" />
          </div>
          <h3 class="mb-2 text-xl font-semibold text-white">Recipe Collection</h3>
          <p class="text-gray-400">Store and organize all your favorite recipes in one place with easy access.</p>
        </div>
      </div>
    </section>

    <section class="bg-gray-800/30 py-16">
      <div class="container mx-auto px-4 text-center">
        <h2 class="mb-4 text-3xl font-bold text-white">Ready to get organized?</h2>
        <p class="mb-8 text-gray-300">Join now and streamline your meal planning.</p>
        <Button label="Create Free Account" size="large" @click="openRegister = true" />
      </div>
    </section>

    <footer class="py-8 text-center text-gray-500">
      <p>&copy; 2026 Sloppy Seconds. All rights reserved.</p>
    </footer>

    <Register v-model="openRegister" />
  </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import { ClipboardDocumentListIcon, UserGroupIcon, BookOpenIcon } from "@heroicons/vue/24/outline";
import Button from "primevue/button";
import Login from "../components/Login.vue";
import Register from "../components/Register.vue";

export default {
  components: {
    Head,
    Button,
    ClipboardDocumentListIcon,
    UserGroupIcon,
    BookOpenIcon,
    Login,
    Register,
  },
  data() {
    return {
      openRegister: false,
    };
  },
};
</script>

<style scoped>
</style>
