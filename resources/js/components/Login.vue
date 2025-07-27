<template>
  <button class="text-gray-300 hover:text-gray-700 hover:text-white" @click="open = true">Login</button>

  <Dialog class="min-w-[400px]" :draggable="false" v-model:visible="open" header="Login" modal>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="email" v-model="email" />
      <label>Email Address</label>
    </FloatLabel>
    <FloatLabel variant="in">
      <InputText class="w-full" type="password" v-model="password" />
      <label>Password</label>
    </FloatLabel>
    <Button label="Login" class="my-2 w-full" @click="attemptLogin" />
    <button
      class="text-gray-600 hover:text-gray-400 underline hover:no-underline m-auto block"
      @click="
        open = false;
        $emit('register');
      "
    >
      New Here? Register Now
    </button>
  </Dialog>
</template>

<script>
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
export default {
  emits: ["register"],
  components: {
    Button,
    Dialog,
    FloatLabel,
    InputText,
  },
  data() {
    return {
      open: false,
      email: null,
      password: null,
      remember: false,
    };
  },
  methods: {
    attemptLogin() {
      axios
        .post("/login", {
          email: this.email,
          password: this.password,
          remember: this.remember,
        })
        .then((response) => {
          window.location.href = this.route("home");
        })
        .catch((error) => {});
    },
  },
};
</script>

<style scoped>
</style>
