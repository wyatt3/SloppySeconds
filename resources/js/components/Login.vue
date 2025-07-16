<template>
  <button @click="open = true" class="nav-link">Login</button>

  <Dialog v-model:visible="open" header="Login" modal></Dialog>
</template>

<script>
import Dialog from "primevue/dialog";
export default {
  components: {
    Dialog,
  },
  data() {
    return {
      open: false,
    };
  },
};
</script>

<style scoped>
</style>
