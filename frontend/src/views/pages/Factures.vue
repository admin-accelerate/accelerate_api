<template>
  <div v-if="isLoading" class="loading-overlay">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Chargement...</span>
    </div>
  </div>

  <b-row v-if="!isPreviexFacture">
    <b-col sm="12">
      <b-card no-body class="card">
        <div class="card-header d-flex justify-content-between flex-wrap">
          <div class="header-title">
            <h4 class="card-title mb-0">{{ isliste ? listTitle : formTitle }}</h4>
          </div>
          <div class="d-flex align-items-center gap-3">
            <a
              v-if="isliste"
              href="#"
              class="text-center btn btn-primary d-flex gap-2"
              @click="isliste = false"
            >
              <icon-component type="solid" icon-name="file-invoice"></icon-component>
              Nouvelle facture
            </a>

            <a
              v-if="!isliste"
              href="#"
              class="text-center btn btn-primary d-flex gap-2"
              @click="isliste = true"
            >
              <icon-component type="solid" icon-name="adjustment"></icon-component>
              Liste des factures
            </a>
          </div>
        </div>

        <div class="card-body px-0" v-if="isliste">
          <div class="table-responsive">
            <table
              id="user-list-table"
              class="table table-striped hover"
              role="grid"
              data-toggle="data-table"
            >
              <thead>
                <tr class="ligth">
                  <th></th>
                  <th>référence</th>
                  <th>Client</th>
                  <th>Total HT</th>
                  <th>Date émission</th>
                  <th>Date échéance</th>
                  <th style="min-width: 100px">Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- <table-widget :list="tableData" /> -->
                <tr v-for="(item, index) in tableData" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.invoice_number }}</td>
                  <td>{{ item.client.name }}</td>
                  <td>{{ item.total_ht }}</td>
                  <td>{{ returnFormatedDate(item.issue_date) }}</td>
                  <td>{{ returnFormatedDate(item.due_date) }}</td>
                  <td>
                    <div class="flex align-items-center list-user-action">
                      <a
                        @click="getCurrentFacture('preview', item.id)"
                        title="Consulter la facture"
                        class="btn btn-sm btn-icon btn-success mx-1"
                        href="#"
                      >
                        <span class="btn-inner">
                          <icon-component type="outlined" icon-name="eye" />
                        </span>
                      </a>

                      <a
                        @click="getCurrentFacture('edit', item.id)"
                        title="Modifier la facture"
                        class="btn btn-sm btn-icon btn-warning mx-1"
                        href="#"
                      >
                        <span class="btn-inner">
                          <icon-component type="outlined" icon-name="pencil-alt" />
                        </span>
                      </a>
                      <a
                        @click="getCurrentFacture('delete', item.id)"
                        title="Supprimer la facture"
                        class="btn btn-sm btn-icon btn-danger mx-1"
                        href="#"
                      >
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
        <!-- tableau liste factures -->

        <div class="card" v-if="!isliste">
          <div class="card-body">
            <form
              :class="`row g-3 needs-validation ${valid ? 'was-validated' : ''}`"
              novalidate=""
              @submit.prevent="formSubmit"
            >
              <div class="col-md-6 position-relative">
                <label for="validationTooltip01" class="form-label">Liste des clients</label>
                <Multiselect
                  v-model="selected"
                  :options="listClients.name"
                  :value="listClients.id"
                  placeholder="Sélectionnez un client"
                  :searchable="true"
                  :allow-empty="false"
                />
                <p>Client : {{ selected }}</p>
                <div class="invalid-feedback">Veuillez choisir le client.</div>
              </div>

              <div class="col-md-6 position-relative">
                <label for="validationTooltip02" class="form-label">Email</label>
                <input
                  type="email"
                  v-model="form.email"
                  placeholder="ex: luminem@admin.com"
                  class="form-control"
                  id="validationTooltip02"
                  required=""
                />
                <!-- <div class="valid-tooltip">Looks good!</div> -->
                <div class="invalid-feedback">Veuillez renseigner l'email.</div>
              </div>

              <div class="col-md-6 position-relative">
                <label for="validationTooltip03" class="form-label">Telephone</label>
                <input
                  type="text"
                  v-model="form.phone"
                  class="form-control"
                  id="validationTooltip03"
                  placeholder="ex: +229 01 02256 66"
                  required=""
                />
                <!-- <div class="valid-tooltip">Looks good!</div> -->
                <div class="invalid-feedback">Veuillez renseigner le téléphone.</div>
              </div>

              <div class="col-md-6 position-relative">
                <label for="validationTooltip04" class="form-label">Adresse</label>
                <input
                  type="text"
                  v-model="form.address"
                  class="form-control"
                  id="validationTooltip04"
                  placeholder="Adresse"
                  required=""
                />
                <!-- <div class="valid-tooltip">Looks good!</div> -->
                <div class="invalid-feedback">Veuillez renseigner l'adresse.</div>
              </div>

              <div class="col-12">
                <button class="btn btn-danger" type="reset" @click="resetForm">
                  Annuler
                </button>
                <button class="btn btn-primary ms-3" type="submit">Enregistrer</button>
              </div>
            </form>
          </div>
        </div>
        <!-- formulaire d'ajout facture -->

      </b-card>
    </b-col>
  </b-row>
  <!-- facture list and form -->


  <b-row v-if="isPreviexFacture">
    <b-col lg="12">
      <b-card body-class="rounded">
        <b-row>
          <b-col sm="12">
            <div class="card-header d-flex justify-content-between flex-wrap">
              <div class="d-flex align-items-center gap-3">
                <a
                  href="#"
                  class="text-center btn btn-primary d-flex gap-2"
                  @click="isPreviexFacture = false"
                >
                  <icon-component type="solid" icon-name="adjustment"></icon-component>
                  Liste des factures
                </a>
              </div>
            </div>

