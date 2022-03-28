<template>
    <div class="app-body">
        <!-- <loader :loading="loading"></loader> -->
        <div class="app-row">
            <div class="app-card-body" style="overflow: hidden;">
                <div class="w-100 w-xs-100">
                    <div class="app-sub-card app-sub-card-padding mb-30">
                        <div class="app-card-header" style="display: flex;">
                            <h6 class="mb-0" style="width: 90%;">Suspicious Order</h6>
                            <div>
                                <div class="form-group has-search">
                                    <span class="fa fa-search form-control-feedback"></span>
                                    <input type="text" class="form-control" v-model="search" @keyup="onFilterSet" placeholder="Search">
                                </div>
                            </div>
                        </div>
                        <vuetable
                            ref="ordervuetable"
                            api-url="/get-order-list"
                            :fields="fields"
                            :sort-order="sortOrder"
                            :per-page="perPage"
                            :show-sort-icons="true"
                            :append-params="moreParams"
                            @vuetable:pagination-data="onPaginationData"
                            pagination-path
                            :css="css.table"
                        >
                            <template slot="order_name" slot-scope="props">
                                <router-link :to="{name :'order-details', params: {order_id: props.rowData.order_id}}" tag="td" class="a-link">{{ props.rowData.order_name }}</router-link>
                            </template>
                            <template slot="order_status" slot-scope="props">
                                <div class="app-status cancelled" v-if="props.rowData.order_status === 'Cancelled'">{{props.rowData.order_status}}</div>
                                <div class="app-status pending" v-else-if="props.rowData.order_status === 'Accepted'">{{props.rowData.order_status}}</div>
                                <div class="app-status pending" v-else>{{props.rowData.order_status}}</div>
                            </template>
                            <template slot="cybertonica_risk" slot-scope="props">
                                <div class="app-status cancelled" v-if="props.rowData.cybertonica_risk == 'High'">{{props.rowData.cybertonica_risk}}</div>
                                <div class="app-status medium" v-else-if="props.rowData.cybertonica_risk === 'Medium'">{{props.rowData.cybertonica_risk}}</div>
                                <div class="app-status low" v-else>{{props.rowData.cybertonica_risk}}</div>
                            </template>
                            <template slot="currency" slot-scope="props">
                                {{props.rowData.currency }} {{ props.rowData.total_price }}
                            </template>
                        </vuetable>
                        <!--                        <div style="overflow-x: auto;">-->
                        <!--                            <table class="app-table order-list">-->
                        <!--                                <thead>-->
                        <!--                                <tr><th>Order</th>-->
                        <!--                                    <th>Order Time</th>-->
                        <!--                                    <th>Order Status</th>-->
                        <!--                                    <th>Cybertonica Risk</th>-->
                        <!--                                    <th>Risk Score</th>-->
                        <!--                                    <th>Total Price</th>-->
                        <!--                                </tr></thead>-->
                        <!--                                <tbody>-->
                        <!--                                <tr v-if="orders" v-for="(item, index) in orders" :key="index">-->
                        <!--                                    <router-link :to="{name :'order-details', params: {order_id: item.order_id}}" tag="td" class="a-link">{{  item.order_details.order_name }}</router-link>-->
                        <!--                                    <td>{{item.order_created_at }}</td>-->
                        <!--                                    <td>-->
                        <!--                                        <div class="app-status cancelled" v-if="item.order_details.order_status === 'Cancelled'">{{item.order_details.order_status}}</div>-->
                        <!--                                        <div class="app-status pending" v-else-if="item.order_details.order_status === 'Accepted'">{{item.order_details.order_status}}</div>-->
                        <!--                                        <div class="app-status pending" v-else>{{item.order_details.order_status}}</div>-->
                        <!--                                    </td>-->
                        <!--                                    <td>{{item.cybertonica_risk}}</td>-->
                        <!--                                    <td>{{item.cybertonica_risk_score}}</td>-->
                        <!--                                    <td>{{item.order_details.currency }} {{ item.order_details.total_price }}</td>-->
                        <!--                                </tr>-->
                        <!--                                <tr v-if="!orders.length">-->
                        <!--                                    <td colspan="6" style="text-align: center;">No any order found!</td>-->
                        <!--                                </tr>-->
                        <!--                                </tbody>-->
                        <!--                            </table>-->
                        <!--                        </div>-->
                    </div>
                    <vuetable-pagination ref="orderpagination" :css="css.pagination"
                                         @vuetable-pagination:change-page="onChangePage"
                    ></vuetable-pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Loader from "./Loader";
