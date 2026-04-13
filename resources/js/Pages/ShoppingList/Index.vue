<template>
  <Head title="Shopping List" />
  <div class="shopping-list-container">
    <h1 class="mb-3 text-5xl text-white">Shopping List</h1>

    <!-- Date Range Selector -->
    <div class="date-selector mb-4 flex gap-3 flex-wrap items-end">
      <FloatLabel variant="on">
        <DatePicker v-model="startDate" dateFormat="yy-mm-dd" class="dark-input" />
        <label>Start Date</label>
      </FloatLabel>
      <FloatLabel variant="on">
        <DatePicker v-model="endDate" dateFormat="yy-mm-dd" class="dark-input" />
        <label>End Date</label>
      </FloatLabel>
      <Button @click="generateList" :loading="loading" :disabled="!canGenerate">
        <i class="pi pi-list-check mr-2"></i>
        Generate List
      </Button>
      <Button v-if="ingredients.length > 0" @click="printList" severity="secondary" outlined>
        <i class="pi pi-print mr-2"></i>
        Print
      </Button>
    </div>

    <!-- Quick Date Presets -->
    <div class="presets mb-4 flex gap-2">
      <Button size="small" severity="secondary" outlined @click="setThisWeek"> This Week </Button>
      <Button size="small" severity="secondary" outlined @click="setNextWeek"> Next Week </Button>
      <Button size="small" severity="secondary" outlined @click="setNext2Weeks"> Next 2 Weeks </Button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <ProgressSpinner />
      <p class="mt-2 text-gray-500">Generating shopping list...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="hasSearched && ingredients.length === 0" class="empty-state text-center py-8">
      <i class="pi pi-shopping-cart text-6xl text-gray-300 mb-3 block"></i>
      <p class="text-gray-500">No ingredients found for the selected date range.</p>
      <p class="text-gray-400 text-sm">Try selecting a different date range with scheduled meals.</p>
    </div>

    <!-- Shopping List -->
    <div v-else-if="ingredients.length > 0" class="shopping-list">
      <div class="list-header flex justify-between items-center mb-3">
        <span class="text-gray-500"> {{ checkedCount }} of {{ ingredients.length }} items checked </span>
        <Button v-if="checkedCount > 0" size="small" severity="secondary" text @click="clearChecked">
          Clear Checked
        </Button>
      </div>

      <div class="ingredient-list border border-gray-700 rounded bg-gray-800">
        <div
          v-for="ingredient in ingredients"
          :key="ingredient.id"
          class="ingredient-item flex items-center gap-3 py-3 px-4 border-b border-gray-700 last:border-b-0 hover:bg-gray-700"
          :class="{ 'opacity-50': checkedItems[ingredient.id] }"
        >
          <Checkbox v-model="checkedItems[ingredient.id]" :binary="true" :inputId="'ingredient-' + ingredient.id" />
          <label
            :for="'ingredient-' + ingredient.id"
            class="flex-1 cursor-pointer flex justify-between"
            :class="{ 'line-through': checkedItems[ingredient.id] }"
          >
            <span class="ingredient-name font-medium text-white">{{ ingredient.name }}</span>
            <span class="ingredient-amount text-gray-400">
              {{ formatAmount(ingredient.amount) }} {{ ingredient.unit }}
            </span>
          </label>
        </div>
      </div>
    </div>

    <!-- Initial State -->
    <div v-else class="initial-state text-center py-8">
      <i class="pi pi-calendar text-6xl text-gray-300 mb-3 block"></i>
      <p class="text-gray-500">Select a date range to generate your shopping list.</p>
    </div>
  </div>
</template>

<script>
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import DatePicker from "primevue/datepicker";
import FloatLabel from "primevue/floatlabel";
import { Head } from "@inertiajs/vue3";
import ProgressSpinner from "primevue/progressspinner";

export default {
  components: {
    Button,
    Checkbox,
    DatePicker,
    FloatLabel,
    Head,
    ProgressSpinner,
  },
  data() {
    return {
      startDate: null,
      endDate: null,
      ingredients: [],
      checkedItems: {},
      loading: false,
      hasSearched: false,
    };
  },
  computed: {
    canGenerate() {
      return this.startDate && this.endDate;
    },
    checkedCount() {
      return Object.values(this.checkedItems).filter(Boolean).length;
    },
  },
  methods: {
    generateList() {
      if (!this.canGenerate) return;

      this.loading = true;
      this.hasSearched = true;

      const params = {
        start_date: this.formatDateForApi(this.startDate),
        end_date: this.formatDateForApi(this.endDate),
      };

      axios
        .get(this.route("api.shopping-list.index"), { params })
        .then((response) => {
          this.ingredients = response.data;
          // Reset checked items when generating new list
          this.checkedItems = {};
        })
        .catch((error) => {
          console.error("Error generating shopping list:", error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formatDateForApi(date) {
      if (!date) return null;
      const d = new Date(date);
      return d.toISOString().split("T")[0];
    },
    formatAmount(amount) {
      // Remove trailing zeros and format nicely
      if (amount === null || amount === undefined) return "";
      const num = parseFloat(amount);
      return num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, "");
    },
    clearChecked() {
      this.checkedItems = {};
    },
    printList() {
      return;
    },
    // Date preset methods
    setThisWeek() {
      const today = new Date();
      const dayOfWeek = today.getDay();
      const startOfWeek = new Date(today);
      startOfWeek.setDate(today.getDate() - dayOfWeek);
      const endOfWeek = new Date(startOfWeek);
      endOfWeek.setDate(startOfWeek.getDate() + 6);

      this.startDate = startOfWeek;
      this.endDate = endOfWeek;
    },
    setNextWeek() {
      const today = new Date();
      const dayOfWeek = today.getDay();
      const startOfNextWeek = new Date(today);
      startOfNextWeek.setDate(today.getDate() - dayOfWeek + 7);
      const endOfNextWeek = new Date(startOfNextWeek);
      endOfNextWeek.setDate(startOfNextWeek.getDate() + 6);

      this.startDate = startOfNextWeek;
      this.endDate = endOfNextWeek;
    },
    setNext2Weeks() {
      const today = new Date();
      this.startDate = today;
      const twoWeeksLater = new Date(today);
      twoWeeksLater.setDate(today.getDate() + 14);
      this.endDate = twoWeeksLater;
    },
  },
  created() {
    // Default to this week
    this.setThisWeek();
  },
};
</script>

<style scoped>
.shopping-list-container {
  max-width: 600px;
  margin: auto;
}

.ingredient-item {
  transition: all 0.2s ease;
}

:deep(.dark-input .p-inputtext),
:deep(.dark-input .p-datepicker) {
  background-color: #1f2937;
  border-color: #4b5563;
  color: white;
}

/* Print-friendly styles */
@media print {
  .date-selector,
  .presets,
  .list-header {
    display: none !important;
  }

  .shopping-list-container {
    max-width: 100%;
  }

  .ingredient-item.opacity-50 {
    display: none;
  }

  h1 {
    font-size: 24px !important;
    margin-bottom: 1rem !important;
  }
}
</style>
