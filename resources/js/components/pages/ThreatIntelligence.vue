<template>
    <div class="app-body">
        <loader :loading="loading"></loader>
        <div :style="[loading ? {opacity: 0.3} : {opacity: 1}]" class="app-row dashboard-icon-main-div">
<!--            <div class="w-33 w-xs-100 mb-30 first-icon-class">-->
<!--                <div class="form-group">-->
<!--                    <label for="username">User Name</label><br>-->
<!--                    <select id="username" name="username" v-model="form.unm" class="form-control">-->
<!--                        <option value="mark">mark</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->

            <div class="w-33 w-xs-100 mb-30 first-icon-class">
                <div class="form-group">
                    <label for="threat-type">Threat Type</label><br>
                    <select id="threat-type" name="threatType" class="form-control" v-model="form.threat_type">
                        <option value="all">All</option>
                        <option value="bot">BOT</option>
                        <option value="dev_tools">DEV_TOOLS</option>
                        <option value="copy_paste">COPY_PASTE</option>
                        <option value="private_mode">PRIVATE_MODE</option>
                        <option value="vitual_machine">VIRTUAL_MACHINE</option>
                        <option value="suspicious_ipe">SUSPICIOUS_IPE</option>
                        <option value="phishing">PHISHING</option>
                        <option value="new_country">NEW_COUNTRY</option>
                        <option value="new_city">NEW_CITY</option>
                        <option value="trojan">TORJAN</option>
                    </select>
                </div>
            </div>
            <div class="w-33 w-xs-100 mb-30 first-icon-class">
                <div class="form-group">
                    <label for="threat-severity">Threat Severity</label><br>
                    <select id="threat-severity" name="threatSeverity" class="form-control" v-model="form.threat_severity">
                        <option value="all">All</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            <div class="w-33 w-xs-100 mb-30 first-icon-class">
                <div class="form-group">
                    <label for="platform">Platform</label><br>
                    <select id="platform" name="Platform" class="form-control" v-model="form.platform">
                        <option value="all">All</option>
                        <option value="web">Web</option>
                        <option value="mobile">Mobile</option>
                        <option value="desktop">Desktop</option>
                    </select>
                </div>
            </div>
            <div class="w-33 w-xs-100 mb-30 first-icon-class">
                <div class="form-group">
                    <label>Start Date</label><br>
                    <datetime
                        v-model="start_date"
                        type="date"
                        id="startdate"
                        input-class="form-control"
                        format="dd-MM-yyyy"
                        use12-hour
                        @close="updateForm('start_date')">
                    </datetime>
                </div>
            </div>
            <div class="w-33 w-xs-100 mb-30 first-icon-class">
                <div class="form-group">
                    <label>End Date</label><br>
                    <datetime
                        v-model="end_date"
                        type="date"
                        id="enddate"
                        input-class="form-control"
                        format="dd-MM-yyyy"
                        use12-hour
                        @close="updateForm('end_date')">
                    </datetime>
                </div>
            </div>
            <div class="w-33 w-xs-100 mb-30 first-icon-class">
                <div class="form-group">
                    <label></label><br>
                    <button type="button" class="form-control btn-filter-threat" @click="getData()"><i class="fa fa-filter mr-10" aria-hidden="true"></i>Filter Threats</button>
                </div>
            </div>

            <div class="w-100 w-xs-100">
                <div class="app-sub-card app-sub-card-padding mb-30">
                    <div class="threat-vuetable">
                        <vuetable
                            ref="vuetable"
                            :api-mode="false"
                            :data-manager="dataManager"
                            :fields="fields"
                            :per-page="perPage"
                            @vuetable:pagination-data="onPaginationData"
                            pagination-path="pagination"
                        >

                            <template slot="Severity" slot-scope="props">
                                <span v-if="props.rowData.Severity >= 90" class="severity severity-high">High</span>
                                <span v-else-if="props.rowData.Severity >= 50" class="severity severity-medium">Medium</span>
                                <span v-else class="severity severity-low">Low</span>
                            </template>
                            <template slot="actions" slot-scope="props">
                                <button type="button" @click="openModel(props.rowData)" class="slot-button-review">Review</button>
                            </template>
                        </vuetable>
                    </div>
