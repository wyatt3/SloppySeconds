<template>
  <div class="small-container">
    <Head title="Create Recipe" />
    <h1 class="mb-3 text-5xl text-white">Create Recipe</h1>

    <!-- Recipe Name -->
    <FloatLabel class="mb-3" variant="on">
      <InputText class="input dark-input" v-model="name" />
      <label>Name</label>
    </FloatLabel>
    <Message v-if="errors.name" severity="error" class="mb-2">{{ errors.name[0] }}</Message>

    <!-- Recipe Description -->
    <FloatLabel class="mb-3" variant="on">
      <TextArea class="input dark-input" rows="4" v-model="description" />
      <label>Description</label>
    </FloatLabel>

    <!-- Recipe Type -->
    <FloatLabel class="mb-3" variant="on">
      <Select class="input dark-input" v-model="type" :options="recipeTypes" placeholder="Recipe Type" />
      <label>Recipe Type</label>
    </FloatLabel>
    <Message v-if="errors.type" severity="error" class="mb-2">{{ errors.type[0] }}</Message>

    <!-- Recipe Image -->
    <div class="mb-4">
      <label class="block mb-2 font-semibold text-white">Recipe Image</label>
      <div v-if="imagePreview" class="mb-2 flex items-center gap-2">
        <img :src="imagePreview" class="max-w-48 max-h-48 object-cover rounded" />
        <Button icon="pi pi-times" severity="danger" text @click="clearRecipeImage" />
      </div>
      <FileUpload
        mode="basic"
        accept="image/*"
        :auto="false"
        @select="onRecipeImageSelect"
        chooseLabel="Choose Image"
      />
    </div>

    <!-- Ingredients Section -->
    <h2 class="mb-3 text-3xl text-white">Ingredients</h2>
    <table v-if="ingredients.length > 0">
      <thead>
        <tr>
          <th class="w-8"></th>
          <th class="text-left text-white">Name</th>
          <th class="text-left text-white">Qty</th>
          <th class="text-left text-white">Unit</th>
          <th class="w-20"></th>
        </tr>
      </thead>
      <draggable v-model="ingredients" tag="tbody" item-key="tempId" handle=".handle">
        <template #item="{ element: ingredient, index }">
          <tr>
            <td class="handle cursor-move"><i class="pi pi-bars"></i></td>
            <template v-if="editingIngredientIndex === index">
              <td><InputText v-model="ingredient.name" class="w-full" size="small" /></td>
              <td><InputText v-model="ingredient.amount" type="number" step="any" class="w-16" size="small" /></td>
              <td><InputText v-model="ingredient.unit" class="w-20" size="small" /></td>
              <td class="flex gap-1">
                <Button icon="pi pi-check" text size="small" @click="saveIngredientEdit" />
                <Button
                  icon="pi pi-times"
                  text
                  size="small"
                  severity="secondary"
                  @click="cancelIngredientEdit(index)"
                />
              </td>
            </template>
            <template v-else>
              <td>{{ ingredient.name }}</td>
              <td>{{ ingredient.amount }}</td>
              <td>{{ ingredient.unit }}</td>
              <td class="flex gap-1">
                <Button icon="pi pi-pencil" text size="small" @click="editIngredient(index)" />
                <Button icon="pi pi-trash" text size="small" severity="danger" @click="removeIngredient(index)" />
              </td>
            </template>
          </tr>
        </template>
      </draggable>
    </table>
    <p v-else class="text-gray-500 mb-3">No ingredients added yet.</p>

    <!-- Add New Ingredient -->
    <div class="flex gap-2 mb-5 items-end">
      <FloatLabel variant="on" class="flex-1">
        <InputText v-model="newIngredient.name" class="w-full" size="small" />
        <label>Ingredient</label>
      </FloatLabel>
      <FloatLabel variant="on" class="w-20">
        <InputText v-model="newIngredient.amount" type="number" step="any" class="w-full" size="small" />
        <label>Qty</label>
      </FloatLabel>
      <FloatLabel variant="on" class="w-24">
        <InputText v-model="newIngredient.unit" class="w-full" size="small" />
        <label>Unit</label>
      </FloatLabel>
      <Button icon="pi pi-plus" size="small" @click="addIngredient" :disabled="!canAddIngredient" />
    </div>

    <!-- Directions Section -->
    <h2 class="mb-3 text-3xl text-white">Directions</h2>
    <div v-if="directions.length > 0" class="directions-list mb-3">
      <draggable v-model="directions" item-key="tempId" handle=".handle">
        <template #item="{ element: direction, index }">
          <div class="direction-item border border-gray-700 rounded p-3 mb-2 bg-gray-800 text-white">
            <div class="flex items-start gap-2">
              <div class="handle cursor-move pt-1"><i class="pi pi-bars"></i></div>
              <div class="flex-1">
                <template v-if="editingDirectionIndex === index">
                  <FloatLabel variant="on" class="mb-2">
                    <InputText v-model="direction.title" class="w-full" size="small" />
                    <label>Title (optional)</label>
                  </FloatLabel>
                  <FloatLabel variant="on" class="mb-2">
                    <TextArea v-model="direction.content" rows="2" class="w-full" />
                    <label>Direction</label>
                  </FloatLabel>
                  <div class="flex items-center gap-2 mb-2">
                    <FileUpload
                      mode="basic"
                      accept="image/*"
                      :auto="false"
                      @select="(e) => onDirectionImageSelect(e, index)"
                      chooseLabel="Image"
                      class="p-button-sm"
                    />
                    <img
                      v-if="direction.imagePreview"
                      :src="direction.imagePreview"
                      class="w-16 h-16 object-cover rounded"
                    />
                  </div>
                  <div class="flex gap-1">
                    <Button label="Save" icon="pi pi-check" size="small" @click="saveDirectionEdit" />
                    <Button
                      label="Cancel"
                      icon="pi pi-times"
                      size="small"
                      severity="secondary"
                      @click="cancelDirectionEdit(index)"
                    />
                  </div>
                </template>
                <template v-else>
                  <div class="flex justify-between">
                    <div>
                      <div class="font-semibold">
                        Step {{ index + 1 }}<span v-if="direction.title">: {{ direction.title }}</span>
                      </div>
                      <p class="text-gray-300">{{ direction.content }}</p>
                    </div>
                    <div class="flex gap-1">
                      <Button icon="pi pi-pencil" text size="small" @click="editDirection(index)" />
                      <Button icon="pi pi-trash" text size="small" severity="danger" @click="removeDirection(index)" />
                    </div>
                  </div>
                  <img v-if="direction.imagePreview" :src="direction.imagePreview" class="mt-2 max-w-32 rounded" />
                </template>
              </div>
            </div>
          </div>
        </template>
      </draggable>
    </div>
    <p v-else class="text-gray-500 mb-3">No directions added yet.</p>

    <!-- Add New Direction -->
    <div class="border border-gray-700 rounded p-3 mb-5 bg-gray-800">
      <FloatLabel variant="on" class="mb-2">
        <InputText v-model="newDirection.title" class="w-full" />
        <label>Title (optional)</label>
      </FloatLabel>
      <FloatLabel variant="on" class="mb-2">
        <TextArea v-model="newDirection.content" rows="3" class="w-full" />
        <label>Direction</label>
      </FloatLabel>
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2">
          <FileUpload
            mode="basic"
            accept="image/*"
            :auto="false"
            @select="onNewDirectionImageSelect"
            chooseLabel="Add Image"
          />
          <img
            v-if="newDirection.imagePreview"
            :src="newDirection.imagePreview"
            class="w-12 h-12 object-cover rounded"
          />
          <Button
            v-if="newDirection.imagePreview"
            icon="pi pi-times"
            text
            size="small"
            severity="secondary"
            @click="clearNewDirectionImage"
          />
        </div>
        <Button label="Add Direction" icon="pi pi-plus" @click="addDirection" :disabled="!canAddDirection" />
      </div>
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2">
      <Button
        label="Create Recipe"
        icon="pi pi-check"
        :loading="submitting"
        @click="submitRecipe"
        :disabled="!canSubmit"
      />
    </div>

    <!-- General Error Message -->
    <Message v-if="generalError" severity="error" class="mt-3">{{ generalError }}</Message>
  </div>
