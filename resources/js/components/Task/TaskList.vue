
<template>
    <div class="col-md-6 mt-4">
        <div class="card card-default">
            <div class="card-header">Task List</div>

            <div class="card-body">
                <ul>
                    <li v-for="task in tasks" :key="task.id">{{ task.title }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>
 
<script>
    export default {

        data(){
            return{
                tasks: []
            }
        },
        created(){
            //get task data from database
            axios.get('./api/task')
            .then(response => this.tasks = response.data);

            //listening for taskCreated Event
            Event.$on('taskCreated', (title)=> {
                //push the emitted task into the tasks array
                this.tasks.push(title);
            })
            
        }
    }
</script>
