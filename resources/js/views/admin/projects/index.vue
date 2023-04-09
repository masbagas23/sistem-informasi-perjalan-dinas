<template>
  <div>
    <b-card class="text-center">
      <!-- Header -->
      <b-row class="d-flex align-items-center">
        <b-col cols="8">
          <!-- Keyword -->
          <b-form-input
            v-model="keyword"
            type="text"
            placeholder="Cari"
            class="h-75 w-25"
          ></b-form-input>
        </b-col>
        <b-col cols="4" class="text-right">
          <!-- Print -->
          <b-button variant="secondary" size="sm"
            ><b-icon icon="printer-fill"></b-icon></b-button
          >
          <!-- Add -->
          <b-button @click="addNew" variant="success" size="sm"
            ><b-icon icon="plus"></b-icon> Tambah</b-button
          >
          <!-- Reload -->
          <b-button variant="info" size="sm"
            ><b-icon icon="arrow-clockwise"></b-icon
          ></b-button>
        </b-col>
      </b-row>
      <!-- Table -->
      <b-table
        :items="collection.data"
        :busy.sync="isBusy"
        :fields="fields"
        :sort-by.sync="tableParams.order_column"
        :sort-desc.sync="tableParams.order_direction"
        responsive="sm"
        class="py-2"
        show-empty hover bordered breceptioned small
      >
        <template #table-busy>
            <div class="text-center text-info my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
            </div>
        </template>

        <template v-slot:cell(index)="row">
            {{ (row.index + 1) }}
        </template>
      </b-table>
      <div class="card-footer px-0 bg-white d-flex justify-content-between align-items-center">
          <div>
              <b-form-select v-model="tableParams.per_page" :options="tableParams.pageOptions"></b-form-select>
          </div>
          <div>
              <!-- <p class="text-muted">{{collection.meta.from}} hingga {{collection.meta.to}}, Total {{collection.meta.total}}</p> -->
              <!-- <p v-if="collection.data">
                  {{ $t('table summary', { from: collection.meta.from, to: collection.meta.to, total: collection.meta.total}) }}
              </p> -->
          </div>
          <div>
              <b-pagination v-model="tableParams.page" pills
                  :total-rows="collection.meta.total"
                  :per-page="collection.meta.per_page"
                  v-if="collection.data && collection.data.length > 0"
                  ></b-pagination>
          </div>
      </div>
    </b-card>

    <!-- Modal Add Project -->
    <b-modal id="modal-add-project" size="lg" title="Tambah Projek" ref="modal" no-close-on-esc no-close-on-backdrop>
        <formComponent/>
        <template v-slot:modal-footer>
            <b-button class="btn btn-secondary ml-2" @click="hideModal()"><i class="fas fa-arrow-left mr-2"></i> {{ $t('cancel') }}</b-button>
            <button class="btn btn-primary" @click.prevent="submit"> {{ $t('save') }}
                <b-spinner v-if="loadingProcess" small class="mr-2"></b-spinner>
                <i v-else class="far fa-save mr-2"></i> {{ $t('save') }}
            </button>
        </template>
    </b-modal>
  </div>
</template>
<script>
import {mapMutations, mapActions, mapState} from 'vuex'
import formComponent from './form.vue'
export default {
  components:{
    formComponent
  },
  created(){
    this.reloadTable(this.tableParams);
  },
  data() {
    return {
      fields: [
        { key: 'index', label: '#', thStyle: 'text-align: center; width: 80px;', sortable:false},
        { key: 'name', label: 'Nama', sortable: true},
        { key: 'pic', label: 'Penanggung Jawab', sortable: true},
        { key: 'desc', label: 'Deskripsi', sortable: false },
        { key: 'status', thStyle: 'text-align: center; width: 80px;'}
      ],
      items: [],
      loadingProcess:false,
      keyword: "",
    };
  },
  computed:{
    ...mapState('mstProject',{
      collection: state => state.collection,
      isBusy: state => state.isBusy,
      form: state => state.form,
    }),
    tableParams: {
        get() {
            //MENGAMBIL VALUE PAGE DARI VUEX MODULE
            return this.$store.state.admin.mstProject.tableParams
        },
        set(params) {
            this.$store.commit('mstProject/SET_TABLE_PARAMS', params)
        }
    },
  },
  watch: {
    tableParams: {
        handler:function(params){
            this.reloadTable(params);
        },
        deep: true
    },
    keyword(){
        this.reloadTable(this.tableParams, true);
    }
  },
  methods:{
    ...mapActions('mstProject', ['load','show','store','update','destroy','download']),
    addNew(){
      this.$bvModal.show('modal-add-project');
    },
    hideModal(){
      this.$bvModal.hide('modal-add-project');
    },
    reloadTable(params, keyword = false, filter = false){
      this.load({
          keyword:this.keyword,
          company_id: this.company_id,
          payroll_schedule_id: this.payroll_schedule_id,
          payroll_transaction_id: this.payroll_transaction_id,
          page: keyword ? this.tableParams.page = 1 : params.page,
          per_page: params.per_page,
          order_column: params.order_column,
          order_direction: params.order_direction,
      });
  },
  rowClass(item, type) {
      if (!item || type !== 'row') return

      let current_time = moment(),
          item_time = moment(item.updated_at),
          duration = moment.duration(current_time.diff(item_time));

      if (duration.as('seconds') < 60) return 'table-success'
  },
  handleSynchronize(){
      this.reloadTable(this.tableParams);
  },
  }
};
</script>