</template>

<script>
import Button from "primevue/button";
import draggable from "vuedraggable";
import FileUpload from "primevue/fileupload";
import FloatLabel from "primevue/floatlabel";
import { Head } from "@inertiajs/vue3";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
import Select from "primevue/select";
import TextArea from "primevue/textarea";

export default {
  components: {
    Button,
    draggable,
    FileUpload,
    FloatLabel,
    Head,
    InputText,
    Message,
    Select,
    TextArea,
  },
  data() {
    return {
      // Form fields
      name: "",
      description: "",
      image: null,
      imagePreview: null,
      type: null,

      // Recipe type options
      recipeTypes: ["Entree", "Side", "Dessert", "Drink"],

      // Collections
      ingredients: [],
      directions: [],

      // New item forms
      newIngredient: { name: "", amount: "", unit: "" },
      newDirection: { title: "", content: "", image: null, imagePreview: null },

      // Edit state
      editingIngredientIndex: null,
      editingDirectionIndex: null,
      ingredientBackup: null,
      directionBackup: null,

      // UI state
      submitting: false,
      errors: {},
      generalError: null,

      // Temp ID counter for local items
      tempIdCounter: 0,
    };
  },
  computed: {
    canAddIngredient() {
      return this.newIngredient.name.trim() && this.newIngredient.amount && this.newIngredient.unit.trim();
    },
    canAddDirection() {
      return this.newDirection.content?.trim();
    },
    canSubmit() {
      return this.name.trim() && this.type && !this.submitting;
    },
  },
  methods: {
    // === Image Handling ===
    onRecipeImageSelect(event) {
      const file = event.files[0];
      this.image = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    clearRecipeImage() {
      this.image = null;
      this.imagePreview = null;
    },
    onNewDirectionImageSelect(event) {
      const file = event.files[0];
      this.newDirection.image = file;
      this.newDirection.imagePreview = URL.createObjectURL(file);
    },
    clearNewDirectionImage() {
      this.newDirection.image = null;
      this.newDirection.imagePreview = null;
    },
    onDirectionImageSelect(event, index) {
      const file = event.files[0];
      this.directions[index].image = file;
      this.directions[index].imagePreview = URL.createObjectURL(file);
    },

    // === Ingredient Management ===
    addIngredient() {
      this.ingredients.push({
        tempId: ++this.tempIdCounter,
        name: this.newIngredient.name.trim(),
        amount: parseFloat(this.newIngredient.amount),
        unit: this.newIngredient.unit.trim(),
      });
      this.newIngredient = { name: "", amount: "", unit: "" };
    },
    editIngredient(index) {
      this.ingredientBackup = { ...this.ingredients[index] };
      this.editingIngredientIndex = index;
    },
    saveIngredientEdit() {
      this.editingIngredientIndex = null;
      this.ingredientBackup = null;
    },
    cancelIngredientEdit(index) {
      if (this.ingredientBackup) {
        this.ingredients[index] = { ...this.ingredientBackup };
      }
      this.editingIngredientIndex = null;
      this.ingredientBackup = null;
    },
    removeIngredient(index) {
      this.ingredients.splice(index, 1);
    },

    // === Direction Management ===
    addDirection() {
      this.directions.push({
        tempId: ++this.tempIdCounter,
        title: this.newDirection.title?.trim() || null,
        content: this.newDirection.content.trim(),
        image: this.newDirection.image,
        imagePreview: this.newDirection.imagePreview,
      });
      this.newDirection = { title: "", content: "", image: null, imagePreview: null };
    },
    editDirection(index) {
      this.directionBackup = { ...this.directions[index] };
      this.editingDirectionIndex = index;
    },
    saveDirectionEdit() {
      this.editingDirectionIndex = null;
      this.directionBackup = null;
    },
    cancelDirectionEdit(index) {
      if (this.directionBackup) {
        this.directions[index] = { ...this.directionBackup };
      }
      this.editingDirectionIndex = null;
      this.directionBackup = null;
    },
    removeDirection(index) {
      this.directions.splice(index, 1);
    },

    // === Form Submission ===
    async submitRecipe() {
      this.submitting = true;
      this.errors = {};
      this.generalError = null;

      try {
        // Step 1: Create the recipe
        const recipeFormData = new FormData();
        recipeFormData.append("name", this.name);
        recipeFormData.append("description", this.description || "");
        recipeFormData.append("type", this.type);
        if (this.image) {
          recipeFormData.append("image", this.image);
        }

        const recipeResponse = await axios.post(this.route("api.recipes.store"), recipeFormData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        const recipeId = recipeResponse.data.id;

        // Step 2: Add ingredients (sequential to preserve order)
        for (let i = 0; i < this.ingredients.length; i++) {
          const ingredient = this.ingredients[i];
          await axios.post(this.route("api.ingredients.store", { recipe: recipeId }), {
            name: ingredient.name,
            amount: ingredient.amount,
            unit: ingredient.unit,
          });
        }

        // Step 3: Add directions (sequential to preserve order)
        for (let i = 0; i < this.directions.length; i++) {
          const direction = this.directions[i];
          const directionFormData = new FormData();
          directionFormData.append("content", direction.content);
          if (direction.title) {
            directionFormData.append("title", direction.title);
          }
          if (direction.image) {
            directionFormData.append("image", direction.image);
          }

          await axios.post(this.route("api.directions.store", { recipe: recipeId }), directionFormData, {
            headers: { "Content-Type": "multipart/form-data" },
          });
        }

        // Success - redirect to the recipe page
        window.location.href = this.route("recipe.show", { recipe: recipeId });
      } catch (error) {
        this.submitting = false;
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.generalError = "An error occurred while creating the recipe. Please try again.";
          console.error("Error creating recipe:", error);
        }
      }
    },
  },
};
</script>

<style scoped>
.small-container {
  width: 100%;
  max-width: 600px;
  margin: auto;
}
.input {
  width: 100%;
}
table {
  width: 100%;
  margin-bottom: 0.5rem;
}
table th,
table td {
  padding: 0.25rem 0.5rem;
  vertical-align: middle;
}
table th {
  font-weight: 600;
}
table td {
  color: #d1d5db;
}
.handle {
  cursor: move;
}
:deep(.dark-input .p-inputtext) {
  background-color: #1f2937;
  border-color: #4b5563;
  color: white;
}
:deep(.dark-input .p-select) {
  background-color: #1f2937;
  border-color: #4b5563;
  color: white;
}
:deep(.dark-input .p-textarea) {
  background-color: #1f2937;
  border-color: #4b5563;
  color: white;
}
</style>
