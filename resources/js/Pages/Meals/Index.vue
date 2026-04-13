<template>
  <Head title="Meal Planner" />
  <div>
    <h1 class="mb-3 text-5xl text-white">Meal Planner</h1>

    <!-- Week Navigation -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <Button @click="previousWeek" icon="pi pi-chevron-left" text />
        <span class="text-xl font-semibold min-w-64 text-center text-white">{{ weekRangeDisplay }}</span>
        <Button @click="nextWeek" icon="pi pi-chevron-right" text />
      </div>
      <Button @click="goToToday" label="Go to Today" size="small" />
    </div>

    <!-- 7-Day Grid -->
    <div class="week-grid">
      <div
        v-for="day in weekDays"
        :key="day.dateString"
        class="day-column border border-gray-700 rounded p-3 bg-gray-800"
        :class="{ 'border-emerald-500 bg-gray-700': day.isToday }"
      >
        <!-- Day Header -->
        <div class="day-header text-center mb-3 pb-2 border-b border-gray-700">
          <div class="text-sm text-gray-400">{{ day.dayName }}</div>
          <div class="font-bold text-lg text-white" :class="{ 'text-emerald-400': day.isToday }">
            {{ day.dateDisplay }}
          </div>
        </div>

        <!-- Recipes for this day -->
        <div class="recipes space-y-2 min-h-24">
          <div
            v-for="recipe in getMealRecipes(day.dateString)"
            :key="recipe.id"
            class="recipe-card bg-gray-700 border border-gray-600 rounded p-2 text-sm relative group shadow-sm"
          >
            <a :href="route('recipe.show', { recipe: recipe.id })" class="text-white hover:text-emerald-400">
              {{ recipe.name }}
            </a>
            <span class="text-xs text-gray-400 block">{{ recipe.type }}</span>
            <Button
              @click="removeRecipe(day.dateString, recipe.id)"
              icon="pi pi-times"
              text
              severity="danger"
              size="small"
              class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity"
            />
          </div>
        </div>

        <!-- Add Recipe Button -->
        <Button
          @click="openRecipeSelector(day.dateString)"
          icon="pi pi-plus"
          label="Add"
          text
          size="small"
          class="mt-2 w-full"
        />
      </div>
    </div>

    <!-- Recipe Selector Dialog -->
    <Dialog v-model:visible="showRecipeSelector" header="Add Recipe to Meal" :modal="true" :style="{ width: '500px' }">
      <div class="mb-3 pt-1">
        <FloatLabel variant="on">
          <InputText v-model="recipeSearch" class="w-full dark-input" />
          <label>Search recipes...</label>
        </FloatLabel>
      </div>
      <div
        v-if="filteredRecipes.length > 0"
        class="recipe-list max-h-80 overflow-y-auto border border-gray-700 rounded bg-gray-800"
      >
        <div
          v-for="recipe in filteredRecipes"
          :key="recipe.id"
          @click="selectRecipe(recipe)"
          class="p-3 hover:bg-gray-700 cursor-pointer border-b border-gray-700 last:border-b-0"
        >
          <div class="font-medium text-white">{{ recipe.name }}</div>
          <div class="text-sm text-gray-400">{{ recipe.type }}</div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500">
        <i class="pi pi-search text-4xl mb-2"></i>
        <p>No recipes found</p>
      </div>
    </Dialog>
  </div>
</template>

<script>
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import FloatLabel from "primevue/floatlabel";
import { Head } from "@inertiajs/vue3";
import InputText from "primevue/inputtext";