<!--                    <div class="app-card-header">-->
<!--                        <h6 class="mb-0"></h6>-->
<!--                    </div>-->
<!--                    <div style="overflow-x: auto;">-->
<!--                        <table class="app-table threat-list">-->
<!--                            <thead>-->
<!--                            <tr><th>Date</th>-->
<!--                                <th>Time</th>-->
<!--                                <th>Threat Type</th>-->
<!--                                <th>Severity</th>-->
<!--                                <th>StoreID</th>-->
<!--                                <th>Customer</th>-->
<!--                                <th>Action</th>-->
<!--                            </tr></thead>-->
<!--                            <tbody>-->
<!--                            <tr v-if="threats.length > 0" v-for="(item, index) in threats" :key="index">-->
<!--                                <td>{{item.DisplayDate }}</td>-->
<!--                                <td>{{item.DisplayTime }}</td>-->
<!--                                <td>{{item.Type}}</td>-->
<!--                                <td>-->
<!--                                    <span v-if="item.Severity >= 90" class="severity severity-high">High</span>-->
<!--                                    <span v-else-if="item.Severity >= 50" class="severity severity-medium">Medium</span>-->
<!--                                    <span v-else class="severity severity-low">Low</span>-->
<!--                                </td>-->
<!--                                <td>{{item.ID}}</td>-->
<!--                                <td>{{item.CustomerName}}</td>-->
<!--                                <td><a @click="openModel(index)" style="cursor:pointer;"><i class="fa fa-eye"></i></a></td>-->
<!--                            </tr>-->
<!--                            <tr v-if="threats.length == 0">-->
<!--                                <td colspan="6" style="text-align: center;">No any threats found!</td>-->
<!--                            </tr>-->
<!--                            </tbody>-->
<!--                        </table>-->
<!--                    </div>-->
                </div>
                <vuetable-pagination ref="pagination" :css="css.pagination"
                                     @vuetable-pagination:change-page="onChangePage"
                ></vuetable-pagination>
            </div>
        </div>

        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close threat-close"  @click="closeModel()">&times;</span>
                        <h4 class="modal-title">Threat Details</h4>
                        <span class="mr-10 model-span">{{modelData.DisplayDate}}</span><span class="model-span">{{modelData.DisplayTime}}</span>
                    </div>
                    <div class="threat-model modal-body app-row">
                        <div class="w-33 w-xs-100 mb-30 first-icon-class">
                            <p>Customer</p><span class="model-span">{{modelData.CustomerName}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class"></div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class"></div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Type</p><span class="model-span">{{modelData.ThreatType}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Severity</p>
                            <span class="model-span">
                                 <span v-if="modelData.Severity >= 90" class="severity severity-high">High</span>
                                    <span v-else-if="modelData.Severity >= 50" class="severity severity-medium">Medium</span>
                                    <span v-else class="severity severity-low">Low</span>
                            </span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Platform</p><span class="model-span">{{modelData.Platform}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Origin</p><span class="model-span">{{modelData.Country}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>City</p><span class="model-span">{{modelData.City}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>IP Address</p><span class="model-span">{{modelData.IPAddress}}</span>
                        </div>
                        <div class="w-100 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Cookie</p><span class="model-span">{{modelData.Cookie}}</span>
                        </div>
                        <div class="w-100 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Message</p><span class="model-span">{{modelData.Messages}}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-filter-threat" @click="closeModel()">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Loader from "./Loader";
import {Datetime} from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css'
import moment from 'moment';
import Vuetable from 'vuetable-2/src/components/Vuetable';
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
import helper from "../../helper";

export default {
    components: {
        Loader,
        datetime: Datetime,
        Vuetable,
        VuetablePagination
    },
    data() {
        return {
            loading: true,
            start_date: new Date(new Date().setDate(new Date().getDate()-6)).toISOString(),
            end_date: new Date().toISOString(),
            form:{
                unm: 'mark',
                start_date: '',
                end_date: '',
                threat_type: 'all',
                threat_severity: 'all',
                platform: 'all',
            },
            threats: [],
            modelData: [],
            perPage: 10,
            fields: [
                { name: 'DisplayDate', title: 'Date', class: 'vuetable-displaydate'},
                { name: 'DisplayTime', title: 'Time'},
                { name: 'ThreatType', title: 'Threat Type'},
                { name: '__slot:Severity', title: 'Severity'},
                { name: 'IPAddress', title: 'IP Address'},
                { name: 'Country', title: 'Country'},
                '__slot:actions'
            ],
            css: {
                table: {
                    tableClass: 'table table-striped table-bordered table-hovered',
                    loadingClass: 'loading',
                    ascendingIcon: 'glyphicon glyphicon-chevron-up',
                    descendingIcon: 'glyphicon glyphicon-chevron-down',
                    handleIcon: 'glyphicon glyphicon-menu-hamburger',
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
        }
    },
    methods:{
        async getData(){
            this.loading = true;
            let base = this;
            let url = 'get-threat-intelligence';
            let method = 'post';
            await axios({
                url: url,
                data: {  'data': base.form, },
                method: method,
            }).then(function (res) {
                base.threats = res.data.data;
            }).catch(function (err) {
                console.log(err);
            }).finally(function (res) {
                base.loading = false;
            });
        },
        updateForm(type){
            if( type == 'start_date' ){
                this.form.start_date = moment(this.start_date).format("yyyy-MM-DD HH:mm:ss");
            }else{
                this.form.end_date = moment(this.end_date).format("yyyy-MM-DD HH:mm:ss");
            }

        },
        closeModel(){
            var modal = document.getElementById("myModal");
            $('#myModal').hide();
        },
        openModel(data){
            this.modelData = data;
            $('#myModal').slideDown();
            // var modal = document.getElementById("myModal");
            // modal.style.display = "block";
        },
        documentClick(event){
            // Get the modal
            var modal = document.getElementById("myModal");

            // When the user clicks anywhere outside of the modal, close it
                if (event.target === modal) {
                    modal.style.display = "none";
                }
        },
        dataManager(sortOrder, pagination) {
            if (this.threats.length < 1) {
                return {
                    pagination: [],
                    data: _.slice([], 0, 0)
                };
            }

            let local = this.threats;

            // sortOrder can be empty, so we have to check for that as well
            if (sortOrder.length > 0) {
                console.log("orderBy:", sortOrder[0].sortField, sortOrder[0].direction);
                local = _.orderBy(
                    local,
                    sortOrder[0].sortField,
                    sortOrder[0].direction
                );
            }

            pagination = this.$refs.vuetable.makePagination(
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
            this.$refs.pagination.setPaginationData(paginationData)
        },
        onChangePage (page) {
            this.$refs.vuetable.changePage(page)
        },

    },
    mounted(){
        // this.form.start_date = moment().format("DD-MM-yyyy HH:mm:ss");
        this.form.start_date = moment().subtract(6, 'd').format('yyyy-MM-DD HH:mm:ss');
        this.form.end_date = moment().format("yyyy-MM-DD HH:mm:ss");
        if( typeof this.$route.params.type != 'undefined' && this.$route.params.type != '' ){
            let str = this.$route.params.type;
            this.form.threat_type = str.toLowerCase();
            this.getData();
        }else{
            this.getData();
        }

        document.onclick = this.documentClick;
        helper.bannerShow();
    },
    watch: {
        threats(newVal, oldVal) {
            this.$refs.vuetable.refresh();
        }
    }
}
</script>
