<template>
  <Dialog class="min-w-[400px]" :draggable="false" v-model:visible="open" header="Register" modal></Dialog>
</template>

<script>
import Dialog from "primevue/dialog";
export default {
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    Dialog,
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
