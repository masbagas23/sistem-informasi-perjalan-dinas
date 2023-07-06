<template>
    <div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-12">
                <!-- Bar Chart -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Perjalanan</h6>
                    </div>
                    <b-overlay :show="isShow" rounded="sm">
                        <div v-if="isShow">
                            <b-skeleton-table :rows="5" :columns="1"></b-skeleton-table>
                        </div>
                        <div v-else class="card-body">
                            <apexchart
                                width="100%"
                                type="bar"
                                :options="chart"
                                :series="series"
                            ></apexchart>
                        </div>
                    </b-overlay>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $axios from "@app/utils/axios";
import moment from 'moment'
export default {
    created() {
        this.load();
    },
    data() {
        return {
            isShow:false,
            chart: {
                type: "bar",
                plotOptions: {
                    bar: {
                        distributed: true
                    }
                } 
            },
            series: [{
                name:"Jumlah Perjalanan",
                data:[
                    // {
                    //     x: "category A",
                    //     y: 10,
                    // },
                    // {
                    //     x: "category B",
                    //     y: 18
                    // },
                    // {
                    //     x: "category C",
                    //     y: 13
                    // }
                ]
            }]
        };
    },
    methods: {
        load() {
            const payload = {
                filter_month : moment().format('MM')
            }
            this.isShow = true
            $axios.get(`/statistic/top-customer-trip`, {params:payload}).then(response => {
                _.merge(this.series[0].data, _.sortBy(response.data.data, "y"))
                this.isShow = false
            });
        }
    }
};
</script>
