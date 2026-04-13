<template>
  <div class="small-container">
    <Head :title="'Edit ' + recipe.name" />
    <h1 class="mb-3 text-5xl text-white">Edit Recipe</h1>

    <!-- Recipe Name -->
    <FloatLabel class="mb-3" variant="on">
      <InputText class="input dark-input" v-model="form.name" />
      <label>Name</label>
    </FloatLabel>

    <!-- Recipe Description -->
    <FloatLabel class="mb-3" variant="on">
      <TextArea class="input dark-input" rows="4" v-model="form.description" />
      <label>Description</label>
    </FloatLabel>

    <!-- Recipe Type -->
    <FloatLabel class="mb-3" variant="on">
      <Select class="input dark-input" v-model="form.type" :options="recipeTypes" placeholder="Select Type" />
      <label>Recipe Type</label>
    </FloatLabel>
    <Message v-if="errors.name" severity="error" class="mb-2">{{ errors.name[0] }}</Message>

    <!-- Recipe Image -->
    <div class="mb-4">
      <label class="block mb-2 font-semibold text-white">Recipe Image</label>
      <div v-if="imagePreview || recipe.image" class="mb-2 flex items-center gap-2">
        <img :src="imagePreview || recipe.imagePath" class="max-w-48 max-h-48 object-cover rounded" />
        <Button
          v-if="imagePreview"
          icon="pi pi-times"
          severity="danger"
          text
          @click="clearImageChange"
          title="Undo change"
        />
      </div>
      <FileUpload mode="basic" accept="image/*" :auto="false" @select="onImageSelect" chooseLabel="Change Image" />
    </div>

    <!-- Ingredients Section -->
    <h2 class="mb-3 text-3xl text-white">Ingredients</h2>
    <table v-if="localIngredients.length > 0">
      <thead>
        <tr>
          <th class="w-8"></th>
          <th class="text-left text-white">Name</th>
          <th class="text-left text-white">Qty</th>
          <th class="text-left text-white">Unit</th>
          <th class="w-24"></th>
        </tr>
      </thead>
      <draggable v-model="localIngredients" tag="tbody" item-key="id" handle=".handle" @end="onIngredientReorder">
        <template #item="{ element: ingredient, index }">
          <tr :class="{ 'bg-green-50': ingredient._new }">
            <td class="handle cursor-move"><i class="pi pi-bars"></i></td>
            <template v-if="editingIngredientId === ingredient.id">
              <td><InputText v-model="ingredient.name" class="w-full" size="small" /></td>
              <td><InputText v-model="ingredient.amount" type="number" step="any" class="w-16" size="small" /></td>
              <td><InputText v-model="ingredient.unit" class="w-20" size="small" /></td>
              <td class="flex gap-1">
                <Button
                  icon="pi pi-check"
                  text
                  size="small"
                  @click="saveIngredient(ingredient)"
                  :loading="ingredient._saving"
                />
                <Button
                  icon="pi pi-times"
                  text
                  size="small"
                  severity="secondary"
                  @click="cancelIngredientEdit(ingredient)"
                />
              </td>
            </template>
            <template v-else>
              <td>{{ ingredient.name }}</td>
              <td>{{ ingredient.amount }}</td>
              <td>{{ ingredient.unit }}</td>
              <td class="flex gap-1">
                <Button icon="pi pi-pencil" text size="small" @click="editIngredient(ingredient)" />
                <Button
                  icon="pi pi-trash"
                  text
                  size="small"
                  severity="danger"
                  @click="deleteIngredient(ingredient)"
                  :loading="ingredient._deleting"
                />
              </td>
            </template>
          </tr>
        </template>
      </draggable>
    </table>
    <p v-else class="text-gray-500 mb-3">No ingredients yet.</p>

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
      <Button
        icon="pi pi-plus"
        size="small"
        @click="addIngredient"
        :disabled="!canAddIngredient"
        :loading="addingIngredient"
      />
    </div>

    <!-- Directions Section -->
    <h2 class="mb-3 text-3xl text-white">Directions</h2>
    <div v-if="localDirections.length > 0" class="directions-list mb-3">
      <draggable v-model="localDirections" item-key="id" handle=".handle" @end="onDirectionReorder">
        <template #item="{ element: direction, index }">
          <div
            class="direction-item border border-gray-700 rounded p-3 mb-2 bg-gray-800 text-white"
            :class="{ 'bg-green-900': direction._new }"
          >
            <div class="flex items-start gap-2">
              <div class="handle cursor-move pt-1"><i class="pi pi-bars"></i></div>
              <div class="flex-1">
                <template v-if="editingDirectionId === direction.id">
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
                      @select="(e) => onDirectionImageSelect(e, direction)"
                      chooseLabel="Image"
                      class="p-button-sm"
                    />
                    <img
                      v-if="direction._imagePreview || direction.image"
                      :src="direction._imagePreview || direction.imagePath"
                      class="w-16 h-16 object-cover rounded"
                    />
                  </div>
                  <div class="flex gap-1">
                    <Button
                      label="Save"
                      icon="pi pi-check"
                      size="small"
                      @click="saveDirection(direction)"
                      :loading="direction._saving"
                    />
                    <Button
                      label="Cancel"
                      icon="pi pi-times"
                      size="small"
                      severity="secondary"
                      @click="cancelDirectionEdit(direction)"
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
                      <Button icon="pi pi-pencil" text size="small" @click="editDirection(direction)" />
                      <Button
                        icon="pi pi-trash"
                        text
                        size="small"
                        severity="danger"
                        @click="deleteDirection(direction)"
                        :loading="direction._deleting"
                      />
                    </div>
                  </div>
                  <img v-if="direction.image" :src="direction.imagePath" class="mt-2 max-w-32 rounded" />
                </template>
              </div>
            </div>
          </div>
        </template>
      </draggable>
    </div>
    <p v-else class="text-gray-500 mb-3">No directions yet.</p>

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
        <Button
          label="Add Direction"
          icon="pi pi-plus"
          @click="addDirection"
          :disabled="!canAddDirection"
          :loading="addingDirection"
        />
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center">
      <Button
        label="Discard Changes"
        icon="pi pi-undo"
        severity="secondary"
        as="a"
        :href="route('recipe.show', { recipe: recipe.id })"
      />
      <!-- Save Recipe Button -->
      <div class="flex gap-2">
        <Button label="Save Recipe" icon="pi pi-check" :loading="savingRecipe" @click="saveRecipe" />
        <span v-if="recipeSaved" class="text-green-600 flex items-center"
          ><i class="pi pi-check-circle mr-1"></i> Saved</span
        >
      </div>
      <Button label="Delete Recipe" severity="danger" icon="pi pi-trash" @click="confirmDelete" />
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="showDeleteDialog" header="Delete Recipe" :modal="true" :style="{ width: '400px' }">
      <p class="text-white">Are you sure you want to delete "{{ recipe.name }}"?</p>
      <p class="text-gray-500 text-sm mt-2">This action cannot be undone.</p>
      <template #footer>
        <Button label="Cancel" severity="secondary" @click="showDeleteDialog = false" />
        <Button label="Delete" severity="danger" @click="deleteRecipe" :loading="deletingRecipe" />
      </template>
    </Dialog>
  </div>