<br/>
<br/>
            <h4 class="mb-2 border-bottom pb-2">Facture: {{currentFacture.invoice_number}}</h4>
            <h5 class="mb-3">Client: {{currentFacture.client.name}}</h5>
            
            <p>Email: {{currentFacture.client.email}}</p>
            <p>Téléphone: {{currentFacture.client.phone}}</p>
            <p>Adresse: {{currentFacture.client.address}}</p>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12 mt-4">
            <b-table-simple responsive="lg">
              <b-thead>
                <b-tr>
                  <b-th scope="col">Désignation</b-th>
                  <b-th class="text-center" scope="col">Quantité</b-th>
                  <b-th class="text-center" scope="col">Prix</b-th>
                  <b-th class="text-center" scope="col">Totals</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>

               <!-- ligne des produits -->
                <b-tr v-for="(product,index) in currentFacture.lines" :key="index">
                  <b-td>
                    <h6 class="mb-0">Article {{index+1}}</h6>
                    <p class="mb-0">{{product.description}}</p>
                  </b-td>
                  <b-td class="text-center">5</b-td>
                  <b-td class="text-center">{{currencyFormatter('500000')}}</b-td>
                  <b-td class="text-center">{{currencyFormatter(product.amount)}}</b-td>
                </b-tr>
              <!-- ligne des produits -->
                

                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Total Hors taxe</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center">{{currencyFormatter(currentFacture.total_ht)}}</b-td>
                </b-tr>
                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Tax 20%</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center">{{currencyFormatter(currentFacture.total_ht*0.2)}}</b-td>
                </b-tr>
                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Réduction 0%</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center">currencyFormatter(0)</b-td>
                </b-tr>
                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Total Net</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"><b>{{currencyFormatter(currentFacture.total_ht+(currentFacture.total_ht*0.2))}}</b></b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </b-col>
        </b-row>

        <div class="row">
          <div class="col-sm-12">
            <p class="mb-0 mt-4" style="font-weight: bold">
              <!-- <icon-component type="solid" icon-name="file-invoice"></icon-component> -->
              Facture à payer au plus tard le <span style="color: red"> {{returnFormatedDate(currentFacture.due_date)}}</span>.
            </p>
            <div class="d-flex justify-content-center mt-4">
              <p>Réalisé le 09/05/2025 par Accelerate-Facturation</p>
            </div>
            <div class="d-flex justify-content-center mt-4">
              <button type="button" class="btn btn-primary">Imprimer</button>
            </div>
          </div>
        </div>
      </b-card>
    </b-col>
  </b-row>
  <!-- facture details view -->


</template>
<script>
// import TableWidget from '@/components/widgets/users/TableWidget.vue';
// import {  computed } from 'vue'
import axios from "axios";
import { userAuthStore } from "@store/auth";
import { required } from "@vuelidate/validators";
import { useVuelidate } from "@vuelidate/core";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

