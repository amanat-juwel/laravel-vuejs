<template>
    <div class="col-md-6 mt-4">
        <div class="card card-default">
            <div class="card-header">Task Form</div>

            <div class="card-body">
                <form action="./api/task" method="post" @submit.prevent="addTask()">
                    <div class="form-group">
                        <input autocomplete="off" type="text" v-model="title" class="form-control" name="title" placeholder="Task Title">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info">Add Tasks</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
 
<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data(){
            return{
                title: ''
            }
        },
        methods: {
            addTask(){
                //store data to database
                axios.post('./api/task', {
                    title: this.title
                })
                .then(function () {

                })
                .catch(function (error) {
                    console.log(error);
                });
                
                //emiting an Event(Registered as Global Event in app.js) named 'taskCreated' to notify the changes to other Components
                Event.$emit('taskCreated',{
                    //passing parameter with taskCreated Event
                    title: this.title
                });
                //set the form title null after form submit
                this.title = '';

            }
        },
    }
</script>
