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
                  <td>{{ currencyFormatter(item.total_ht) }}</td>
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


        <div class="container py-4" v-if="!isliste">
          <div class="card shadow-sm">
            <form @submit.prevent="formSubmit" class="card-body">
              <!-- Informations de la facture -->
              <div class="row mb-4">
                <div class="col-md-12 mb-3">                  
                  <b-form-group label="Client">
                    <b-form-select v-model="invoice.client_id">
                      <b-form-select-option :value="null" disabled>-- Sélectionner un client --</b-form-select-option>
                      <b-form-select-option
                        v-for="client in listClients"
                        :key="client.id"
                        :value="client.id"
                      >
                        {{ client.name }}
                      </b-form-select-option>
                    </b-form-select>
                  </b-form-group>
                  
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Date d'émission</label>
                  <input
                    v-model="invoice.issue_date"
                    type="date"
                    class="form-control"
                    required
                  />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Date échéance</label>
                  <input
                    v-model="invoice.due_date"
                    type="date"
                    class="form-control"
                    required
                  />
                </div>
              </div>

              <!-- Lignes de produits -->
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="mb-0">Produits</h5>
                  <button type="button" class="btn btn-primary btn-sm"  @click="addProductLine">
                   + Ajouter un produit
                  </button> 
                </div>

                <div class="table-responsive">
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Description</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(line, index) in invoice.lines" :key="index">
                        <td>
                          <input
                            v-model="line.description"
                            type="text"
                            class="form-control"
                            placeholder="Description du produit"
                            required
                          />
                        </td>
                        <td>
                          <input
                            v-model.number="line.quantity"
                            type="number"
                            class="form-control"
                            min="1"
                            required
                            @input="updateLineTotal(index)"
                          />
                        </td>
                        <td>
                          <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input
                              v-model.number="line.unit_price"
                              type="number"
                              min="0"
                              step="0.01"
                              class="form-control"
                              required
                              @input="updateLineTotal(index)"
                            />
                          </div>
                        </td>
                        <td class="fw-semibold">
                          {{ currencyFormatter(line.total_amount) }}
                        </td>
                        <td>
                          <button
                            type="button"
                            @click="removeProductLine(index)"
                            class="btn btn-link text-danger p-0"
                            title="Supprimer"
                          >
                          <span class="btn-inner">
                            <icon-component type="outlined" icon-name="trash" />
                          </span>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div
                  v-if="invoice.lines.length === 0"
                  class="alert alert-secondary text-center mt-3"
                >
                  Aucun produit ajouté. Cliquez sur "Ajouter un produit" pour commencer.
                </div>
              </div>

              <!-- Totaux -->
              <div class="d-flex justify-content-end mb-4">
                <div class="w-100" style="max-width: 300px">
                  <div class="d-flex justify-content-between border-bottom py-1">
                    <span class="fw-medium">Sous-total:</span>
                    <span>{{ currencyFormatter(sousTotal) }}</span>
                  </div>
                  <div class="d-flex justify-content-between border-bottom py-1">
                    <span class="fw-medium">TVA (20%) :</span>
                    <span>{{ currencyFormatter(taxeApplique) }}</span>
                  </div>
                  <div class="d-flex justify-content-between fw-bold py-2 fs-5">
                    <span>Total :</span>
                    <span>{{ currencyFormatter(total) }}</span>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <!-- <div class="mb-4">
                <label class="form-label">Notes</label>
                <textarea
                  v-model="invoice.notes"
                  class="form-control"
                  rows="3"
                  placeholder="Informations complémentaires, conditions de paiement, etc."
                ></textarea>
              </div> -->

              <!-- Boutons d'action -->
              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-danger"
                  @click="resetForm"
                >
                  Annuler
                </button>
               
                <!-- <button
                  type="submit"
                  class="btn btn-secondary d-flex align-items-center"
                  :disabled="invoice.lines.length === 0"
                >
                  Enregistrer la facture
                </button> -->

                <button class="btn btn-primary ms-3"  :disabled="invoice.lines.length === 0" type="submit">Enregistrer</button>

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

            <br />
            <br />
            <h4 class="mb-2 border-bottom pb-2">
              Facture: {{ currentFacture.invoice_number }}
            </h4>
            <h5 class="mb-3">Client: {{ currentFacture.client.name }}</h5>

            <p>Email: {{ currentFacture.client.email }}</p>
            <p>Téléphone: {{ currentFacture.client.phone }}</p>
            <p>Adresse: {{ currentFacture.client.address }}</p>
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
                <b-tr v-for="(product, index) in currentFacture.lines" :key="index">
                  <b-td>
                    <p class="mb-0">{{ product.description }}</p>
                  </b-td>
                  <b-td class="text-center"> {{ product.quantity }} </b-td>
                  <b-td class="text-center">{{ currencyFormatter(product.unit_price) }}</b-td>
                  <b-td class="text-center">{{ currencyFormatter(product.total_amount) }}</b-td>
                </b-tr>
                <!-- ligne des produits -->

                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Total Hors taxe</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center">{{
                    currencyFormatter(currentFacture.total_ht)
                  }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Tax 20%</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center">{{
                    currencyFormatter(currentFacture.total_ht * 0.2)
                  }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td>
                    <h6 class="mb-0" style="font-weight: bold">Total TTC</h6>
                  </b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"></b-td>
                  <b-td class="text-center"
                    ><b>{{
                      currencyFormatter(
                        currentFacture.total_ht + currentFacture.total_ht * 0.2
                      )
                    }}</b></b-td
                  >
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </b-col>
        </b-row>

        <div class="row">
          <div class="col-sm-12">
            <p class="mb-0 mt-4" style="font-weight: bold">
              <!-- <icon-component type="solid" icon-name="file-invoice"></icon-component> -->
              Facture à payer au plus tard le
              <span style="color: red">
                {{ returnFormatedDate(currentFacture.due_date) }}</span
              >.
            </p>
            <div class="d-flex justify-content-center mt-4">
              <p>Réalisé le {{currentFacture.issue_date}} par Accelerate-Facturation</p>
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
// import { computed } from 'vue'
import axios from "axios";
import { userAuthStore } from "@store/auth";
import { required } from "@vuelidate/validators";
import { useVuelidate } from "@vuelidate/core";
import Swal from "sweetalert2";
import dayjs from "dayjs";
// import Multiselect from 'vue-multiselect'
import "vue-multiselect/dist/vue-multiselect.css";

