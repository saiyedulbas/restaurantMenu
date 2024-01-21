<template>
    <div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createCategoryModalLabel">Create Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Your form elements for creating a category -->
            <form @submit.prevent="createCategory">
              <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" class="form-control" id="categoryName" v-model="newCategoryName" required>
              </div>
              <!-- Add more form fields as needed -->
  
              <button type="submit" class="btn btn-primary">Create Category</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        newCategoryName: '',
        // Add more data properties as needed
      };
    },
    methods: {
      createCategory() {
        // Logic to create a category goes here
        // Access new category name using this.newCategoryName
        // Perform necessary actions (e.g., API request, data manipulation)
  
        // Close the modal after creating the category
        $('#createCategoryModal').modal('hide');
  
        // Optionally, reset the form fields
        this.newCategoryName = '';
      },
    },
  };
  </script>
  
  <style scoped>
  /* Modal Container */
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

  </style>
  