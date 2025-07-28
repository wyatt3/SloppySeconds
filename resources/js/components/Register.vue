<template>
  <Dialog class="min-w-[400px]" :draggable="false" v-model:visible="open" header="Register" modal>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="email" v-model="email" />
      <label>Email Address</label>
    </FloatLabel>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="password" v-model="password" />
      <label>Password</label>
    </FloatLabel>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="password" v-model="passwordConfirm" />
      <label>Confirm Password</label>
    </FloatLabel>
    <FloatLabel variant="in">
      <InputText class="w-full" type="text" v-model="joinCode" />
      <label>Join Code (Optional)</label>
    </FloatLabel>
    <div v-if="error" class="text-red-600 text-center mt-2">Invalid Credentials. Please Try Again.</div>
    <Button label="Register" class="my-2 w-full" :loading="loading" @click="register" />
  </Dialog>
</template>

<script>
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
export default {
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    Button,
    Dialog,
    FloatLabel,
    InputText,
  },
  data() {
    return {
      email: "",
      password: "",
      passwordConfirm: "",
      joinCode: "",
      error: false,
      loading: false,
    };
  },
  methods: {
    register() {
      axios.post(this.route("register"), {
        email: this.email,
        password: this.password,
        password_confirm: this.passwordConfirm,
        joinCode: this.joinCode,
      });
    },
  },
  computed: {
    open: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit("update:modelValue", value);
      },
    },
  },
};
</script>

<style scoped>
</style>
