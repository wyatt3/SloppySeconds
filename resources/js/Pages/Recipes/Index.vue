<template>
  <div>
    <h1>Recipes</h1>
    <div v-for="type in Object.keys(groupedRecipes)" :key="type">
      <h2>{{ type }}</h2>
      <div v-for="recipe in groupedRecipes[type]" :key="recipe.id">
        <h3>{{ recipe.name }}</h3>
      </div>
    </div>
  </div>
</template>

<script>
export default {
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
      console.log(filteredRecipes);
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
