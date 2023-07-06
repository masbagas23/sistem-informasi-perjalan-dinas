<template>
    <div class="col-xl-4 col-md-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <b-overlay :show="isShow" rounded="sm">
                  <div v-if="isShow">
                    <b-skeleton-table :rows="1" :columns="1"></b-skeleton-table>
                  </div>
                  <div v-else>
                    <div
					class="
						text-xs
						font-weight-bold
						text-primary text-uppercase
						mb-1
					"
					>
					PERJALAN DINAS (BULANAN)
					</div>
					<div class="h5 mb-0 font-weight-bold text-gray-800">
					{{total}} PERJALAN
					</div>
                  </div>
                </b-overlay>
              </div>
              <div class="col-auto">
                <i class="fa fa-suitcase fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
</template>
<script>
import $axios from '@app/utils/axios'
import moment from 'moment'
export default {
	data(){
      return{
        isShow:false,
        total:0,
      }
    },
	mounted(){
      this.loadCounter()
    },
    methods:{
        loadCounter(){
            const payload = {
                filter_month : moment().format('MM')
            }
			this.isShow = true
            $axios.get(`/business-trip-counter`,{params:payload}).then(response => {
                this.total = response.data.data.total
				this.isShow = false
            });
        }
    }
}
</script>