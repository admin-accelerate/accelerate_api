  <template>
    <div v-if="isLoading" class="loading-overlay">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <b-row>
      <b-col sm="12">
        <b-card no-body class="card">
          <div class="card-header d-flex justify-content-between flex-wrap">
            <div class="header-title">
              <h4 class="card-title mb-0"> {{ isliste? listTitle: formTitle }}</h4>
            </div>
            <div  class="d-flex align-items-center gap-3" >
              <a v-if="isliste" href="#" class="text-center btn btn-primary d-flex gap-2" @click="isliste=false">
                <icon-component type="solid" icon-name="user-add"></icon-component>  
                Nouveau client
              </a>

              <a v-if="!isliste" href="#" class="text-center btn btn-primary d-flex gap-2" @click="isliste=true">
                <icon-component type="solid" icon-name="adjustment"></icon-component>
                Liste des clients
              </a>
            </div>


          </div>


          <div class="card-body px-0" v-if="isliste">
            <div class="table-responsive">
              <table id="user-list-table" class="table table-striped hover" role="grid" data-toggle="data-table">
                <thead>
                  <tr class="ligth">
                    <th></th>
                    <th>Désignation</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Adresse</th>
                    <th style="min-width: 100px">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- <table-widget :list="tableData" /> -->
                  <tr v-for="(item, index) in tableData" :key="index">
                    <td>{{index+1}}</td>
                    <td> {{ item.name}} </td>  
                    <td> {{ item.email}} </td>  
                    <td> {{ item.phone}} </td>  
                    <td> {{ item.address}} </td>  
                    <td>
                      <div class="flex align-items-center list-user-action">
                        <!-- <a class="btn btn-sm btn-icon btn-success mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Add" href="#">
                          <span class="btn-inner">
                            <icon-component type="outlined" icon-name="user-add" />
                          </span>
                        </a> -->
                        <a @click="getCurrentClient('edit',item.id)" title="Modifier le client" class="btn btn-sm btn-icon btn-warning mx-1"  href="#">
                          <span class="btn-inner">
                            <icon-component type="outlined" icon-name="pencil-alt" />
                          </span>
                        </a>
                        <a @click="getCurrentClient('delete',item.id)" title="Supprimer le client" class="btn btn-sm btn-icon btn-danger mx-1"  href="#">
                          <span class="btn-inner">
                            <icon-component type="outlined" icon-name="trash" />
                          </span>
                        </a>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- tableau liste clients -->
           
          
          <div class="card" v-if="!isliste">
            <div class="card-body">
              
              <form :class="`row g-3 needs-validation ${valid ? 'was-validated' : ''}`" novalidate="" @submit.prevent="formSubmit">
                <div class="col-md-6 position-relative"> 
                  <label for="validationTooltip01" class="form-label">Désignation</label>
                  <input type="text" v-model="form.name" class="form-control" id="validationTooltip01" placeholder="Désignation" required="" />
                  <!-- <div class="valid-tooltip">Looks good!</div> -->
                  <div class="invalid-feedback">Veuillez renseigner la désignation.</div>
                </div>
                 
                <div class="col-md-6 position-relative">
                  <label for="validationTooltip02" class="form-label">Email</label>
                  <input type="email" v-model="form.email" placeholder="ex: luminem@admin.com" class="form-control" id="validationTooltip02" required="" />
                  <!-- <div class="valid-tooltip">Looks good!</div> -->
                  <div class="invalid-feedback">Veuillez renseigner l'email.</div>

                </div>
                
                <div class="col-md-6 position-relative">
                  <label for="validationTooltip03" class="form-label">Telephone</label>
                  <input type="text" v-model="form.phone" class="form-control" id="validationTooltip03" placeholder="ex: +229 01 02256 66" required="" />
                  <!-- <div class="valid-tooltip">Looks good!</div> -->
                  <div class="invalid-feedback">Veuillez renseigner le téléphone.</div>

                </div>

                <div class="col-md-6 position-relative">
                  <label for="validationTooltip04" class="form-label">Adresse</label>
                  <input type="text" v-model="form.address" class="form-control" id="validationTooltip04" placeholder="Adresse" required="" />
                  <!-- <div class="valid-tooltip">Looks good!</div> -->
                  <div class="invalid-feedback">Veuillez renseigner l'adresse.</div>
 
                </div>

                <div class="col-12">
                  <button class="btn btn-danger" type="reset" @click="resetForm">Annuler</button>
                  <button class="btn btn-primary ms-3" type="submit">Enregistrer</button>
                </div>
              </form>
            </div>
          </div>
          <!-- formulaire d'ajout client -->



        </b-card>
      </b-col>
    </b-row>
    <!-- New Permission modal -->
    <!-- <div class="modal fade" id="new-permission" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropPermissionLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropPermissionLabel">Add Permission</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Permission title</label>
              <input type="text" class="form-control" placeholder="Permission Title" />
            </div>
            <div class="text-start">
              <button type="button" class="btn btn-primary me-2" data-bs-dismiss="modal">Save</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div> -->
    <!-- New Role modal -->
    <!-- <div class="modal fade" id="new-role" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropRoleLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropRoleLabel">Add Role</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Role title</label>
              <input type="text" class="form-control" placeholder="Role Title" />
            </div>
            <div>
              <span>Status</span>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="status-yes" value="option2" />
                <label class="form-check-label" for="status-yes"> Yes </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="status-no" value="option2" />
                <label class="form-check-label" for="status-no"> No </label>
              </div>
            </div>
            <div class="text-start mt-2">
              <button type="button" class="btn btn-primary me-2" data-bs-dismiss="modal">Save</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div> -->
  </template>
  <script>
  // import TableWidget from '@/components/widgets/users/TableWidget.vue';
  // import {  computed } from 'vue'
  import axios from 'axios';
  import {userAuthStore} from '@store/auth';
   import { required } from '@vuelidate/validators'
  import { useVuelidate } from '@vuelidate/core'
  import Swal from 'sweetalert2';

  export default {
    components: {
      // TableWidget
    },
    name: 'Clients-list',
    setup() {
      // const auth = userAuthStore();
      // const userToken = computed(() => auth.token);
      const v = useVuelidate()
    
      return {
        v
      }
    },
    computed:{
      userToken() {
        const auth = userAuthStore();
        return auth.token;
      }
    },

    data() {
      return {
        tableData: [],
        isliste:true,
        listTitle:'Liste des clients',
        formTitle:'Ajouter un client',
        action:'add',
        idClient:0,
        isLoading: false,

        valid: false,
        form: {
          name: '',
          email: '',
          phone: '',
          address: ''
        }
      }
   
    },
    methods: {
      async formSubmit() {
        // étape validation formulaire
        this.valid = true
        const result = await this.v.$validate()
        
        if (result) {
          // console.log(this.action)
          if(this.action==='add'){
            this.addClient();
          }
          if(this.action==='edit'){
            this.editClient();
          }
         
        }
      },

      resetForm() {
        this.v.$reset()
        this.valid = false
        
        this.form.name= '';
        this.form.email= '';
        this.form.phone= '';
        this.form.address= '';

        this.isliste = true;
        this.listTitle='Liste des clients';
        this.formTitle='Ajouter un client';
        this.action='add';
        this.idClient=0;
      },

      async addClient(){
        this.isLoading = true;
        try{
          
          await axios.get('/sanctum/csrf-cookie');
          const response = await axios.post('http://localhost:8000/api/v1/clients', this.form,{
            headers: {
              Authorization: `Bearer ${this.userToken}`
            }
          });
         if(response.status === 201 || response.statusText === "Created"){    
            Swal.fire('Enregistrement!', response.data.message+' 🎉', 'success');
            this.resetForm();
            this.getListClient(this.userToken)
         }else{
          let errorGlobal = [];
          errorGlobal = response.data.errorsList;
          
          let errorMessage = '';
          if(errorGlobal.email && errorGlobal.email.length > 0){
            errorMessage = 'Cet email est déjà utilisé!! Veuillez choisir un autre.';
          }else{
            errorMessage = 'Une erreur est survenue lors de l\'enregistrement';          
          }
 
          Swal.fire('Erreur!', errorMessage, 'error');
         }
        }catch(error){
          console.log(error);
        }finally{
          this.isLoading = false;
        }
      },

      async getListClient(){
        this.isLoading = true;
        try{
          await axios.get('/sanctum/csrf-cookie');
          const response = await axios.get('http://localhost:8000/api/v1/clients', {
            headers: {
              Authorization: `Bearer ${this.userToken}`
            }
          });
                    
          this.tableData = response.data.data; 
          // console.log(tableData.value)

        }catch (error) {
          console.error("Erreur lors de la récupération des clients :", error);
        }finally{
          this.isLoading = false;
        }
       
      },

      getCurrentClient(action,idCLient){
        this.idClient = idCLient;
        if(action === 'edit'){
            this.action = 'edit';
            this.isliste = false;
            this.formTitle = 'Modifier le client';
            const currentClient = this.tableData;
            // this.form = currentClient.find(client => client.id === idCLient);
            this.form = structuredClone(currentClient.find(client => client.id === idCLient));

            // console.log(this.form);
        }

        if(action === 'delete'){
          this.action = 'delete';
          const currentClient = structuredClone(this.tableData.find(client => client.id === idCLient));
          Swal.fire({
            title: 'Êtes-vous sûr de vouloir supprimer le client '+currentClient.name+' ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
          }).then((result) => {
            if (result.isConfirmed) {
              this.deleteClient(idCLient);
            }
          });

        }
        
      },

      async editClient(){
        this.isLoading = true;
        try{
          await axios.get('/sanctum/csrf-cookie');
          const response = await axios.put('http://localhost:8000/api/v1/clients/'+this.idClient, this.form,{
            headers: {
              Authorization: `Bearer ${this.userToken}`
            }
          });
          
          if(response.status === 201 || response.statusText === "Created"){    
            Swal.fire('Modification!', response.data.message+' 🎉', 'success');
            this.resetForm();
            this.getListClient(this.userToken)
          }else{
             
             const errorMessage = 'Une erreur est survenue lors de la mise à jour';           
            Swal.fire('Erreur!', errorMessage, 'error');

          }   
        }catch(error){
          console.log(error);
          // this.isliste = true;
          // this.listTitle='Liste des clients';
          // this.formTitle='Ajouter un client';
          // this.action='add';
          // this.idClient=0;
        }finally{
          this.isLoading = false;
        }

      },

      async deleteClient(id) {
        this.isLoading = true;
        try {
          await axios.get('/sanctum/csrf-cookie');
          const response = await axios.delete(`http://localhost:8000/api/v1/clients/${id}`, {
            headers: {
              Authorization: `Bearer ${this.userToken}`
            }
          });

          if (response.status === 200 || response.statusText === "OK") {
            Swal.fire('Supprimé !', response.data.message || 'Client supprimé avec succès.', 'success');
            this.getListClient();
          } else {
            Swal.fire('Erreur', 'Échec de la suppression du client.', 'error');
          }
        } catch (error) {
          console.error(error);
          Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
        }finally{
          this.isLoading = false;
        }
      },

    
      validations: {
      return:{
        form: {
          name: {
            required
          },
          email: {
            required
          },
          phone: {
            required
          },
          adresse: {
            required
          }
        }
      } 
      },

    },
    mounted() {
      this.getListClient();
    }
  }
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

</style>
  