<template>
  <div class="box box-primary dashboard-widget-box">
    <div class="box-header">
      <i class="fa fa-list-ul"></i>
      <h3 class="box-title">{{lang('todo')}}<sup style="padding-left: 3px"><span class="label label-info">Beta</span></sup></h3>
      <div style="margin-top:1rem;" class="row">
        <div :class="editMode ? 'col-md-9' : 'col-md-12'">
          <input class="form-control" autofocus autocomplete="off" :placeholder="lang('todo_placeholder')"
            v-model.trim="newTodoText" @keyup.enter="editMode ? editTodoName() : addNewTodo()">
        </div>
        <div class="col-md-3">
          <button v-if="editMode" type="button" class="btn btn-default pull-right" title="Cancel editing"
            @click="cancelEditMode"><i class="fa fa-times"></i>{{lang('cancel')}}</button>
        </div>
      </div>
    </div>

    <!-- Show quote message if todo list is empty  -->
    <div class="no-todo-section" v-if="todoList.length === 0">
      <blockquote>&quot; {{lang('todo_quote')}} &quot;</blockquote>
    </div>

    <!-- Show todo list if list is not empty -->
    <div class="box-body scrollable-area" v-else>
      <draggable-element class="list-group" tag="ul" v-model="todoList" @change="onReorder">
        <transition-group>
          <li v-for="(todo, index) in todoList" :key="todo.id" class="list-group-item">

            <div class="row">
              <div class="col-xs-9">
                <!-- Todo marker -->
                <span class="todo-mark" v-show="todo.status==='pending'" @click="onTodoMarkClick(index)" :title="getTodoMarkerTitle('pending')"><i class="fa fa-clock-o fa-lg" aria-hidden="true" style="color:red"></i></span>
                <span class="todo-mark" v-show="todo.status==='in-progress'" @click="onTodoMarkClick(index)" :title="getTodoMarkerTitle('in-progress')"><i class="fa fa-check-circle-o fa-lg" aria-hidden="true" style="color:orange"></i></span>
                <span class="todo-mark" v-show="todo.status==='completed'" @click="onTodoMarkClick(index)" :title="getTodoMarkerTitle('completed')"><i class="fa fa-check-circle-o fa-lg" aria-hidden="true" style="color:green"></i></span>

                <!-- Todo name -->
                <span class="text todo-text" :style="todo.status==='completed' ? {'text-decoration': 'line-through'} : {}">{{todo.name}}</span>

                <!-- Todo status -->
                <span class="label label-danger text-lowercase" v-show="todo.status==='pending'">{{lang('pending')}}</span>
                <span class="label label-warning text-lowercase" v-show="todo.status==='in-progress'">{{lang('in_progress')}}</span>
                <span class="label label-success text-lowercase" v-show="todo.status==='completed'">{{lang('completed')}}</span>
              </div>

              <!-- Todo actions -->
              <div class="todo-tools col-xs-3">
                <i class="dropdown-toggle fa fa-trash-o pull-right" @click="deleteTodo(todo.id, index)" style="cursor:pointer; color:red;" title="Remove" aria-hidden="true"></i>
                <i class="dropdown-toggle fa fa-pencil-square-o pull-right" @click="onEditTodo(index)" style="cursor:pointer" title="Edit" aria-hidden="true"></i>
              </div>
            </div>
          </li>
        </transition-group>
      </draggable-element>
    </div>
  </div>
</template>

<script type="text/javascript">