</template>

<script>
import Button from "primevue/button";
import Dialog from "primevue/dialog";
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
    Dialog,
    draggable,
    FileUpload,
    FloatLabel,
    Head,
    InputText,
    Message,
    Select,
    TextArea,
  },
  props: {
    recipe: Object,
  },
  data() {
    return {
      // Form data
      form: {
        name: this.recipe.name,
        description: this.recipe.description || "",
        type: this.recipe.type,
      },
      originalForm: null,

      // Image handling
      newImage: null,
      imagePreview: null,

      // Recipe types
      recipeTypes: ["Entree", "Side", "Dessert", "Drink"],

      // Local copies for editing
      localIngredients: [],
      localDirections: [],

      // Edit state
      editingIngredientId: null,
      editingDirectionId: null,
      ingredientBackup: null,
      directionBackup: null,

      // New item forms
      newIngredient: { name: "", amount: "", unit: "" },
      newDirection: { title: "", content: "", image: null, imagePreview: null },

      // Loading states
      savingRecipe: false,
      deletingRecipe: false,
      addingIngredient: false,
      addingDirection: false,

      // UI state
      showDeleteDialog: false,
      recipeSaved: false,
      errors: {},
    };
  },
  computed: {
    canAddIngredient() {
      return this.newIngredient.name?.trim() && this.newIngredient.amount && this.newIngredient.unit?.trim();
    },
    canAddDirection() {
      return this.newDirection.content?.trim();
    },
  },
  methods: {
    // === Image Handling ===
    onImageSelect(event) {
      const file = event.files[0];
      this.newImage = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    clearImageChange() {
      this.newImage = null;
      this.imagePreview = null;
    },
    onDirectionImageSelect(event, direction) {
      const file = event.files[0];
      direction._newImage = file;
      direction._imagePreview = URL.createObjectURL(file);
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

    // === Recipe Save ===
    async saveRecipe() {
      this.savingRecipe = true;
      this.errors = {};
      this.recipeSaved = false;

      try {
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("description", this.form.description || "");
        formData.append("type", this.form.type);
        if (this.newImage) {
          formData.append("image", this.newImage);
        }

        // Laravel requires _method for PUT with FormData
        formData.append("_method", "PUT");

        await axios.post(this.route("api.recipes.update", { recipe: this.recipe.id }), formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        // Save ingredient order (only if there are ingredients)
        if (this.localIngredients.length > 0) {
          const ingredientOrders = this.localIngredients.map((ingredient, index) => ({
            id: ingredient.id,
            order: index,
          }));
          await axios.put(
            this.route("api.ingredients.update-orders", {
              recipe: this.recipe.id,
            }),
            { ingredients: ingredientOrders }
          );
        }

        // Save direction order (only if there are directions)
        if (this.localDirections.length > 0) {
          const directionOrders = this.localDirections.map((direction, index) => ({
            id: direction.id,
            order: index,
          }));
          await axios.put(
            this.route("api.directions.update-orders", {
              recipe: this.recipe.id,
            }),
            { directions: directionOrders }
          );
        }

        // Update original form to reflect saved state
        this.originalForm = { ...this.form };
        this.newImage = null;
        this.recipeSaved = true;

        // Hide saved message after 3 seconds
        setTimeout(() => {
          this.recipeSaved = false;
        }, 3000);
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          console.error("Error saving recipe:", error);
        }
      } finally {
        this.savingRecipe = false;
      }
    },

    // === Ingredient Management ===
    editIngredient(ingredient) {
      this.ingredientBackup = { ...ingredient };
      this.editingIngredientId = ingredient.id;
    },
    cancelIngredientEdit(ingredient) {
      if (this.ingredientBackup) {
        Object.assign(ingredient, this.ingredientBackup);
      }
      this.editingIngredientId = null;
      this.ingredientBackup = null;
    },
    async saveIngredient(ingredient) {
      ingredient._saving = true;

      try {
        await axios.put(
          this.route("api.ingredients.update", {
            recipe: this.recipe.id,
            ingredient: ingredient.id,
          }),
          {
            name: ingredient.name,
            amount: parseFloat(ingredient.amount),
            unit: ingredient.unit,
          }
        );

        this.editingIngredientId = null;
        this.ingredientBackup = null;
      } catch (error) {
        console.error("Error saving ingredient:", error);
      } finally {
        ingredient._saving = false;
      }
    },
    async addIngredient() {
      this.addingIngredient = true;

      try {
        const response = await axios.post(this.route("api.ingredients.store", { recipe: this.recipe.id }), {
          name: this.newIngredient.name.trim(),
          amount: parseFloat(this.newIngredient.amount),
          unit: this.newIngredient.unit.trim(),
        });

        this.localIngredients.push({ ...response.data, _new: true });
        this.newIngredient = { name: "", amount: "", unit: "" };

        // Remove _new flag after a moment
        setTimeout(() => {
          const added = this.localIngredients.find((i) => i.id === response.data.id);
          if (added) added._new = false;
        }, 2000);
      } catch (error) {
        console.error("Error adding ingredient:", error);
      } finally {
        this.addingIngredient = false;
      }
    },
    async deleteIngredient(ingredient) {
      ingredient._deleting = true;

      try {
        await axios.delete(
          this.route("api.ingredients.delete", {
            recipe: this.recipe.id,
            ingredient: ingredient.id,
          })
        );

        this.localIngredients = this.localIngredients.filter((i) => i.id !== ingredient.id);
      } catch (error) {
        console.error("Error deleting ingredient:", error);
        ingredient._deleting = false;
      }
    },
    async onIngredientReorder() {
      // Just update local order - will be saved with recipe
    },

    // === Direction Management ===
    editDirection(direction) {
      this.directionBackup = { ...direction };
      this.editingDirectionId = direction.id;
    },
    cancelDirectionEdit(direction) {
      if (this.directionBackup) {
        Object.assign(direction, this.directionBackup);
        direction._imagePreview = null;
        direction._newImage = null;
      }
      this.editingDirectionId = null;
      this.directionBackup = null;
    },
    async saveDirection(direction) {
      direction._saving = true;

      try {
        const formData = new FormData();
        formData.append("content", direction.content);
        if (direction.title) {
          formData.append("title", direction.title);
        }
        if (direction._newImage) {
          formData.append("image", direction._newImage);
        }

        // Laravel requires _method for PUT with FormData
        formData.append("_method", "PUT");

        const response = await axios.post(
          this.route("api.directions.update", {
            recipe: this.recipe.id,
            direction: direction.id,
          }),
          formData,
          { headers: { "Content-Type": "multipart/form-data" } }
        );

        // Update with response data
        Object.assign(direction, response.data);
        direction._imagePreview = null;
        direction._newImage = null;

        this.editingDirectionId = null;
        this.directionBackup = null;
      } catch (error) {
        console.error("Error saving direction:", error);
      } finally {
        direction._saving = false;
      }
    },
    async addDirection() {
      this.addingDirection = true;

      try {
        const formData = new FormData();
        formData.append("content", this.newDirection.content.trim());
        if (this.newDirection.title) {
          formData.append("title", this.newDirection.title.trim());
        }
        if (this.newDirection.image) {
          formData.append("image", this.newDirection.image);
        }

        const response = await axios.post(this.route("api.directions.store", { recipe: this.recipe.id }), formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        this.localDirections.push({ ...response.data, _new: true });
        this.newDirection = {
          title: "",
          content: "",
          image: null,
          imagePreview: null,
        };

        // Remove _new flag after a moment
        setTimeout(() => {
          const added = this.localDirections.find((d) => d.id === response.data.id);
          if (added) added._new = false;
        }, 2000);
      } catch (error) {
        console.error("Error adding direction:", error);
      } finally {
        this.addingDirection = false;
      }
    },
    async deleteDirection(direction) {
      direction._deleting = true;

      try {
        await axios.delete(
          this.route("api.directions.delete", {
            recipe: this.recipe.id,
            direction: direction.id,
          })
        );

        this.localDirections = this.localDirections.filter((d) => d.id !== direction.id);
      } catch (error) {
        console.error("Error deleting direction:", error);
        direction._deleting = false;
      }
    },
    async onDirectionReorder() {
      // Just update local order - will be saved with recipe
    },

    // === Delete Recipe ===
    confirmDelete() {
      this.showDeleteDialog = true;
    },
    async deleteRecipe() {
      this.deletingRecipe = true;

      try {
        await axios.delete(this.route("api.recipes.delete", { recipe: this.recipe.id }));
        window.location.href = this.route("recipes.index");
      } catch (error) {
        console.error("Error deleting recipe:", error);
        this.deletingRecipe = false;
      }
    },
  },
  created() {
    // Store original form values for change detection
    this.originalForm = { ...this.form };

    // Initialize local copies of ingredients and directions
    this.localIngredients = [...this.recipe.orderedIngredients];
    this.localDirections = [...this.recipe.orderedDirections];
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
