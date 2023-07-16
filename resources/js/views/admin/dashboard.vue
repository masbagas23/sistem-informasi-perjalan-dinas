<template>
  <div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
      <!-- <a
        href="#"
        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
        ><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a
      > -->
    </div>

    <!-- Content Row -->
    <div class="row">
      <!-- Trip Counter -->
      <cardTripCounter/>

      <!-- Expense Counter -->
      <cardExpenseCounter/>

      <!-- Vehicle Loan Counter -->
      <cardVehicleLoanCounter/>
      
    </div>

    <div class="row">
      <div class="col-lg-6 col-md-12">
        <topCustomerTrip/>
      </div>
      <div class="col-lg-6 col-md-12">
        <topCustomerCost/>
      </div>
    </div>
  </div>
</template>

<script>
import topCustomerTrip from "./others/top-customer-trip.vue"
import topCustomerCost from "./others/top-customer-cost.vue"
import cardTripCounter from './others/trip-card.vue'
import cardExpenseCounter from './others/expense-card.vue'
import cardVehicleLoanCounter from './others/vehicle-loan-card.vue'
export default {
  name: "Dashboard",
  components:{
    topCustomerTrip,
    topCustomerCost,
    cardTripCounter,
    cardExpenseCounter,
    cardVehicleLoanCounter
  }
};
</script>
