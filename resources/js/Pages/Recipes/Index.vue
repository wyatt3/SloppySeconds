<template>
  <div>
    <h1 class="mb-3 text-5xl">Recipes</h1>
    <Button label="New Recipe" as="a" :href="route('recipe.create')" />
    <div class="filters mb-3 flex gap-2">
      <FloatLabel variant="on">
        <InputText v-model="search" />
        <label>Search</label>
      </FloatLabel>
      <FloatLabel variant="on">
        <Select v-model="typeFilter" :options="allTypes" placeholder="Recipe Type" />
        <label>Recipe Type</label>
      </FloatLabel>
    </div>
    <table class="recipe-table">
      <template v-for="type in Object.keys(groupedRecipes)" :key="type">
        <thead class="bg-gray-200">
          <tr>
            <th colspan="100%" class="text-left px-5 py-2">{{ type }}s</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="recipe in groupedRecipes[type]" :key="recipe.id">
            <td class="px-5 py-2">
              <a :href="route('recipe.show', recipe.id)"><i class="pi pi-eye"></i></a>
            </td>
            <td class="px-5 py-2">{{ recipe.name }}</td>
            <td class="px-5 py-2">{{ recipe.description }}</td>
          </tr>
        </tbody>
      </template>
    </table>
  </div>
</template>

<script>
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import FloatLabel from "primevue/floatlabel";
export default {
  components: { InputText, Select, FloatLabel },
  data() {
    return {
      recipes: [],
      loading: false,
      search: "",
      typeFilter: "All",
      allTypes: ["All"],
    };
  },
  methods: {
    getRecipes() {
      this.loading = true;
      axios.get(this.route("api.recipes.index")).then((response) => {
        this.recipes = response.data;
        // Get all unique recipe types
        this.allTypes = ["All", ...new Set(this.recipes.map((recipe) => recipe.type))];
        this.loading = false;
      });
    },
  },
  computed: {
    groupedRecipes() {
      let filteredRecipes = this.recipes.filter((recipe) => {
        return (
          recipe.name.includes(this.search) ||
          recipe.description.includes(this.search) ||
          recipe.type.includes(this.search)
        );
      });
      filteredRecipes = filteredRecipes.filter((recipe) => {
        return this.typeFilter === "All" || recipe.type === this.typeFilter;
      });
      return filteredRecipes.reduce((acc, item) => {
        const groupKey = item["type"];
        if (!acc[groupKey]) {
          acc[groupKey] = [];
        }
        acc[groupKey].push(item);
        return acc;
      }, {});
    },
  },
  created() {
    this.getRecipes();
  },
};
</script>

<style scoped>
.recipe-table {
  width: 100%;
}
</style>
