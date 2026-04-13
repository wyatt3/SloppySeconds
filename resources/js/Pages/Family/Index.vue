<template>
  <Head title="Family" />
  <div>
    <h1 class="mb-3 text-5xl text-white">Family</h1>

    <!-- Not in a family -->
    <div v-if="!inFamily" class="space-y-6">
      <div class="text-gray-300">You are not currently part of a family.</div>

      <div class="space-y-4">
        <div>
          <Button @click="createFamily" label="Create Family" :loading="loading" />
        </div>

        <div class="border-t my-6 border-gray-700"></div>

        <div class="text-lg font-semibold text-white">Or join an existing family</div>
        <div class="flex gap-2">
          <InputText v-model="joinCode" placeholder="Enter join code" class="w-48 dark-input" />
          <Button @click="joinFamily" label="Join Family" :loading="loading" :disabled="!joinCode" />
        </div>
      </div>
    </div>

    <!-- In a family -->
    <div v-else class="space-y-6">
      <div class="bg-gray-800 border border-gray-700 rounded p-4 text-white">
        <div class="text-lg font-semibold mb-2">Invite Code</div>
        <div class="flex items-center gap-2">
          <code class="text-2xl font-mono bg-gray-700 px-3 py-2 rounded border">{{ family.join_code }}</code>
          <Button @click="copyJoinCode" icon="pi pi-copy" text size="small" />
        </div>
        <div v-if="copied" class="text-green-600 text-sm mt-1">Copied!</div>
      </div>

      <div class="text-white">
        <div class="text-lg font-semibold mb-2">Members</div>
        <div class="border rounded">
          <div
            v-for="member in family.members"
            :key="member.id"
            class="p-3 border-b last:border-b-0 flex justify-between items-center"
          >
            <div>
              <div class="font-medium">{{ member.name }}</div>
              <div class="text-sm text-gray-500">{{ member.email }}</div>
            </div>
            <div v-if="member.id === currentUserId" class="text-sm text-gray-500">
              (you)
              <Button class="ml-2" @click="leaveFamily" label="Leave Family" severity="danger" :loading="loading" />
            </div>
          </div>
        </div>
      </div>

      <div></div>
    </div>
  </div>
</template>

<script>
import Button from "primevue/button";
import { Head } from "@inertiajs/vue3";
import InputText from "primevue/inputtext";

export default {
  components: { Button, Head, InputText },
  data() {
    return {
      inFamily: false,
      family: null,
      joinCode: "",
      loading: false,
      copied: false,
      currentUserId: null,
    };
  },
  methods: {
    async fetchFamily() {
      this.loading = true;
      try {
        const response = await axios.get(this.route("api.family.index"));
        this.inFamily = response.data.in_family;
        this.family = response.data.family;
        if (this.$page.props.auth.user) {
          this.currentUserId = this.$page.props.auth.user.id;
        }
      } catch (error) {
        console.error("Error fetching family:", error);
      } finally {
        this.loading = false;
      }
    },
    async createFamily() {
      this.loading = true;
      try {
        const response = await axios.post(this.route("api.family.create"));
        this.inFamily = true;
        this.family = response.data.family;
      } catch (error) {
        console.error("Error creating family:", error);
      } finally {
        this.loading = false;
      }
    },
    async joinFamily() {
      this.loading = true;
      try {
        const response = await axios.post(this.route("api.family.join"), {
          join_code: this.joinCode,
        });
        this.inFamily = true;
        this.family = response.data.family;
        this.joinCode = "";
      } catch (error) {
        console.error("Error joining family:", error);
        if (error.response?.data?.error) {
          alert(error.response.data.error);
        }
      } finally {
        this.loading = false;
      }
    },
    async leaveFamily() {
      if (!confirm("Are you sure you want to leave this family?")) {
        return;
      }
      this.loading = true;
      try {
        await axios.delete(this.route("api.family.leave"));
        this.inFamily = false;
        this.family = null;
      } catch (error) {
        console.error("Error leaving family:", error);
      } finally {
        this.loading = false;
      }
    },
    async copyJoinCode() {
      if (this.family?.join_code) {
        await navigator.clipboard.writeText(this.family.join_code);
        this.copied = true;
        setTimeout(() => {
          this.copied = false;
        }, 2000);
      }
    },
  },
  created() {
    this.fetchFamily();
  },
};
</script>

<style scoped>
:deep(.dark-input .p-inputtext) {
  background-color: #1f2937;
  border-color: #4b5563;
  color: white;
}
</style>
