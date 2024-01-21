<template>
  <div class="center-container">
      <div class="form-container">
        <!-- Section 1: Create Category -->
        <div v-if="currentSection === 'createCategory'" >
          <div class="container">
              <h2>Categories</h2>
              
          </div>
          
          <!-- Your form elements for creating a category -->
          <form @submit.prevent="createCategory" class="top-div">
              <ul class="category-list">
                  <li v-for="category in categories" :key="category.id" @click="loadLevel1Subcategories(category.id)">
                  <div class="category-item">
                      <span class="item">{{ category.data }}</span>
                      <div v-if="category.discount !== 0" class="green-egg-tag">{{ category.discount }}%</div>
                      <div v-if="category.id === selectedCategoryId && loadingSection === 'level1Subcategories'" class="loader"></div>
                      <div v-if="category.type === 'item'" class="red-tag">Item</div>
                  </div>
                  </li>
              </ul>
          <!-- <button @click="showDiscountSection">Next: Create Discount</button> -->
          </form>
      </div>


      <!-- <div v-if="currentSection === 'loading'" class="loader"></div> -->
        <div v-if="currentSection === 'level1Subcategories'">
          <div class="container">
              <h2>Sub Categories</h2>
              <div class="icon-container">
                    <i class="fas fa-arrow-left" @click="showCreateCategorySection" title="Back To Categories"></i> <!-- Back Icon -->
                </div>
          </div>
          <!-- Your form elements for creating a category -->

          <form @submit.prevent="createCategory">
          <ul v-if="currentSection === 'level1Subcategories'">
              <li v-for="category in level1Subcategories" :key="category.id" @click="category.type !== 'item' ? loadLevel1Subcategories(category.id) : null">
              <div class="category-item">
                  <span class="item">{{ category.data }}</span>
                  <div v-if="category.discount !== 0" class="green-egg-tag">{{category.discount}}%</div>
                  <div v-if="category.id === selectedCategoryId && loadingSection === 'level1Subcategories'" class="loader"></div>
                  <div class="tag-and-icon" v-if="category.type === 'item'">
                       <div class="red-tag">Item</div>
                  </div>
                  

              </div>
              </li>
          </ul>
          </form>
          
        </div>
  
      </div>
  </div>
</template>

<script>
import axios from 'axios';
import {useToast} from 'vue-toast-notification';
  import 'vue-toast-notification/dist/theme-sugar.css';
  const $toast = useToast();

export default {
  data() {
    return {
      currentSection: 'createCategory',
      categories: [],
      level1Subcategories: [],
      level2Subcategories: [],
      loadingSection: '',
      discountValue: 0,
      newCategoryName: '',
      selectedCategoryId: null,
      type:'',
      currentCategoryId: null,
      showModals: false,
      showDiscountModals: false,
      selectedCategoryType: 'category',
      };
  },
  mounted() {
      // Fetch initial parent categories from the backend
      this.fetchCategories();
  },
  methods: {

  async  fetchCategories() {
      try {
          // Replace the URL with your actual API endpoint
          const response = await axios.get(`http://localhost:8000/api/categories`);

          this.categories = response.data;
          this.type = this.categories[0].type;
          this.currentCategoryId = null;
          console.log("current id: ",this.currentCategoryId);

      } catch (error) {
          console.error('Error fetching level_1 subcategories:', error);
          // Handle error as needed
      }
  
  },
  async loadLevel1Subcategories(categoryId) {
      this.selectedCategoryId = categoryId;
      this.loadingSection = 'level1Subcategories';
      this.currentCategoryId = categoryId;

      console.log("current id: ",this.currentCategoryId);

  // Simulate API call to fetch level_1 subcategories based on the selected category
  // Replace this with your actual API endpoint
  // Update level1Subcategories data with the fetched data
  try {
          // Replace the URL with your actual API endpoint
          const response = await axios.get(`http://localhost:8000/api/categories/${categoryId}`);

          this.level1Subcategories = response.data.categories;
          
          this.currentSection = 'level1Subcategories';
          this.selectedCategoryId = null;
          this.type = this.level1Subcategories[0].type;
          // console.log('the type is: ',this.type);
      } catch (error) {
          console.error('Error fetching level_1 subcategories:', error);
          // Handle error as needed
      }

      // Switch to level_1 subcategories section
  

  // Switch to level_1 subcategories section
  },
   
    showCreateCategorySection() {
      this.currentSection = 'createCategory';
      this.fetchCategories();
    },
    
  },
};
</script>

<style scoped>
.icon-container {
  display: flex;
  justify-content: space-between;
  width: 13%;
  cursor: pointer;
}
.green-egg-tag {
background-color: #4caf50; /* Green color */
color: #fff; /* Text color */
border-radius: 50%; /* Circular shape */
padding: 4px 8px; /* Adjust padding as needed */
position: absolute;
top: 0;
left: 0;
transform: translate(-50%, -50%); /* Center the tag */
z-index: 1; /* Ensure the tag is on top of other elements */
}
.tag-and-icon {
    /* display: block; */
    width: 69px;
  display: flex;
  align-items: center;
  /* margin-right: 9px; */
  justify-content: space-between; /* Center items vertically */
}
/* Modal Container */


label {
display: block;
font-weight: bold;
margin-bottom: 8px;
color: #333;
}



/* Responsive */
@media (max-width: 768px) {
.modal-dialog {
  width: 90%;
}
}


.category-list {
list-style: none;
padding: 0;
margin: 0;
}

.category-item {
display: flex;
align-items: center;
justify-content: space-between;
border-bottom: 1px solid #ccc;
padding: 12px;
transition: background-color 0.3s ease;
cursor: pointer; /* Add cursor pointer */
}

.category-item:hover {
background-color: #f9f9f9;
}

.item {
font-size: 16px;
color: #333;
font-weight: bold;
}
.container {
display: flex;
margin-left: -14px;
width: 105%;
margin-bottom: 16px;
justify-content: space-between;
align-items: center; /* Optional: align items in the center vertically */
}

.container button {
background-color: #4caf50;
color: #fff;
padding: 8px 16px;
border: none;
border-radius: 4px;
cursor: pointer;
}

.container button:hover {
background-color: #45a049;
}
.category-item {
position: relative;
}

.item {
color: black; /* Set your preferred text color */
}

.loader {
display: inline-block;
border: 4px solid rgba(0, 0, 0, 0.1);
border-left: 4px solid #3498db;
border-radius: 50%;
width: 20px;
height: 20px;
animation: spin 1s linear infinite;
}

.red-tag {
/* position: absolute; */
top: 0;
right: 0;
background-color: red;
color: white;
padding: 4px 8px;
border-radius: 4px;
}

@keyframes spin {
0% { transform: rotate(0deg); }
100% { transform: rotate(360deg); }
}
.center-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}
body {
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f4f4;
}

.form-container {
  background-color: #fff;
  padding: 26px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 40%;
  height: 75%;
  overflow: auto;
}
.top-div {
display: flex;
flex-direction: column;
justify-content: space-between;
height: 100%; /* Make sure top-div takes full height of form-container */
}

h2 {
  color: #333;
}

button {
  background-color: #4caf50;
  color: #fff;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 10px;
}

button:hover {
  background-color: #45a049;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  margin-bottom: 8px;
}
</style>