export default {
  components: {
    // TableWidget
    // Multiselect
  },
  name: "Factures-list",
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
    sousTotal() {
      return this.invoice.lines.reduce((sum, product) => sum + product.total_amount, 0);
    },

    taxeApplique() {
      return this.sousTotal * 0.2; //Appliquer une TVA 20%
    },
    total() {
      return this.sousTotal + this.taxeApplique;
    },
  },

  data() {
    return {
      selected: "",
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

      invoice: {
        client_id: "",
        issue_date: "",
        due_date: "",
        lines: [],
      },
    };
  },
  methods: {
    // Actions ligne de produit
    addProductLine() {
      this.invoice.lines.push({
        description: "",
        quantity: 1,
        unit_price: 0,
        total_amount: 0,
      });
    },
    removeProductLine(index) {
      this.invoice.lines.splice(index, 1);
    },
    updateLineTotal(index) {
      const line = this.invoice.lines[index];
      line.total_amount = line.quantity * line.unit_price;
    },

    resetForm() {
      this.invoice = {
        client_id: "",
        issue_date: new Date().toISOString().split("T")[0],
        due_date: "",
        lines: [
          {
            description: "",
            quantity: 1,
            unit_price: 0,
            total_amount: 0,
          },
        ],
      };

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

    //End Action ligne de produit

    returnFormatedDate(date) {
      return dayjs(date).format("DD-MM-YYYY");
    },

    currencyFormatter(amount) {
      const formatteur = new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "EUR",
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
          this.addFacture();
        }
        if (this.action === "edit") {
          this.UpdateFacture();
        }
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
        // console.log(response.data.data);
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
        this.formTitle = "Modifier la facture";
        
        //show invoice data to update
        const invoice = structuredClone(
          this.tableData.find((facture) => facture.id === idFacture)
        );

        console.log(this.invoice)

        // this.invoice = {
          this.invoice.client_id = invoice.client.id;
          this.invoice.issue_date = invoice.issue_date;
          this.invoice.due_date = invoice.due_date;
          this.invoice.lines = invoice.lines;
        // };


      }

      if (action === "delete") {
        this.action = "delete";
      
        const CurrentInvoice = structuredClone(
          this.tableData.find((facture) => facture.id === idFacture)
        );

        Swal.fire({
          title:
            "Êtes-vous sûr de vouloir supprimer la facture " + CurrentInvoice.invoice_number + " ?",
          text: "Cette action est irréversible !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Oui, supprimer",
          cancelButtonText: "Annuler",
        }).then((result) => {
          if (result.isConfirmed) {
            this.deleteFacture(idFacture);
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

    async getListClient() {
      this.isLoading = true;
      try {
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.get("http://localhost:8000/api/v1/clients", {
          headers: {
            Authorization: `Bearer ${this.userToken}`,
          },
        });

        this.listClients = response.data.data;
        // console.log(tableData.value)
      } catch (error) {
        console.error("Erreur lors de la récupération des clients :", error);
      } finally {
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

    async addFacture() {
      console.log(this.invoice) 
      this.isLoading = true;
      try {
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.post(
          "http://localhost:8000/api/v1/invoices",
          this.invoice,
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
          let errorMessage = "Une erreur est survenue lors de l'enregistrement";
          
          Swal.fire("Erreur!", errorMessage, "error");
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },

    async deleteFacture(idfacture){
      this.isLoading = true;
        try {
          await axios.get('/sanctum/csrf-cookie');
          const response = await axios.delete(`http://localhost:8000/api/v1/invoices/${idfacture}`, {
            headers: {
              Authorization: `Bearer ${this.userToken}`
            }
          });

          if (response.status === 200 || response.statusText === "OK") {
            Swal.fire('Supprimé !', response.data.message || 'Facure supprimé avec succès.', 'success');
            this.getListFactures();
          } else {
            Swal.fire('Erreur', 'Échec de la suppression de la facture.', 'error');
          }
        } catch (error) {
          console.error(error);
          Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
        }finally{
          this.isLoading = false;
        }
    },

    async UpdateFacture(){
      this.isLoading = true;
        try{
          await axios.get('/sanctum/csrf-cookie');
          const response = await axios.put('http://localhost:8000/api/v1/invoices/'+this.idFacture, this.invoice,{
            headers: {
              Authorization: `Bearer ${this.userToken}`
            }
          });
          
          if(response.status === 201 || response.statusText === "Created"){    
            Swal.fire('Modification!', response.data.message+' 🎉', 'success');
            this.resetForm();
            this.getListFactures(this.userToken)
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
  },
  mounted() {
    this.getListFactures();
    this.getListClient();

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");
    this.invoice.issue_date = `${year}-${month}-${day}`;

    // Ajouter une ligne de produit vide par défaut
    this.addProductLine();
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