export default {
  components: {
    // TableWidget
    Multiselect
  },
  name: "Clients-list",
  setup() {
    // const auth = userAuthStore();
    // const userToken = computed(() => auth.token);
    const v = useVuelidate();

    return {
      v,
    };
  },
  computed: {
    userToken() {
      const auth = userAuthStore();
      return auth.token;
    },
  },

  data() {
    return {
      selected:'',
      tableData: [],
      isliste: true,
      isPreviexFacture: false,
      listTitle: "Liste des factures",
      formTitle: "Ajouter une facture",
      action: "add",
      idFacture: 0,
      isLoading: false,

      valid: false,
      form: {
        client_id: "",
        facture_line: {
          invoice_id: 0,
          description: "",
          qte: 0,
          amount: 0,
        },
        total_ht: 0,
      },

      currentFacture: {},
      listClients: [],
    };
  },
  methods: {
    returnFormatedDate(date) {
      return dayjs(date).format("DD-MM-YYYY");
    },

    currencyFormatter(amount) {
      const formatteur = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
      });
      
      return formatteur.format(amount);
    },

    async formSubmit() {
      // étape validation formulaire
      this.valid = true;
      const result = await this.v.$validate();

      if (result) {
        // console.log(this.action)
        if (this.action === "add") {
          this.addClient();
        }
        if (this.action === "edit") {
          this.editClient();
        }
      }
    },

    resetForm() {
      this.v.$reset();
      this.valid = false;

      this.form.name = "";
      this.form.email = "";
      this.form.phone = "";
      this.form.address = "";

      this.isliste = true;
      this.listTitle = "Liste des factures";
      this.formTitle = "Ajouter une facture";
      this.action = "add";
      this.idFacture = 0;
    },

    async addClient() {
      this.isLoading = true;
      try {
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.post(
          "http://localhost:8000/api/v1/invoices",
          this.form,
          {
            headers: {
              Authorization: `Bearer ${this.userToken}`,
            },
          }
        );
        if (response.status === 201 || response.statusText === "Created") {
          Swal.fire("Enregistrement!", response.data.message + " 🎉", "success");
          this.resetForm();
          this.getListFactures(this.userToken);
        } else {
          let errorGlobal = [];
          errorGlobal = response.data.errorsList;

          let errorMessage = "";
          if (errorGlobal.email && errorGlobal.email.length > 0) {
            errorMessage = "Cet email est déjà utilisé!! Veuillez choisir un autre.";
          } else {
            errorMessage = "Une erreur est survenue lors de l'enregistrement";
          }

          Swal.fire("Erreur!", errorMessage, "error");
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },

    async getListFactures() {
      this.isLoading = true;
      try {
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.get("http://localhost:8000/api/v1/invoices", {
          headers: {
            Authorization: `Bearer ${this.userToken}`,
          },
        });
        console.log(response.data.data);
        this.tableData = response.data.data;
      } catch (error) {
        console.error("Erreur lors de la récupération des factures :", error);
      } finally {
        this.isLoading = false;
      }
    },

    getCurrentFacture(action, idFacture) {
      this.idFacture = idFacture;
      if (action === "edit") {
        this.action = "edit";
        this.isliste = false;
        this.formTitle = "Modifier le client";
        const currentClient = this.tableData;
        // this.form = currentClient.find(client => client.id === idFacture);
        this.form = structuredClone(
          currentClient.find((client) => client.id === idFacture)
        );

        // console.log(this.form);
      }

      if (action === "delete") {
        this.action = "delete";
        const currentClient = structuredClone(
          this.tableData.find((client) => client.id === idFacture)
        );
        Swal.fire({
          title:
            "Êtes-vous sûr de vouloir supprimer le client " + currentClient.name + " ?",
          text: "Cette action est irréversible !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Oui, supprimer",
          cancelButtonText: "Annuler",
        }).then((result) => {
          if (result.isConfirmed) {
            this.deleteClient(idFacture);
          }
        });
      }

      if (action === "preview") {
        this.action = "preview";
        this.isPreviexFacture = true;
        this.isliste = true;
        this.currentFacture = structuredClone(
          this.tableData.find((facture) => facture.id === idFacture)
        );
        console.log(this.currentFacture);
      }
    },

    async editClient() {
      this.isLoading = true;
      try {
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.put(
          "http://localhost:8000/api/v1/clients/" + this.idFacture,
          this.form,
          {
            headers: {
              Authorization: `Bearer ${this.userToken}`,
            },
          }
        );

        if (response.status === 201 || response.statusText === "Created") {
          Swal.fire("Modification!", response.data.message + " 🎉", "success");
          this.resetForm();
          this.getListFactures(this.userToken);
        } else {
          const errorMessage = "Une erreur est survenue lors de la mise à jour";
          Swal.fire("Erreur!", errorMessage, "error");
        }
      } catch (error) {
        console.log(error);
        // this.isliste = true;
        // this.listTitle='Liste des clients';
        // this.formTitle='Ajouter un client';
        // this.action='add';
        // this.idFacture=0;
      } finally {
        this.isLoading = false;
      }
    },

    async deleteClient(id) {
      this.isLoading = true;
      try {
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.delete(
          `http://localhost:8000/api/v1/clients/${id}`,
          {
            headers: {
              Authorization: `Bearer ${this.userToken}`,
            },
          }
        );

        if (response.status === 200 || response.statusText === "OK") {
          Swal.fire(
            "Supprimé !",
            response.data.message || "Client supprimé avec succès.",
            "success"
          );
          this.getListFactures();
        } else {
          Swal.fire("Erreur", "Échec de la suppression du client.", "error");
        }
      } catch (error) {
        console.error(error);
        Swal.fire("Erreur", "Une erreur est survenue lors de la suppression.", "error");
      } finally {
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
                    
          this.listClients = response.data.data; 
          // console.log(tableData.value)

        }catch (error) {
          console.error("Erreur lors de la récupération des clients :", error);
        }finally{
          this.isLoading = false;
        }
       
      },

    validations: {
      return: {
        form: {
          name: {
            required,
          },
          email: {
            required,
          },
          phone: {
            required,
          },
          adresse: {
            required,
          },
        },
      },
    },
  },
  mounted() {
    this.getListFactures();
    this.getListClient();
  },
};
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
</style>
