<template>
  <div>
    <h1>Recipes</h1>
  </div>
</template>

<script>
export default {
  data() {
    return {
      recipes: [],
      loading: false,
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
  created() {
    this.getRecipes();
  },
};
</script>

<style scoped>
</style>
