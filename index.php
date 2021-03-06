<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD con Vue.js, PHP y MySQL</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
  <style>
    :root {
      --color-primary: #41B883;
      --color-secondary: #35495E;
      --bg-color: #727F80;
    }

    .btn,
    .btn-large {
      background-color: var(--color-primary);
    }

    .btn:hover,
    .btn-large:hover {
      background-color: var(--color-secondary);
    }

    .ModalWindow {
      position: fixed;
      z-index: 999;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, .25);
      display: none;
    }

    .ModalWindow-container{
      margin: 5vh auto 0;
      width: 60%;
      background-color: #FFF;
    }

    .ModalWindow-heading{
      padding: 0 1rem;
      background-color:  var(--bg-color);
      color: #FFF;
    }

    .ModalWindow-content {
      padding: 1rem;
    }

    .u-flexColumnCenter {
      padding: 1rem;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .u-show {
      display: initial;
    }

    .fade-enter-active,
    .fade-leave-active {
      transition: opacity .5s
    }

    .fade-enter,
    .fade-leave-to {
      opacity: 0
    }
  </style>
</head>
<body>
  <main id="app" class="container center">
    <section class="row valign-wrapper">
      <div class="col s12 m3">
        <img class="responsive-img" src="https://vuejs.org/images/logo.png" alt="Vue.js">
      </div>
      <div class="col s12 m6">
        <h1>CRUD</h1>
        <h5>(PHP + MySQL)</h5>
      </div>
      <div class="col s12 m3">
        <img class="responsive-img" src="https://ed.team/themes/custom/escueladigital/img/EDteam-logo.svg" alt="EDteam">
      </div>
    </section>
    <section class="row">
      <div class="col s12">
        <h4>Curso de Vue.js desde cero</h4>
      </div>
    </section>
    <section class="row valign-wrapper">
      <div class="col s10">
        <h3 class="left">Lista de estudiantes</h3>
      </div>
      <div class="col s2">
        <button class="btn-large btn-floating" @click="toggleModal('add')">
          <i class="material-icons">add_circle</i>
        </button>
      </div>
    </section>
    <hr>
    <transition name="fade">
      <p class="u-flexColumnCenter red accent-1 red-text text-darken-4"
      v-if="errorMessage">
        {{ errorMessage }}
        <i class="material-icons prefix">error</i>
      </p>
      <p class="u-flexColumnCenter green accent-1 green-text text-darken-4"
      v-if="successMessage">
        {{ successMessage }}
        <i class="material-icons prefix">check_circle</i>
      </p>
    </transition>
    <transition name="fade">
      <table class="responsive-table stripe">
      <tr>
        <th>Id</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Web</th>
        <th>Editar</th>
        <th>Borrar</th>
      </tr>
      <tr v-for="student in students" :key="student.id">
        <td>{{ student.id }}</td>
        <td>{{ student.name }}</td>
        <td>{{ student.email }}</td>
        <td>{{ student.web }}</td>
        <td>
          <button class="btn btn-floating" @click="getStudent('edit', student)">
            <i class="material-icons">edit</i>
          </button>
        </td>
        <td>
          <button class="btn btn-floating" @click="getStudent('delete', student)">
            <i class="material-icons">delete</i>
          </button>
        </td>
      </tr>
      </table>
    </transition>
    <transition name="fade">
      <section :class="[ 'ModalWindow', displayAddModal ]" v-if="showAddModal">
        <div class="ModalWindow-container">
          <header class="ModalWindow-heading">
            <div class="row valign-wrapper">
              <div class="col s10">
                <h4 class="left">Agregar Estudiante</h4>
              </div>
              <div class="col s2">
                <button class="btn btn-floating right" @click="toggleModal('add')">
                  <i class="material-icons">close</i>
                </button>
              </div>
            </div>
          </header>
          <form class="ModalWindow-content row" @submit.prevent="createStudent">
            <div class="input-field col s12">
              <i class="material-icons prefix">account_circle</i>
              <input name="name" type="text" placeholder="Nombre" required>
            </div>
            <div class="input-field col s12">
              <i class="material-icons prefix">email</i>
              <input name="email" type="text" placeholder="Correo" required>
            </div>
            <div class="input-field col s12">
              <i class="material-icons prefix">web</i>
              <input name="web" type="text" placeholder="Web" required>
            </div>
            <div class="input-field col s12">
              <button class="btn-large btn-floating right" type="submit">
                <i class="material-icons">save</i>
              </button>
            </div>
          </form>
        </div>
      </section>
    </transition>
    <transition name="fade">
      <section :class="['ModalWindow', displayEditModal]" v-if="showEditModal">
        <div class="ModalWindow-container">
          <header class="ModalWindow-heading">
            <div class="row valign-wrapper">
              <div class="col s10">
                <h4 class="left">Editar Estudiante</h4>
              </div>
              <div class="col s2">
                <button class="btn btn-floating right" @click="toggleModal('edit')">
                  <i class="material-icons">close</i>
                </button>
              </div>
            </div>
          </header>
          <form class="ModalWindow-content row" @submit.prevent="updateStudent">
            <div class="input-field col s12">
              <i class="material-icons prefix">account_circle</i>
              <input v-model="activeStudent.name" name="name" type="text" placeholder="Nombre" required>
            </div>
            <div class="input-field col s12">
              <i class="material-icons prefix">email</i>
              <input v-model="activeStudent.email" name="email" type="text" placeholder="Correo" required>
            </div>
            <div class="input-field col s12">
              <i class="material-icons prefix">web</i>
              <input v-model="activeStudent.web" name="web" type="text" placeholder="Web" required>
            </div>
            <div class="input-field col s12">
              <button class="btn-large btn-floating right" type="submit">
                <i class="material-icons">save</i>
              </button>
              <input v-model="activeStudent.id" name="id" type="hidden" required>
            </div>
          </form>
        </div>
      </section>
    </transition>
    <transition name="fade">
      <section :class="['ModalWindow', displayDeleteModal]" v-if="showDeleteModal">
        <div class="ModalWindow-container">
          <header class="ModalWindow-heading">
            <div class="row valign-wrapper">
              <div class="col s10">
                <h4 class="left">Eliminar Estudiante</h4>
              </div>
              <div class="col s2">
                <button class="btn btn-floating right" @click="toggleModal('delete')">
                  <i class="material-icons">close</i>
                </button>
              </div>
            </div>
          </header>
          <form class="ModalWindow-content row" @submit.prevent="deleteStudent">
            <div class="input-field col s12">
              <p class="flow-text center">¿Estás seguro de eliminar al estudiante: <b>{{activeStudent.name}}</b>.</p>
              <input v-model="activeStudent.id" name="id" type="hidden" required>
            </div>
            <div class="input-field col s4 offset-s4">
              <button class="btn-large btn-floating left" type="submit">
                <i class="material-icons">check</i>
              </button>
              <button class="btn-large btn-floating right" type="button" @click="toggleModal('delete')">
                <i class="material-icons">close</i>
              </button>
            </div>
          </form>
        </div>
      </section>
    </transition>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/vue"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script>
    const app = new Vue({
      el: "#app",
      data: {
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        errorMessage: '',
        successMessage: '',
        students: [],
        activeStudent: {}
      },
      mounted(){
        this.getAllStudents()
      },
      computed: {
        displayAddModal(){
          return (this.showAddModal==true) ? 'u-show' : ''
        },
        displayEditModal(){
          return (this.showEditModal==true) ? 'u-show' : ''
        },
        displayDeleteModal(){
          return (this.showDeleteModal==true) ? 'u-show' : ''
        }
      },
      methods: {
        toggleModal(modal){
          if (modal ==='add'){
            this.showAddModal=!this.showAddModal
          }else if(modal==='edit'){
            this.showEditModal=!this.showEditModal
          }else if(modal==='delete'){
            this.showDeleteModal=!this.showDeleteModal
          }
        },
        setMessages(res,removeBucle){
          if (res.data.error==1) {
            this.errorMessage = res.data.message
          }else{
            if (removeBucle != 'read') {
              this.successMessage = res.data.message
              this.getAllStudents()
            }
            this.successMessage = res.data.message
          }

          setTimeout(() =>{
            this.errorMessage = false
            this.successMessage = false
          }, 2000)
        },
        getAllStudents(){
          axios.get('./api.php?action=read')
            .then(res=>{
              //console.log(res)
              let removeBucle = 'read'
              this.setMessages(res,removeBucle)
              this.students = res.data.students
            })
        },
        createStudent(e){
          axios.post( './api.php?action=create', new FormData(e.target))
            .then(res => {
              this.toggleModal('add')
              this.setMessages(res)
            })
        },
        getStudent(action, student){
          this.toggleModal(action)
          this.activeStudent = student
        },
        updateStudent(e){
          axios.post( './api.php?action=update', new FormData(e.target))
            .then(res => {
              this.toggleModal('edit')
              this.setMessages(res)
            })
        },
        deleteStudent(e){
          axios.post( './api.php?action=delete', new FormData(e.target))
            .then(res => {
              this.toggleModal('delete')
              this.setMessages(res)
            })
        }
      }
    })
  </script>
</body>
</html>
