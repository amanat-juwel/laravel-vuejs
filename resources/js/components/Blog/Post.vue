<template>
    <div class="row mt-5" >
        <div class="col-md-12" v-if="$gate.isAdminOrAuthor()">
            <h1>POST</h1>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <button class="btn btn-success" @click="newModal">Add New <i class="fas fa-clipboard-list fa-fw"></i></button>
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
                            <th>Title</th>
                            <th>Body</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Modify</th>
                        </tr>
                        <tr v-for="post in posts.data" :key="post.id">
                            <td>{{post.id}}</td>
                            <td>{{post.title}}</td>
                            <td>{{post.body}}</td>
                            <td>{{post.image}}</td>
                            <td>{{post.category}}</td>
                            <td>
                                <a href="#" @click="editModal(post)">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                /
                                <a href="#" @click="deletePost(post.id)">
                                    <i class="fa fa-trash red"></i>
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <pagination :data="posts" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="editmode" id="addNewLabel">Update Post Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editmode ? updatePost() : createPost()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.title" type="text" name="title"
                            placeholder="Title"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                        <has-error :form="form" field="title"></has-error>
                    </div>
                    <div class="form-group">
                        <textarea v-model="form.body" name="body" id="body"
                        placeholder="Short body for user (Optional)"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('body') }"></textarea>
                        <has-error :form="form" field="body"></has-error>
                    </div>
                    <div class="form-group">
                        <select name="category_id" v-model="form.category_id" id="category_id" class="form-control" :class="{ 'is-invalid': form.errors.has('category_id') }">
                            <option v-for="category in categories.data" :key="category.id" :value="category.id">{{category.name}}</option>
                            
                        </select>
                        <has-error :form="form" field="category_id"></has-error>
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
                posts : {},
                categories : {},
                search: '',
                form: new Form({
                    id:'',
                    title : '',
                    body : '',
                    image : '',
                    category_id : ''
                })
            }
        },
        methods:{
            searchit: _.debounce(() => {
                Event.$emit('searching-post');
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
            loadPost(){
                if(this.$gate.isAdminOrAuthor()){
                    this.$Progress.start();
                    axios.get("api/posts").then(({ data }) => (this.posts = data));
                    this.$Progress.finish();
                }
               
            },
            loadCategory(){
                if(this.$gate.isAdminOrAuthor()){
                    axios.get("api/categories").then(({ data }) => (this.categories = data));
                }
               
            },
            getResults(page = 1) {
                    axios.get('api/posts?page=' + page)
                        .then(response => {
                            this.posts = response.data;
                        });
            },
            createPost(){
                this.$Progress.start();
                this.form.post('api/posts')
                .then(()=>{
                    Event.$emit('AfterCreatePost');
                    $('#addNew').modal('hide')
                    Toast.fire({
                        type: 'success',
                        title: 'Post Created successfully'
                        })
                    this.$Progress.finish();
                })
                .catch(()=>{
                })
            },
            updatePost(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/posts/'+this.form.id)
                .then(() => {
                    // success
                    $('#addNew').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Post has been Updated',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    this.$Progress.finish();
                    Event.$emit('AfterCreatePost');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            deletePost(id){
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
                            this.form.delete('api/posts/'+id).then(()=>{
                                    Swal.fire(
                                    'Deleted!',
                                    'Post has been deleted.',
                                    'success'
                                    )
                                Event.$emit('AfterCreatePost');
                            }).catch(()=> {
                                Swal.fire("Failed!", "There was something wronge.", "warning");
                            });
                        }
                    })
            },
        },
        created() {
            Event.$on('searching-post',() => {
                //let query = this.$parent.search;
                let query = this.search;
                axios.get('api/findPost?q=' + query)
                .then((data) => {
                    this.posts = data.data
                })
                .catch(() => {
                })
            })
           this.loadPost();
           this.loadCategory();
           Event.$on('AfterCreatePost',() => {
               this.loadPost();
           });
        //    setInterval(() => this.loadPost(), 3000);
        }
    }
</script>