import axios from 'axios';
import draggable from 'vuedraggable'
import {errorHandler, successHandler} from 'helpers/responseHandler';

	export default {
		
    name : 'dashboard-todo',
  
		data: () => {
			return {
        todoList: [],
        newTodoText: '',
        editMode: false,
        editTodoIndex: undefined,
        page: 1
      }
		},

		beforeMount() {
			this.getTodoList();
    },
		
		methods: {

			getTodoList(successResponse) {
        this.$store.dispatch('startLoader', 'dashboard-todo-get-todo-list');
				axios.get('api/agent/todo-list')
        .then(response => {
          if(successResponse) {
            successHandler(successResponse, 'dashboard-page');
          }
          this.todoList = response.data.data.data;
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
				})
				.finally(() => {
					this.$store.dispatch('stopLoader', 'dashboard-todo-get-todo-list');
				})
      },

      editTodoName() {
        if(typeof this.editTodoIndex !== 'undefined') {
          this.$store.dispatch('startLoader', 'dashboard-todo-edit-todo-name');
          let clonedTodoList = JSON.parse(JSON.stringify(this.todoList));
          clonedTodoList[this.editTodoIndex].name = this.newTodoText;
          this.updateTodo(clonedTodoList).then(response => {
            successHandler(response, 'dashboard-page');
            this.todoList[this.editTodoIndex].name = this.newTodoText;
            this.newTodoText = '';
            this.editMode = false;
          })
          .catch(err => {
            errorHandler(err, 'dashboard-page');
          })
          .finally(() => {
            this.$store.dispatch('stopLoader', 'dashboard-todo-edit-todo-name');
          })
        }
      },

      onTodoMarkClick(index) {
        this.$store.dispatch('startLoader', 'dashboard-todo-on-todo-mark-click');
        this.todoList[index].status = this.getNextState(this.todoList[index].status);
        this.updateTodo(this.todoList).then(response => {
          successHandler(response, 'dashboard-page');
        })
        .catch(err => {
          errorHandler(err, 'dashboard-page');
        })
        .finally(() => {
          this.$store.dispatch('stopLoader', 'dashboard-todo-on-todo-mark-click');
        })
      },

      getTodoMarkerTitle(currentState) {
        return `Click to change status to ${this.getNextState(currentState)}`;
      },

      getNextState(currentState) {
        const nextStateObj = {
          pending: 'in-progress',
          'in-progress': 'completed',
          completed: 'pending'
        };

        return nextStateObj[currentState]
      },
      
      onReorder() {
        this.$store.dispatch('startLoader', 'dashboard-todo-on-reorder');
        this.updateTodo(this.todoList).then(response => {
          successHandler(response, 'dashboard-page');
        })
        .catch(err => {
          errorHandler(err, 'dashboard-page');
        })
        .finally(() => {
          this.$store.dispatch('stopLoader', 'dashboard-todo-on-reorder');
        })
      },

      addNewTodo() {
        this.$store.dispatch('startLoader', 'dashboard-todo-add-new-todo');
        axios.post('api/agent/create-todo', { name: this.newTodoText })
        .then(response => {
          this.newTodoText = '';
          this.getTodoList(response);
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
        })
        .finally(() => {
          this.$store.dispatch('stopLoader', 'dashboard-todo-add-new-todo');
        })
      },

      deleteTodo(id, index) {
        let isDeleteConfirmed = confirm('Are you sure you want to delete?');
        if(isDeleteConfirmed) {
          this.$store.dispatch('startLoader', 'dashboard-todo-delete-todo');
          axios.delete('api/agent/todo/' + id)
          .then(res => {
            this.getTodoList(res);
          })
          .catch(err => {
            errorHandler(err, 'dashboard-page');
          })
          .finally(() => {
            this.$store.dispatch('stopLoader', 'dashboard-todo-delete-todo');
          })
        }
      },

      updateTodo(newTodoList) {
        return axios.post('api/agent/update-todos', { todos : newTodoList })
      },

      onEditTodo(index) {
        this.editMode = true;
        this.editTodoIndex = index;
        this.newTodoText = this.todoList[index].name;
      },

      cancelEditMode() {
        this.editMode = false;
        this.newTodoText = '';
        this.editTodoIndex = undefined;
      }
		},

		components: {
      'draggable-element': draggable,
		}
	};
</script>

<style type="text/css" scoped>
.scrollable-area {
  height: 70% !important;
}
.todo-tools {
  display: none;
}
.list-group-item {
  cursor: move;
}
.list-group-item:hover .todo-tools {
  display: block;
}
.todo-text {
  padding-left: 7px
}
.todo-mark {
  cursor: pointer;
}
</style>