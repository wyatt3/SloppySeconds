<template>
  <div class="container">
    <img class="header-img" :src="recipe.imagePath" />
    <h1 class="text-5xl mt-3 mb-3">{{ recipe.name }}</h1>
    <p class="text-gray-500">{{ recipe.description }}</p>
  </div>
</template>

<script>
export default {
  props: {
    recipe: Object,
  },
  data() {
    return {};
  },
};
</script>

<style scoped>
.container {
  margin: auto;
  width: 100%;
}

@media screen and (min-width: 601px) {
  .container {
    width: 85%;
  }
}

@media screen and (min-width: 1024px) {
  .container {
    width: 50%;
  }
}

.header-img {
  width: 100%;
  object-fit: cover;
  margin: auto;
}
</style>
