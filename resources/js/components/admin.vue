<template>
    <div class="center-container">
        <div class="form-container">
          <!-- Section 1: Create Category -->
          <div v-if="currentSection === 'createCategory'" >
            <div class="container">
                <h2>Categories</h2>
                <div class="icon-container">
                    <i class="fas fa-arrow-left" @click="showCreateCategorySection" title="Back To Categories"></i> <!-- Back Icon -->
                    <i v-if="type === 'item'" class="fas fa-plus" @click="showModal" title="Add Item"></i> <!-- Add Item Icon -->
                    <i v-if="type === 'category'" class="fas fa-plus" @click="showModal" title="Add Category"></i> <!-- Add Category Icon -->
                    <i v-if="type === ''" class="fas fa-plus" @click="showModal" title="Add Category or Item"></i> <!-- Add Category Icon -->
                </div>
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
                        <i class="fas fa-percent" @click.stop="showDiscountModal(category.id)" title="Add Discount"></i> <!-- Add Discount Icon -->
                    </div>
                    </li>
                </ul>
            <!-- <button @click="showDiscountSection">Next: Create Discount</button> -->
            </form>
        </div>

        <!-- modal -->
        <div class="modal" :class="{ 'modal': showModals, 'show': showModals }" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Create Category</h5>
                <button type="button" class="close" @click="closeModal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Your form elements for creating a category -->
                <form @submit.prevent="createCategory">
                <div class="form-group">
                    <label for="categoryName">Category Name</label>
                    <input type="text" class="form-control" id="categoryName" v-model="newCategoryName" required>
                    <label for="categoryType">Category Type</label>
                    <select class="form-control" id="categoryType" v-model="selectedCategoryType">
                      <option v-if="type !== 'category'" value="item">Item</option>
                        <option v-if="type !== 'item' " value="category">Category</option>
                    </select>
                </div>
                <!-- Add more form fields as needed -->

                <button type="submit" class="btn btn-primary">Create Category</button>
                </form>
            </div>
            </div>
        </div>
        </div>

        <div class="modal" :class="{ 'modal': showDiscountModals, 'show': showDiscountModals }" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Create Discount</h5>
                <button type="button" class="close" @click="closeDiscountModal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Your form elements for creating a category -->
                <form @submit.prevent="createDiscount">
                <div class="form-group">
                    <label for="categoryName">Discount Value</label>
                    <input type="number" class="form-control" id="categoryName" v-model="discountValue" required>
                </div>
                <!-- Add more form fields as needed -->

                <button type="submit" class="btn btn-primary">Create Discount</button>
                </form>
            </div>
            </div>
        </div>
        </div>

        <!-- modal end -->


        <!-- <div v-if="currentSection === 'loading'" class="loader"></div> -->
          <div v-if="currentSection === 'level1Subcategories'">
            <div class="container">
                <h2>Sub Categories</h2>
                <div class="icon-container">
                    <i class="fas fa-arrow-left" @click="showCreateCategorySection" title="Back To Categories"></i> <!-- Back Icon -->
                    <i v-if="type === 'item'" class="fas fa-plus" @click="showModal" title="Add Item"></i> <!-- Add Item Icon -->
                    <i v-if="type === 'category'" class="fas fa-plus" @click="showModal" title="Add Category"></i> <!-- Add Category Icon -->
                    <i v-if="type === ''" class="fas fa-plus" @click="showModal" title="Add Category or Item"></i> <!-- Add Category Icon -->
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
                          <i class="fas fa-percent" @click.stop="showDiscountModal(category.id)" title="Add Discount"></i>
                    </div>
                    <!-- <div v-if="category.type === 'item'" class="red-tag">Item</div> Add the red tag here -->
                    <i v-if="category.type !== 'item'" class="fas fa-percent" @click.stop="showDiscountModal(category.id)" title="Add Discount"></i> <!-- Add Discount Icon -->

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
    showModal() {
        this.showModals = true;
    },
    closeModal() {
        this.showModals = false;
    },
    showDiscountModal(categoryId) {
        this.showDiscountModals = true;
        this.currentCategoryId = categoryId;
    },
    closeDiscountModal() {
        this.showDiscountModals = false;
        this.currentCategoryId = null;
    },
    async  fetchCategories() {
        try {
            // Replace the URL with your actual API endpoint
            const response = await axios.get(`http://localhost:8000/api/categories`);

            this.categories = response.data;
            this.type = this.categories[0].type;
            this.currentCategoryId = null;
            console.log("current id: ",this.currentCategoryId);

        } catch (error) {
            this.type = '';
            console.error('Error fetching level_1 subcategories:', error);
            // Handle error as needed
        }
    
    },
    async loadLevel1Subcategories(categoryId) {
      this.type = '';
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
            if (this.level1Subcategories.length > 0 && 'type' in this.level1Subcategories[0]) {
                this.type = this.level1Subcategories[0].type;
            }
            console.log("the type is: ",this.type)
            // console.log('the type is: ',this.type);
        } catch (error) {
            console.error('Error fetching level_1 subcategories:', error);
            // Handle error as needed
        }

        // Switch to level_1 subcategories section
    

    // Switch to level_1 subcategories section
    },
      async createCategory() {
        try {
            let parent = 0;
            if(this.currentCategoryId==null){
                parent = 0;
            }else{
                parent =  this.currentCategoryId;
            }
            const categoryPayload = {
                data: this.newCategoryName,
                type: this.selectedCategoryType,
                parent: parent,
                isSelect: 0,
                discount: 0,
            };
            // Replace the URL with your actual API endpoint
            const response = await axios.post(`http://localhost:8000/api/categories`, categoryPayload);

           

            this.newCategoryName = '';
            this.selectedCategoryType = '';
            this.currentSection = 'createCategory';
            this.fetchCategories();
            this.$toast.success('Category saved.', {
                duration: 3000,
                dismissible: true,
            })
            this.showModals =false;
            // console.log('the type is: ',this.type);
        } catch (error) {
            console.error('Error:', error);
            this.$toast.error(`Error: ${error.response.data.message}`, {
                duration: 3000,
                dismissible: true,
            })
            // Handle error as needed
        }
        this.categories.push(categoryPayload);
        // Reset form or perform other actions as needed
      },
      async createDiscount() {
        console.log("discount");
        const discountPayload = {
                discount: this.discountValue,
            };
        try {
          
            // Replace the URL with your actual API endpoint
            const response = await axios.post(`http://localhost:8000/api/discount/${this.currentCategoryId}`,discountPayload);

        
            this.currentSection = 'createCategory';
            this.fetchCategories();
            this.$toast.success('Discount saved.', {
                duration: 3000,
                dismissible: true,
            })
            this.closeDiscountModal()
            this.discountValue=0;
            // console.log('the type is: ',this.type);
        } catch (error) {
            console.error('Error:', error);
            this.$toast.error('Something went wrong.', {
                duration: 3000,
                dismissible: true,
            })
            // Handle error as needed
        }
        // Reset form or perform other actions as needed
      },
      showCreateCategorySection() {
        this.currentSection = 'createCategory';
        this.fetchCategories();
      },
      
    },
  };
  </script>
  
  <style scoped>

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
.show {
    display: block !important;
}
  .modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
}

/* Modal Content */
.modal-dialog {
  margin: 50px auto;
  max-width: 600px;
}

.modal-content {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

/* Modal Header */
.modal-header {
  padding: 15px;
  border-bottom: 1px solid #ddd;
}

.modal-title {
  font-size: 20px;
  font-weight: bold;
  color: #333;
}

/* Modal Body */
.modal-body {
  padding: 20px;
}

/* Form Elements */
.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 8px;
  color: #333;
}

.input-group {
  width: 100%;
}

.form-control {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  transition: border-color 0.3s;
}

.form-control:focus {
  outline: none;
  border-color: #3498db;
}

/* Modal Footer (Optional) */
.modal-footer {
  padding: 15px;
  border-top: 1px solid #ddd;
  text-align: right;
}

.btn-primary {
  background-color: #3498db;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primary:hover {
  background-color: #2980b9;
}

/* Close Button */
.close {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  cursor: pointer;
}

.close:hover {
  color: #e74c3c;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-dialog {
    width: 90%;
  }
}

.icon-container {
  display: flex;
  justify-content: space-between;
  width: 13%;
  cursor: pointer;
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
  