export default {
  components: { Button, Dialog, FloatLabel, Head, InputText },
  data() {
    return {
      // Week state
      weekStartDate: null,

      // Meals data - keyed by date string (YYYY-MM-DD)
      meals: {},

      // All recipes for selector
      recipes: [],

      // Modal state
      showRecipeSelector: false,
      selectedDate: null,
      recipeSearch: "",

      // Loading states
      loading: false,
    };
  },
  computed: {
    weekDays() {
      if (!this.weekStartDate) return [];

      const days = [];
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      for (let i = 0; i < 7; i++) {
        const date = new Date(this.weekStartDate);
        date.setDate(date.getDate() + i);

        days.push({
          dateString: this.formatDate(date),
          dayName: date.toLocaleDateString("en-US", { weekday: "short" }),
          dateDisplay: date.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
          }),
          isToday: date.getTime() === today.getTime(),
        });
      }
      return days;
    },
    weekRangeDisplay() {
      if (!this.weekStartDate) return "";
      const endDate = new Date(this.weekStartDate);
      endDate.setDate(endDate.getDate() + 6);

      const startStr = this.weekStartDate.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
      });
      const endStr = endDate.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
      });
      return `${startStr} - ${endStr}`;
    },
    filteredRecipes() {
      if (!this.recipeSearch) return this.recipes;
      const search = this.recipeSearch.toLowerCase();
      return this.recipes.filter((r) => r.name.toLowerCase().includes(search) || r.type.toLowerCase().includes(search));
    },
  },
  methods: {
    formatDate(date) {
      return date.toISOString().split("T")[0];
    },
    getWeekStart(date) {
      const d = new Date(date);
      const day = d.getDay();
      // Adjust to get Sunday as start of week (change to Monday: day === 0 ? -6 : 1)
      const diff = d.getDate() - day;
      d.setDate(diff);
      d.setHours(0, 0, 0, 0);
      return d;
    },
    previousWeek() {
      const newStart = new Date(this.weekStartDate);
      newStart.setDate(newStart.getDate() - 7);
      this.weekStartDate = newStart;
      this.fetchMeals();
    },
    nextWeek() {
      const newStart = new Date(this.weekStartDate);
      newStart.setDate(newStart.getDate() + 7);
      this.weekStartDate = newStart;
      this.fetchMeals();
    },
    goToToday() {
      this.weekStartDate = this.getWeekStart(new Date());
      this.fetchMeals();
    },
    async fetchMeals() {
      this.loading = true;
      const endDate = new Date(this.weekStartDate);
      endDate.setDate(endDate.getDate() + 6);

      try {
        const response = await axios.get(this.route("api.meals.index"), {
          params: {
            start_date: this.formatDate(this.weekStartDate),
            end_date: this.formatDate(endDate),
          },
        });

        // Convert array to object keyed by date
        this.meals = {};
        response.data.forEach((meal) => {
          this.meals[meal.date] = meal;
        });
      } catch (error) {
        console.error("Error fetching meals:", error);
      } finally {
        this.loading = false;
      }
    },
    async fetchRecipes() {
      try {
        const response = await axios.get(this.route("api.recipes.index"));
        this.recipes = response.data;
      } catch (error) {
        console.error("Error fetching recipes:", error);
      }
    },
    getMealRecipes(dateString) {
      const meal = this.meals[dateString];
      return meal?.recipes || [];
    },
    openRecipeSelector(dateString) {
      this.selectedDate = dateString;
      this.recipeSearch = "";
      this.showRecipeSelector = true;
    },
    async selectRecipe(recipe) {
      const dateString = this.selectedDate;
      let meal = this.meals[dateString];

      try {
        // If no meal exists for this date, create one first
        if (!meal) {
          const createResponse = await axios.post(this.route("api.meals.store"), {
            date: dateString,
          });
          meal = createResponse.data;
          meal.recipes = [];
          this.meals[dateString] = meal;
        }

        // Add recipe to meal
        const response = await axios.post(
          this.route("api.meals.add-recipe", {
            meal: meal.id,
            recipe: recipe.id,
          })
        );

        // Update local state with response
        this.meals[dateString] = response.data;
        this.showRecipeSelector = false;
      } catch (error) {
        console.error("Error adding recipe to meal:", error);
      }
    },
    async removeRecipe(dateString, recipeId) {
      const meal = this.meals[dateString];
      if (!meal) return;

      try {
        const response = await axios.delete(
          this.route("api.meals.remove-recipe", {
            meal: meal.id,
            recipe: recipeId,
          })
        );

        // Update local state with response
        this.meals[dateString] = response.data;
      } catch (error) {
        console.error("Error removing recipe from meal:", error);
      }
    },
  },
  created() {
    // Initialize to current week
    this.weekStartDate = this.getWeekStart(new Date());
    this.fetchMeals();
    this.fetchRecipes();
  },
};
</script>

<style scoped>
.week-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.day-column {
  min-height: 200px;
}

.recipe-card {
  transition: all 0.2s ease;
}

.recipe-card:hover {
  background-color: #374151;
}

:deep(.dark-input .p-inputtext) {
  background-color: #1f2937;
  border-color: #4b5563;
  color: white;
}

@media (max-width: 1024px) {
  .week-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .week-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .week-grid {
    grid-template-columns: 1fr;
  }

  .day-column {
    min-height: auto;
  }
}
</style>