import helper from '../../helper';
import Vuetable from 'vuetable-2/src/components/Vuetable';
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
export default {
    components: {
        Loader,
        Vuetable,
        VuetablePagination,
    },
    data(){
        return{
            //loading: true,
            search: '',
            orders: [],
            perPage: 10,
            moreParams: {},
            fields: [
                { name: '__slot:order_name', title: 'Order', sortField: 'order_name'},
                { name: 'order_created_at', title: 'Order Time', sortField: 'order_created_at' },
                { name: '__slot:order_status', title: 'Order Status', sortField: 'order_status'},
                { name: '__slot:cybertonica_risk', title: 'Cybertonica Risk', sortField: 'cybertonica_risk'},
                // { name: 'cybertonica_risk', title: 'Cybertonica Risk', sortField: 'cybertonica_risk'},
                { name: 'cybertonica_risk_score', title: 'Risk Score', sortField: 'cybertonica_risk_score'},
                { name: '__slot:currency', title: 'Total Price', sortField: 'total_price'},
            ],
            css: {
                table: {
                    tableClass: 'table table-striped table-bordered table-hovered',
                    loadingClass: 'loading',
                    ascendingIcon: 'fa fa-caret-up icon',
                    descendingIcon: 'fa fa-caret-down icon',
                    ascendingClass: 'sorted-asc',
                    descendingClass: 'sorted-desc',
                    sortableIcon: 'fa fa-caret-down icon',
                    handleIcon: 'grey sidebar icon',

                },
                pagination: {
                    infoClass: 'pull-left',
                    wrapperClass: 'vuetable-pagination pull-right',
                    activeClass: 'btn-primary',
                    disabledClass: 'disabled',
                    pageClass: 'btn btn-border',
                    linkClass: 'btn btn-border',
                    icons: {
                        first: '',
                        prev: '',
                        next: '',
                        last: '',
                    },
                }
            },
            sortOrder: [
                {
                    name: 'order_created_at',
                    sortField: 'order_created_at',
                    direction: 'desc'
                },
            ],
        }
    },
    methods:{
        async getOrder(){
            this.loading = true;
            let base = this;
            let url = 'get-order-list';
            let method = 'get';
            await axios({
                url: url,
                method: method,
            }).then(function (res) {
                base.orders = res.data.data;
            }).catch(function (err) {
                console.log(err);
            }).finally(function (res) {
                base.loading = false;
            });
        },
        dataManager(sortOrder, pagination) {
            console.log('1213123213');
            if (this.orders.order.length < 1) {
                return {
                    pagination: [],
                    data: _.slice([], 0, 0)
                };
            }

            let local = this.orders.order;

            // sortOrder can be empty, so we have to check for that as well
            if (sortOrder.length > 0) {
                console.log("orderBy:", sortOrder[0].sortField, sortOrder[0].direction);
                local = _.orderBy(
                    local,
                    sortOrder[0].sortField,
                    sortOrder[0].direction
                );
            }

            pagination = this.$refs.ordervuetable.makePagination(
                local.length,
                this.perPage
            );
            let from = pagination.from - 1;
            let to = from + this.perPage;

            return {
                pagination: pagination,
                data: _.slice(local, from, to)
            };
        },
        onPaginationData (paginationData) {
            this.$refs.orderpagination.setPaginationData(paginationData)
        },
        onChangePage (page) {
            this.$refs.ordervuetable.changePage(page)
        },
        onFilterSet () {
            console.log(this.search);
            this.moreParams = {
                's': this.search
            }
            Vue.nextTick( () => this.$refs.ordervuetable.refresh())
        },
        onFilterReset () {
            this.moreParams = {}
            Vue.nextTick( () => this.$refs.vuetable.refresh())
        }
    },
    created() {
        //this.getOrder();
        helper.bannerShow();
    },
    watch: {
        orders(newVal, oldVal) {
            this.$refs.ordervuetable.refresh();
        }
    }
}
</script>
