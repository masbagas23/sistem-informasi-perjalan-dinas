<template>
    <div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-12">
                <!-- Bar Chart -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Pelanggan</h6>
                    </div>
                    <div class="card-body">
                        <apexchart
                            width="500"
                            type="bar"
                            :options="chart"
                            :series="series"
                        ></apexchart>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $axios from "@app/utils/axios";

export default {
    created() {
        this.load();
    },
    data() {
        return {
            chart: {
                type: "bar"
            },
            series: [
                {
                    x: "category A",
                    y: 10
                },
                {
                    x: "category B",
                    y: 18
                },
                {
                    x: "category C",
                    y: 13
                }
            ]
        };
    },
    methods: {
        load() {
            $axios.get(`/statistic/top-customer`).then(response => {
                // this.series[0].data = response.data.data;
            });
        }
    }
};
</script>
