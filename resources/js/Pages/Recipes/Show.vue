<template>
  <div class="container">
    <Head :title="recipe.name" />
    <div v-if="recipe.imagePath && !recipeImageFailed" class="header-img-container">
      <img class="header-img" :src="recipe.imagePath" @error="recipeImageFailed = true" />
    </div>
    <div v-else class="header-img-container header-img-placeholder">
      <span>No Image</span>
    </div>

    <!-- Header with Title and Actions -->
    <div class="flex justify-between items-start mt-3 mb-3">
      <div>
        <h1 class="text-5xl mb-1 text-white">{{ recipe.name }}</h1>
        <span class="text-sm text-gray-400">{{ recipe.type }}</span>
      </div>
      <div class="flex gap-2">
        <Button
          as="a"
          :href="route('recipe.edit', { recipe: recipe.id })"
          icon="pi pi-pencil"
          label="Edit"
          severity="secondary"
        />
        <Button icon="pi pi-calendar-plus" label="Add to Meal" @click="showDatePicker = true" />
      </div>
    </div>

    <p class="text-gray-300 mb-5">{{ recipe.description }}</p>

    <!-- Ingredients Section -->
    <h2 class="text-3xl mb-3 text-white">Ingredients</h2>
    <ul v-if="recipe.orderedIngredients.length > 0" class="ingredient-list mb-5">
      <li
        v-for="ingredient in recipe.orderedIngredients"
        :key="ingredient.id"
        class="py-2 border-b border-gray-700 flex justify-between text-gray-300"
      >
        <span>{{ ingredient.name }}</span>
        <span class="text-gray-500">{{ formatAmount(ingredient.amount) }} {{ ingredient.unit }}</span>
      </li>
    </ul>
    <p v-else class="text-gray-500 mb-5">No ingredients listed.</p>

    <!-- Directions Section -->
    <h2 class="text-3xl mb-3 text-white">Directions</h2>
    <ol v-if="recipe.orderedDirections.length > 0" class="direction-list mb-5">
      <li v-for="(direction, index) in recipe.orderedDirections" :key="direction.id" class="mb-4">
        <div class="flex gap-3">
          <div
            class="step-number flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-bold"
          >
            {{ index + 1 }}
          </div>
          <div class="flex-1">
            <div v-if="direction.title" class="font-semibold mb-1 text-white">{{ direction.title }}</div>
            <p class="text-gray-300">{{ direction.content }}</p>
            <div v-if="direction.image && !directionImageFailed[direction.id]" class="mt-2">
              <img :src="direction.imagePath" class="max-w-sm rounded" @error="() => directionImageFailed[direction.id] = true" />
            </div>
            <div v-else-if="direction.image" class="mt-2 max-w-sm rounded bg-gray-700 h-32 flex items-center justify-center">
              <span class="text-gray-500">No Image</span>
            </div>
          </div>
        </div>
      </li>
    </ol>
    <p v-else class="text-gray-500 mb-5">No directions listed.</p>

    <!-- Add to Meal Date Picker Dialog -->
    <Dialog v-model:visible="showDatePicker" header="Add to Meal Plan" :modal="true" :style="{ width: '350px' }">
      <div class="flex flex-col items-center">
        <DatePicker v-model="selectedDate" inline />
        <Button @click="addToMeal" label="Add to Selected Date" class="mt-4 w-full" :loading="addingToMeal" />
      </div>
    </Dialog>

    <!-- Success Message -->
    <Message
      v-if="mealAddedMessage"
      class="fixed bottom-4 right-4"
      :life="3000"
      @close="mealAddedMessage = ''"
      @life-end="mealAddedMessage = ''"
    >
      {{ mealAddedMessage }}
    </Message>
  </div>
</template>

<script>
import Button from "primevue/button";
import DatePicker from "primevue/datepicker";
import Dialog from "primevue/dialog";
import { Head } from "@inertiajs/vue3";
import Message from "primevue/message";

export default {
  components: { Button, DatePicker, Dialog, Head, Message },
  props: {
    recipe: Object,
  },
  data() {
    return {
      showDatePicker: false,
      selectedDate: new Date(),
      addingToMeal: false,
      mealAddedMessage: "",
      recipeImageFailed: false,
      directionImageFailed: {},
    };
  },
  methods: {
    formatAmount(amount) {
      if (amount === null || amount === undefined) return "";
      const num = parseFloat(amount);
      return num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, "");
    },
    formatDate(date) {
      return date.toISOString().split("T")[0];
    },
    async addToMeal() {
      this.addingToMeal = true;
      const dateString = this.formatDate(this.selectedDate);

      try {
        // First, check if meal exists for this date
        const mealsResponse = await axios.get(this.route("api.meals.index"), {
          params: {
            start_date: dateString,
            end_date: dateString,
          },
        });

        let meal = mealsResponse.data[0];

        // If no meal exists, create one
        if (!meal) {
          const createResponse = await axios.post(this.route("api.meals.store"), {
            date: dateString,
          });
          meal = createResponse.data;
        }

        // Check if recipe is already in this meal
        if (meal.recipes?.some((r) => r.id === this.recipe.id)) {
          this.showDatePicker = false;
          this.mealAddedMessage = `"${this.recipe.name}" is already on ${this.selectedDate.toLocaleDateString()}`;
          return;
        }

        // Add recipe to meal
        await axios.post(this.route("api.meals.add-recipe", { meal: meal.id, recipe: this.recipe.id }));

        this.showDatePicker = false;
        this.mealAddedMessage = `Added "${this.recipe.name}" to ${this.selectedDate.toLocaleDateString()}`;
      } catch (error) {
        console.error("Error adding recipe to meal:", error);
        alert("Error adding recipe to meal plan");
      } finally {
        this.addingToMeal = false;
      }
    },
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

.header-img-container {
  width: 100%;
  max-height: 400px;
  margin: auto;
  border-radius: 0.5rem;
  overflow: hidden;
}

.header-img {
  width: 100%;
  max-height: 400px;
  object-fit: cover;
}

.header-img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #374151;
  min-height: 200px;
  color: #9ca3af;
}

.ingredient-list {
  list-style: none;
  padding: 0;
}

.direction-list {
  list-style: none;
  padding: 0;
}
</style>
