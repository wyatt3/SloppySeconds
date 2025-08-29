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
  <h2>Ingredients</h2>
  <table>
    <thead>
      <tr>
        <th></th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Order</th>
      </tr>
    </thead>
    <draggable v-model="ingredients" tag="tbody" item-key="name" handle=".handle">
      <template #item="{ element: ingredient }">
        <tr>
          <td class="handle"><i class="pi pi-bars"></i></td>
          <td>{{ ingredient.name }}</td>
          <td>{{ ingredient.amount }}</td>
          <td>{{ ingredient.unit }}</td>
          <td>{{ ingredient.order }}</td>
        </tr>
      </template>
    </draggable>
  </table>
  <h2>Directions</h2>
  <table>
    <thead>
      <tr>
        <th></th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Order</th>
      </tr>
    </thead>
    <draggable v-model="directions" tag="tbody" item-key="name" handle=".handle">
      <template #item="{ element: direction }">
        <tr>
          <td class="handle"><i class="pi pi-bars"></i></td>
          <td>{{ direction.title }}</td>
          <td>{{ direction.content }}</td>
          <td>{{ direction.unit }}</td>
          <td>{{ direction.order }}</td>
        </tr>
      </template>
    </draggable>
  </table>
</template>

<script>
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import draggable from "vuedraggable";
import FloatLabel from "primevue/floatlabel";
import { Head } from "@inertiajs/vue3";
import InputText from "primevue/inputtext";
export default {
  components: {
    Column,
    DataTable,
    draggable,
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
      ingredients: [],
      directions: [],
    };
  },
};
</script>

<style scoped>
</style>
