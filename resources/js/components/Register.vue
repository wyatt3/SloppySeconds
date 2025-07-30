<template>
  <Dialog class="min-w-[400px]" :draggable="false" v-model:visible="open" header="Register" modal>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="email" v-model="name" @input="nameErrors = []" />
      <label>Name</label>
    </FloatLabel>
    <Message v-for="error in nameErrors" :key="error" class="ms-2 mb-2" severity="error" size="small" variant="simple">
      {{ error }}
    </Message>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="email" v-model="email" @input="emailErrors = []" />
      <label>Email Address</label>
    </FloatLabel>
    <Message v-for="error in emailErrors" :key="error" class="ms-2 mb-2" severity="error" size="small" variant="simple">
      {{ error }}
    </Message>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="password" v-model="password" @input="passwordErrors = []" />
      <label>Password</label>
    </FloatLabel>
    <FloatLabel variant="in" class="mb-2">
      <InputText class="w-full" type="password" v-model="passwordConfirm" @input="passwordErrors = []" />
      <label>Confirm Password</label>
    </FloatLabel>
    <Message
      v-for="error in passwordErrors"
      :key="error"
      class="ms-2 mb-2"
      severity="error"
      size="small"
      variant="simple"
    >
      {{ error }}
    </Message>
    <FloatLabel variant="in">
      <InputText class="w-full" type="text" v-model="joinCode" @input="joinCodeErrors = []" />
      <label>Fa Code (Optional)</label>
    </FloatLabel>
    <Message
      v-for="error in joinCodeErrors"
      :key="error"
      class="ms-2 mb-2"
      severity="error"
      size="small"
      variant="simple"
    >
      {{ error }}
    </Message>
    <div v-if="error" class="text-red-600 text-center mt-2">Invalid Credentials. Please Try Again.</div>
    <Button label="Register" class="my-2 w-full" :loading="loading" @click="register" />
  </Dialog>
</template>

<script>
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
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
    Message,
  },
  data() {
    return {
      name: "",
      email: "",
      password: "",
      passwordConfirm: "",
      joinCode: "",
      error: false,
      loading: false,
      nameErrors: [],
      emailErrors: [],
      passwordErrors: [],
      joinCodeErrors: [],
    };
  },
  methods: {
    register() {
      this.loading = true;
      axios
        .post(this.route("register"), {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirm,
          join_code: this.joinCode,
        })
        .then((response) => {
          window.location.href = this.route("home");
        })
        .catch((error) => {
          this.loading = false;
          console.error(error);
          this.nameErrors = error.response.data.errors.name;
          this.emailErrors = error.response.data.errors.email;
          this.passwordErrors = error.response.data.errors.password;
          this.joinCodeErrors = error.response.data.errors.join_code;
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
