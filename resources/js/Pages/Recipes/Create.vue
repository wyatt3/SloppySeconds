<template>
  <div class="small-container">
    <Head title="Create Recipe" />
    <h1 class="mb-3 text-5xl">Create Recipe</h1>
    <FloatLabel class="mb-3" variant="on">
      <InputText class="input" v-model="name" />
      <label>Name</label>
    </FloatLabel>
    <FloatLabel class="mb-3" variant="on">
      <TextArea class="input" rows="6" v-model="description" />
      <label>Description</label>
    </FloatLabel>
    <h2 class="mb-3 text-3xl">Ingredients</h2>
    <table>
      <thead>
        <tr>
          <th></th>
          <th>Name</th>
          <th>Quantity</th>
          <th>Unit</th>
        </tr>
      </thead>
      <draggable v-model="ingredients" tag="tbody" item-key="name" handle=".handle">
        <template #item="{ element: ingredient }">
          <tr>
            <td class="handle"><i class="pi pi-bars"></i></td>
            <td>{{ ingredient.name }}</td>
            <td>{{ ingredient.amount }}</td>
            <td>{{ ingredient.unit }}</td>
          </tr>
        </template>
      </draggable>
    </table>
    <h2 class="mb-3 text-3xl">Directions</h2>
    <table>
      <thead>
        <tr>
          <th></th>
          <th>Title</th>
          <th>Direction</th>
          <th>Image</th>
        </tr>
      </thead>
      <draggable v-model="directions" tag="tbody" item-key="name" handle=".handle">
        <template #item="{ element: direction }">
          <tr>
            <td class="handle"><i class="pi pi-bars"></i></td>
            <td>{{ direction.title }}</td>
            <td>{{ direction.content }}</td>
            <td>{{ direction.image }}</td>
          </tr>
        </template>
      </draggable>
    </table>
  </div>
</template>

<script>
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import draggable from "vuedraggable";
import FloatLabel from "primevue/floatlabel";
import { Head } from "@inertiajs/vue3";
import InputText from "primevue/inputtext";
import TextArea from "primevue/textarea";

export default {
  components: {
    Column,
    DataTable,
    draggable,
    FloatLabel,
    Head,
    InputText,
    TextArea,
  },
  data() {
    return {
      name: "",
      description: "",
      image: null,
      type: "",
      ingredients: [],
      directions: [],
    };
  },
};
</script>

<style scoped>
.small-container {
  width: 100%;
  max-width: 500px;
  margin: auto;
}
.input {
  width: 100%;
}

table {
  width: 100%;
  margin-bottom: 1.25rem;
}
</style>
