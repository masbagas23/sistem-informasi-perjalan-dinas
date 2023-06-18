import mstUser from './users/state'
import mstRole from "./roles/state"
import mstJobPosition from "./job-positions/state"
import mstJobCategory from "./job-categories/state"
import mstCostCategory from "./cost-categories/state"
import mstVehicle from "./vehicles/state"
import mstCustomer from "./customers/state"
import businessTripApplication from "./business-trip-applications/state"
import vehicleLoan from "./vehicle-loans/state"
import downPaymentRequest from "./down-payment-requests/state"
import expense from "./expenses/state"
import reportTrip from "./report-trips/state"

export default {
    modules:{
        mstUser,
        mstRole,
        mstJobPosition,
        mstJobCategory,
        mstCostCategory,
        mstVehicle,
        mstCustomer,
        businessTripApplication,
        vehicleLoan,
        downPaymentRequest,
        expense,
        reportTrip
    }
};
