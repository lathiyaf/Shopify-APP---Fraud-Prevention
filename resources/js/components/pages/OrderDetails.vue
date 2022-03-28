<template>
    <div class="app-body">
        <loader :loading="loading"></loader>
<!--        <router-link :style="[loading ? {opacity: 0.3} : {opacity: 1}]" :to="{name :'order-listing'}" tag="td" style="cursor: pointer;"><h1 class="mb-20"><img src="/images/arrow-left.svg" class="mr-10" />Suspicious Orders</h1></router-link>-->

        <div :style="[loading ? {opacity: 0.3} : {opacity: 1}]" v-if="is_order" class="app-row">
            <div class="w-100 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header mb-30">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Risk score</h3>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="text-center bg-eq8 mb-20">
                            <h2 class="h1 mb-0">{{order.cybertonica_risk_score}}
                                <span class="eq8-tag" v-if="order.cybertonica_risk == 'High'">H</span>
                                <span class="eq8-tag medium" v-else-if="order.cybertonica_risk == 'Medium'">M</span>
                                <span class="eq8-tag low" v-else>L</span>
                            </h2>
                            <h6 class="mb-0">{{order.cybertonica_risk}} : {{order.cybertonica_risk_score}}/1000</h6>
                            <p class="mb-0">{{order.alert_message}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header mb-30">
                        <div class="d-flex d-xs-block align-items-center justify-content-between">
                            <h3 class="mb-0 mb-xs-10">Order No. : {{order.order_details.order_name}}</h3>
                            <div class="d-flex align-items-center flex-wrap">
                                <button class="btn btn-success" style="cursor: pointer;" v-if="is_approve_update_status">
                                    <div><span class="Polaris-Spinner Polaris-Spinner--sizeSmall"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M7.229 1.173a9.25 9.25 0 1011.655 11.412 1.25 1.25 0 10-2.4-.698 6.75 6.75 0 11-8.506-8.329 1.25 1.25 0 10-.75-2.385z"></path>
                                        </svg></span>
                                      <div id="PolarisPortalsContainer"></div>
                                    </div>                                
                                </button>
                                 <button v-else class="btn btn-success" style="cursor: pointer;" @click="confirmStatus(order.order_details.order_id, 'approve')"><span>Approve</span></button>

                                 <a style="cursor: pointer;" class="btn btn-danger ml-15" v-if="is_cancel_update_status">
                                    <span class="Polaris-Spinner Polaris-Spinner--sizeSmall"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M7.229 1.173a9.25 9.25 0 1011.655 11.412 1.25 1.25 0 10-2.4-.698 6.75 6.75 0 11-8.506-8.329 1.25 1.25 0 10-.75-2.385z"></path>
                                        </svg></span>
                                </a>
                                <a style="cursor: pointer;" class="btn btn-danger ml-15" @click="confirmStatus(order.order_details.order_id, 'cancel')" v-else>
                                    <span>Cancel order</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="app-card-body" style="overflow: hidden;">
                        <div class="app-row">
                            <div class="w-50 w-xs-100">
                                <div class="app-sub-card app-sub-card-padding mb-30">
                                    <div class="app-card-header mb-20">
                                        <h6 class="mb-0">Order details</h6>
                                    </div>
                                    <div class="list-unstyled app-row">
                                        <div class="w-50 w-xs-100 mb-20"><strong>Order time :</strong> {{order.order_created_at}}</div>
                                        <div class="w-50 w-xs-100 mb-20"><strong>Order total :</strong> {{order.order_details.currency}} {{order.order_details.total_price}}</div>
                                        <div class="w-50 w-xs-100 mb-20"><strong>Cybertonica order status :</strong> {{order.cybertonica_status}}</div>
                                        <div class="w-50 w-xs-100 mb-20"><strong>Cybertonica risk :</strong>
                                            <div class="app-status cancelled" v-if="order.cybertonica_risk == 'High'">{{order.cybertonica_risk}}</div>
                                            <div class="app-status medium" v-else-if="order.cybertonica_risk == 'Medium'">{{order.cybertonica_risk}}</div>
                                            <div class="app-status low" v-else>{{order.cybertonica_risk}}</div>
                                        </div>
                                    </div>
                                </div>

<!--                                <div class="app-sub-card app-sub-card-padding mb-30">-->
<!--                                    <div class="app-card-header">-->
<!--                                        <h6 class="mb-0">Payment Transactions</h6>-->
<!--                                    </div>-->
<!--                                    <div style="overflow-x: auto;">-->
<!--                                        <table class="app-table">-->
<!--                                            <thead>-->
<!--                                            <tr><th>Status</th>-->
<!--                                                <th>Card - Last 4 digit</th>-->
<!--                                                <th>BIN</th>-->
<!--                                                <th>AVS</th>-->
<!--                                                <th>Message</th>-->
<!--                                            </tr></thead>-->
<!--                                            <tbody>-->
<!--                                            <tr>-->
<!--                                                <td><div class="app-status pending">Success</div></td>-->
<!--                                                <td>Visa - 1943</td>-->
<!--                                                <td>RU</td>-->
<!--                                                <td>R</td>-->
<!--                                                <td>Card declined</td>-->
<!--                                            </tr>-->
<!--                                            <tr>-->
<!--                                                <td><div class="app-status cancelled">Failure</div></td>-->
<!--                                                <td>Visa - 1943</td>-->
<!--                                                <td>RU</td>-->
<!--                                                <td>R</td>-->
<!--                                                <td>Card declined</td>-->
<!--                                            </tr>-->
<!--                                            </tbody>-->
<!--                                        </table>-->
<!--                                    </div>-->
<!--                                </div>-->

                            </div>

                            <div class="w-50 w-xs-100">
                                <div class="app-sub-card app-sub-card-padding mb-30">
                                    <div class="app-card-header">
                                        <h6 class="mb-0">Risk factors</h6>
                                    </div>
                                    <div style="overflow-x: auto;" v-if="order.risk_factor.length > 0">
                                        <table class="app-table">
                                            <tbody>
                                            <tr v-for="(rf, index) in order.risk_factor">
<!--                                                <td>{{// rf.factor}} : {{rf.explanation}}</td>-->
                                                <td> {{rf.explanation}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else>
                                        <span>Not found...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="w-100">
                                <div class="app-sub-card app-sub-card-padding mb-30">
                                    <div class="app-card-header mb-20">
                                        <h6 class="mb-0">Locations</h6>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        <div class="w-50 w-xs-100 mb-xs-10">
                                            <GmapMap
                                                :center="{lat:center.lat, lng:center.lng}"
                                                :zoom="10"
                                                map-type-id="terrain"
                                                style="width: 100%; height: 300px"
                                            >
                                                <GmapMarker
                                                    :key="index"
                                                    v-for="(m, index) in markers"
                                                    :position="m.position"
                                                    :icon="m.icon"
                                                />
                                            </GmapMap>
                                        </div>
                                        <div class="w-50 w-xs-100">
                                            <div class="ml-30 ml-xs-0" style="overflow-x: auto;">
                                                <table class="app-table">
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="/images/Group 49.svg" class="mr-10" />
                                                                <strong>Shipping Address :</strong> {{order.order_details.shipping_address}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="/images/Group 49 (1).svg" class="mr-10" />
                                                                <strong>Billing Address :</strong> {{order.order_details.billing_address}}
                                                            </div>
                                                        </td>
                                                    </tr>
<!--                                                    <tr>-->
<!--                                                        <td>-->
<!--                                                            <div class="d-flex align-items-center">-->
<!--                                                                <img src="/images/Group 49 (2).svg" class="mr-10" />-->
<!--                                                                <strong>Device location :</strong>  Sydney, NSW , Australia                                  </div>-->
<!--                                                        </td>-->
<!--                                                    </tr>-->
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100">
                <div class="app-card">
                    <div class="app-card-header mb-20">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Customer details</h3>
                        </div>
                    </div>

                    <div class="app-card-body" style="overflow: hidden;">
                        <div class="app-row">
                            <div class="w-50 w-xs-100">
                                <div class="app-sub-card app-sub-card-padding">
                                    <div class="app-card-header mb-20">
                                        <h6 class="mb-0">Account Information</h6>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex">
                                            <div class="round-img">
                                                <img :src="order.customer.avtar" />
                                            </div>
                                            <div class="ml-xs-0 ml-30">
                                                <h3>{{order.customer.email}}</h3>
                                                <p>Customer since : {{order.customer.customer_since}}</p>
                                                <p>Name : {{order.customer.name}}</p>
                                                <p>Verified email : {{order.customer.is_email_verified}}</p>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-primary">Orders : {{order.customer.order_count}}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="w-50 w-xs-100">
                                <div class="app-sub-card app-sub-card-padding mb-30">
                                    <div class="app-card-header">
                                        <h6 class="mb-0">Order history</h6>
                                    </div>
                                    <div style="overflow-x: auto;">
                                        <table class="app-table history">
                                            <thead>
                                            <tr><th>Risk score</th>
                                                <th>Cybertonica status</th>
                                                <th>Order</th>
                                                <th>Date</th>
                                            </tr></thead>
                                            <tbody>
                                            <tr v-if="order.last_orders.length > 0" v-for="(ls_order, index) in order.last_orders">
                                                <td>{{ls_order.order.cybertonica_risk_score}}</td>
                                                <td>
                                                    <div v-if="ls_order.order_status === 'Cancelled'" class="app-status cancelled">{{ls_order.order_status}}</div>
                                                    <div v-else class="app-status pending">{{ls_order.order.cybertonica_status}}</div>
                                                </td>
                                                <td>{{ls_order.order_name}}</td>
                                                <td>{{ls_order.order.order_created_at}}</td>
                                            </tr>
                                            <tr v-else>
                                                <td colspan="4" style="text-align: center;">Order not found...</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!loading && !is_order"> Order not found.. </div>
    </div>
</template>

<script>
    import helper from '../../helper';
    import {gmapApi} from 'vue2-google-maps';
    import {Button, ContextualSaveBar, Modal, Toast} from "@shopify/app-bridge/actions";
    import Loader from "./Loader";

    export default {
        components: {
            Loader,
        },
        data(){
            return{
                is_cancel_update_status: false,
                is_approve_update_status: false,
                is_order: false,
                order_id: '',
                loading: true,
                order: [],
                markers: [],
                shipping_url: 'https://i.ibb.co/gTM4pvK/Group-49.png',
                billing_url: 'https://i.ibb.co/74vHb4B/Group-49-1.png',
                device_url: 'https://i.ibb.co/108j9Gf/Group-49-2.png',
                GmapOption:{
                    zoomControl: true,
                    mapTypeControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    fullscreenControl: true,
                    disableDefaultUI: false
                },
                center: [],
            }
        },
        methods:{
            async getOrder(){
                this.loading = true;
                let base = this;
                let url = '/get-order?order_id=' + this.order_id;
                let method = 'get';
                await axios({
                    url: url,
                    method: method,
                }).then(function (res) {
                    base.order = res.data.data;
                    base.is_order = (typeof res.data.data.length == 'undefined') ? true : false ;
                    base.markers = (typeof res.data.data.length == 'undefined') ? base.order.makers : [];
                    base.center = (typeof res.data.data.length == 'undefined') ? base.order.center : [];
                }).catch(function (err) {
                    console.log(err);
                }).finally(function (res) {
                    base.loading = false;
                });
            },
            confirmStatus(id, status){
                let base = this;
                if( status === 'approve' &&  base.order.order_details.order_status === 'Cancelled' ){
                    helper.errorToast("Order already cancelled, You can't approve it!!");
                }else if( status === 'approve' &&  base.order.order_details.order_status === 'Approved'){
                    helper.errorToast('Order already approved!!');
                }else if( status === 'cancel' &&  base.order.order_details.order_status === 'Cancelled' ){
                    helper.errorToast('Order already cancelled!!');
                }else{
                    const okButton = ( status == 'cancel' ) ?  Button.create(shopify_app_bridge, {label: 'Yes, Cancel it!', style: Button.Style.Danger}) : Button.create(shopify_app_bridge, {label: 'Yes, Approve it!', style: Button.Style.Success}) ;
                    const msg = (status == 'cancel' ) ?  'Full refund will be made. do you want to continue?' : 'Are you sure, you want to approve this order?' ;
                    const cancelButton = Button.create(shopify_app_bridge, {label: 'No'});
                    let title = status.charAt(0).toUpperCase() +
                        status.slice(1) ;
                    const modalOptions = {
                        title: title + ' order?',
                        message: msg,
                        footer: {
                            buttons: {
                                primary: okButton,
                                secondary: [cancelButton],
                            },
                        },
                    };
                    const myModal = Modal.create(shopify_app_bridge, modalOptions);
                    myModal.dispatch(Modal.Action.OPEN);
                    cancelButton.subscribe(Button.Action.CLICK, data => {
                        myModal.dispatch(Modal.Action.CLOSE);
                    });
                    okButton.subscribe(Button.Action.CLICK, data => {
                        myModal.dispatch(Modal.Action.CLOSE);
                        base.changeStatus(id, status);
                    });
                }
            },
            async changeStatus(id, status){
                let base = this;

                if( status == 'cancel' ){
                    base.is_cancel_update_status = true;
                }else{
                    base.is_approve_update_status = true;
                }

                let url = '/change-status?order_id=' + id + '&status=' + status;
                let method = 'get';

                await axios({
                    url: url,
                    method: method,
                }).then(function (res) {
                    let data = res.data.data;
                    base.order_id = data.order_id;
                    base.getOrder();

                    ( data.msg == 'Order Cancelled Successfully!' ) ? helper.successToast(data.msg) : helper.errorToast(data.msg);
                    base.is_cancel_update_status = false;
                    base.is_approve_update_status = false;
                }).catch(function (err) {
                    console.log(err);
                });
            },
        },
        created() {
            this.order_id = this.$route.params.order_id;
            this.getOrder();
            helper.bannerShow();
            window.ordrId = 0;
        },
        computed: {
            google: gmapApi
        },
    }
</script>
