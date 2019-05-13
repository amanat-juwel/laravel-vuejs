<template>
    <div class="row mt-5" >
        <div class="col-md-12" v-if="$gate.isAdminOrAuthor()">
            <h1>CATEGORY</h1>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <button class="btn btn-success" @click="newModal">Add New <i class="fas fa-boxes fa-fw"></i></button>
                    </h3>
                    <div class="card-tools">
                        <!-- SEARCH FORM -->
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" @keyup="searchit" v-model="search" type="search" placeholder="Search" aria-label="Search">
                            <div class="input-group-append">
                            <button class="btn btn-navbar" @click="searchit">
                                <i class="fa fa-search"></i>
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Modify</th>
                        </tr>
                        <tr v-for="category in categories.data" :key="category.id">
                            <td>{{category.id}}</td>
                            <td>{{category.name}}</td>
                            <td>
                                <a href="#" @click="editModal(category)">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                /
                                <a href="#" @click="deleteCategory(category.id)">
                                    <i class="fa fa-trash red"></i>
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <pagination :data="categories" @pagination-change-page="getResults"></pagination>
                </div>
                
            </div>
        <!-- /.card -->
        </div>

        <div v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Add New</h5>
                    <h5 class="modal-title" v-show="editmode" id="addNewLabel">Update Category Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editmode ? updateCategory() : createCategory()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                    <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                </div>

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
                editmode: false,
                categories : {},
                search: '',
                form: new Form({
                    id:'',
                    name : ''
                })
            }
        },
        methods:{
            searchit: _.debounce(() => {
                Event.$emit('searching-category');
            },1000), // after 1 sec

            editModal(category){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(category);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            loadCategories(){
                if(this.$gate.isAdminOrAuthor()){
                    this.$Progress.start();
                    axios.get("api/categories").then(({ data }) => (this.categories = data));
                    this.$Progress.finish();
                }
               
            },
            getResults(page = 1) {
                    axios.get('api/categories?page=' + page)
                        .then(response => {
                            this.categories = response.data;
                        });
            },
            createCategory(){
                this.$Progress.start();
                this.form.post('api/categories')
                .then(()=>{
                    Event.$emit('AfterCreateCategory');
                    $('#addNew').modal('hide')
                    Toast.fire({
                        type: 'success',
                        title: 'Category Created successfully'
                        })
                    this.$Progress.finish();
                })
                .catch(()=>{
                })
            },
            updateCategory(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/categories/'+this.form.id)
                .then(() => {
                    // success
                    $('#addNew').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Category has been Updated',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    this.$Progress.finish();
                    Event.$emit('AfterCreateCategory');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            deleteCategory(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        // Send request to the server
                        if (result.value) {
                            this.form.delete('api/categories/'+id).then(()=>{
                                    Swal.fire(
                                    'Deleted!',
                                    'Category has been deleted.',
                                    'success'
                                    )
                                Event.$emit('AfterCreateCategory');
                            }).catch(()=> {
                                Swal.fire("Failed!", "There was something wronge.", "warning");
                            });
                        }
                    })
            },
        },
        created() {
            Event.$on('searching-category',() => {
                //let query = this.$parent.search;
                let query = this.search;
                axios.get('api/findCategory?q=' + query)
                .then((data) => {
                    this.categories = data.data
                })
                .catch(() => {
                })
            })
           this.loadCategories();
           Event.$on('AfterCreateCategory',() => {
               this.loadCategories();
           });
        //    setInterval(() => this.loadCategories(), 3000);
        }
    }
</script>
