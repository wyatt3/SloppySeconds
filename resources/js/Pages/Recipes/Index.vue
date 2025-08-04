<template>
  <div>
    <h1>Recipes</h1>
    <div class="filters">
      <InputText v-model="search" placeholder="Search" />
    </div>
    <table class="w-100">
      <template v-for="type in Object.keys(groupedRecipes)" :key="type">
        <thead class="bg-gray-200">
          <tr>
            <th colspan="100%" class="text-left px-5 py-2">{{ type }}s</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="recipe in groupedRecipes[type]" :key="recipe.id">
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
export default {
  components: { InputText },
  data() {
    return {
      recipes: [],
      loading: false,
      search: "",
    };
  },
  methods: {
    getRecipes() {
      this.loading = true;
      axios.get(this.route("api.recipes.index")).then((response) => {
        this.recipes = response.data;
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
</style>
