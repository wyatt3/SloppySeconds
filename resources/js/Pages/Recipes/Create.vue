<template>
  <Head title="Create Recipe" />
  <h1>Create Recipe</h1>
  <FloatLabel variant="on">
    <InputText v-model="name" />
    <label>Name</label>
  </FloatLabel>
  <FloatLabel variant="on">
    <InputText v-model="description" />
    <label>Description</label>
  </FloatLabel>
</template>

<script>
import FloatLabel from "primevue/floatlabel";
import { Head } from "@inertiajs/vue3";
import InputText from "primevue/inputtext";
export default {
  components: {
    FloatLabel,
    Head,
    InputText,
  },
  data() {
    return {
      name: "",
      description: "",
      image: null,
      type: "",
      directions: [],
      ingredients: [],
    };
  },
};
</script>

<style scoped>
</style>
