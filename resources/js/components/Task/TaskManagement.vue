
<template>
  <div class="row">
    <task-form></task-form>
	<task-list></task-list>
  </div>  
</template>
 
<script>
  //import your components here
  import TaskForm from './TaskForm.vue'
  import TaskList from './TaskList.vue'
  export default {
  	
    components: {
      'task-form': TaskForm,
      'task-list' : TaskList
    }
    
 }   
</script>
