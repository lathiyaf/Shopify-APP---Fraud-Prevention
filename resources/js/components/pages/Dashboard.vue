<template>
    <div class="app-body">
        <loader :loading="loading"></loader>

        <div class="d-flex" :style="[loading ? {opacity: 0.3} : {opacity: 1}]" style="max-height: 12%;">
            <h3 class="mb-20 mr-10 fw-500">Dashboard - {{filterDays}} Days Summary</h3>
            <div class="card__control-timeframe dropleft btn-group-sm dropdown">
                <a aria-haspopup="true" style="cursor: pointer;" class="d-flex drp-filter" aria-expanded="true" @click="is_visitor_drp = !is_visitor_drp">
                    <p style="margin: 5px 0px 0px 50px;">Filter Data</p>
                    <svg class="mdi-icon " width="24" height="24" fill="currentColor" viewBox="0 0 24 24" style="margin: 5px;" >
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z">
                        </path>
                    </svg>

                </a>
                <div tabindex="-1" v-if="is_visitor_drp" role="menu" aria-hidden="false" class="dropdown__menu dropdown-menu drp-menu" style="will-change: transform;transform: translate3d(-112px, 0px, 0px);" x-placement="left-start">
                    <button type="button" value="7" tabindex="0" role="menuitem" class="dropdown-item" @click="filterDays=7,getData(7)">Last 7 days</button>
                    <button @click="filterDays=14,getData( 14)" type="button" value="14" tabindex="0" role="menuitem" class="dropdown-item">Last 14 days</button>
                    <button @click="filterDays=21,getData(21)" type="button" value="28" tabindex="0" role="menuitem" class="dropdown-item">Last 21 days</button>
                    <button @click="filterDays=30,getData(30)" type="button" value="last_month" tabindex="0" role="menuitem" class="dropdown-item">Last month</button>
                </div>
            </div>
        </div>
        <div :style="[loading ? {opacity: 0.3} : {opacity: 1}]" class="app-row dashboard-icon-main-div">
            <div class="w-25 w-xs-100 mb-30 first-icon-class">
                <div class="app-card">
                    <div class="app-card-body d-flex align-items-center mb-20 justify-content-between">
                        <div class="dashboard-icon">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>

                        <div class="ml-15">
                            <h6 class="mb-0 fw-500">Site Visitors</h6>
                            <h2 class="h3">{{ (data.live.live_users) ? data.live.live_users : 0}}</h2>
                        </div>
<!--                        <div class="ml-15">-->
<!--                            <line-chart height="60" width="100" :chart-data="dataUsers"></line-chart>-->
<!--                        </div>-->
                    </div>
                    <div class="app-card-footer">
                        <a href="#" class="d-flex align-items-center    ">
                            <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
                            {{data.live.perUser}}
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-25 w-xs-100 mb-30 second-icon-class">
                <div class="app-card">
                    <div class="app-card-body d-flex align-items-center mb-20 justify-content-between">
                        <div class="dashboard-icon">

                            <i class="fas fa-user-secret" aria-hidden="true"></i>

                        </div>
                        <div class="ml-15">
                            <h6 class="mb-0 fw-500">Site Threats</h6>
                            <h2 class="h3">{{(data.live.live_threats) ? data.live.live_threats : 0}}</h2>
                        </div>
<!--                        <div class="ml-15">-->
<!--                            <line-chart height="60" width="100" :chart-data="dataLiveThreats"></line-chart>-->
<!--                        </div>-->
                    </div>
                    <div class="app-card-footer">
                        <a href="#" class="d-flex align-items-center">
                            <i class="fa fa-line-chart mr-1" aria-hidden="true"></i>
                            {{data.live.perThreats}}
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-25 w-xs-100 mb-30 third-icon-class">
                <div class="app-card">
                    <div class="app-card-body d-flex align-items-center mb-20 justify-content-between">
                        <div class="dashboard-icon">
                            <i class="fas fa-bug" aria-hidden="true"></i>
                        </div>
                        <div class="ml-15">
                            <h6 class="mb-0 fw-500">High Risk Threats</h6>
                            <h2 class="h3" style="color:red;">{{(data.live.high_risk_threats) ? data.live.high_risk_threats : 0}}</h2>
                        </div>
<!--                        <div class="ml-15">-->
<!--                            <line-chart height="60" width="100" :chart-data="dataHighRiskThreats"></line-chart>-->
<!--                        </div>-->
                    </div>
                    <div class="app-card-footer">
                        <a href="#" class="d-flex align-items-center">
                            <i class="fa fa-bar-chart mr-1" aria-hidden="true"></i>
                            {{data.live.perHighThreats}}
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-25 w-xs-100 mb-30 fourth-icon-class">
                <div class="app-card">
                    <div class="app-card-body d-flex align-items-center mb-20 justify-content-between">
                        <div class="dashboard-icon">
                            <i class="fa fa-files-o highlight-icon" aria-hidden="true"></i>
                        </div>

                        <div class="ml-15">
                            <h6 class="mb-0 fw-500">Page Views</h6>
                            <h2 class="h3">{{(data.live.page_views) ? data.live.page_views : 0}}</h2>
                        </div>
                        <!--                        <div class="ml-15">-->
                        <!--                            <line-chart height="60" width="100" :chart-data="dataHighRiskThreats"></line-chart>-->
                        <!--                        </div>-->
                    </div>
                    <div class="app-card-footer">
                        <a href="#" class="d-flex align-items-center">
                            <i class="fa fa-area-chart mr-1" aria-hidden="true"></i>
                            {{data.live.perPageView}}
                        </a>
                    </div>
                </div>
            </div>

<!--            site visitors-->
            <div class="w-100 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Site Visitors</h3>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="card__info" v-if="data.Visitors.data.length > 0">
                            <strong class="pr-2" ><span>{{total_visitor}}</span></strong><span>Visits</span>
                        </div>

                        <div v-if="data.Visitors.data.length == 0">
                            <span colspan="4"> No data found... </span>
                        </div>
                        <apex-chart width="95%" height="200" type="area" :options=visitorChart :series=dataVisitors></apex-chart>
<!--                        <line-chart :chart-data="dataVisitors" :styles="myStyles"></line-chart>-->
                    </div>
                    <div class="app-card-footer">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                    </div>
                </div>
            </div>
            <!--            threat detections-->
            <div class="w-50 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Threat Detections</h3>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="card__info"  v-if="data.Threats.data.length > 0">
                            <strong class="pr-2"><span>{{total_threat}}</span></strong><span>Threats</span>
                        </div>

                        <tr v-if="data.Threats.data.length == 0">
                            <td colspan="4"> No data found... </td>
                        </tr>
                        <apex-chart style="width: 100%" height="300" type="bar" :options=threatChart :series=dataThreats></apex-chart>
                        <!--                        <bar-chart :chart-data="dataThreats" :styles="myStyles"></bar-chart>-->
                    </div>
                    <div class="app-card-footer">
                        <div class="d-flex align-items-center justify-content-between">
<!--                            <a>0 of 0 sessions were from users that fell below your Risk score threshold</a>-->
<!--                            <a href="#" class="d-flex align-items-center">-->
<!--                                View details-->
<!--                                <span class="ml-15"><img src="images/arrow-righte.svg"/></span>-->
<!--                            </a>-->
                        </div>
                    </div>
                </div>
                <!--                <div class="app-card">-->
                <!--                    <div class="app-card-header">-->
                <!--                        <div class="d-flex align-items-center justify-content-between">-->
                <!--                            <h3 class="mb-0">Lowest Quality Campaigns</h3>-->
                <!--                            <input type="date" class="cander-icon" />-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="app-card-body">-->
                <!--                        <table class="app-table">-->
                <!--                            <thead>-->
                <!--                            <th>Latest campaign</th>-->
                <!--                            <th>Risk score</th>-->
                <!--                            <th>Sessions</th>-->
                <!--                            </thead>-->
                <!--                            <tbody>-->
                <!--                            <tr>-->
                <!--                                <td>Eom Flash Sale</td>-->
                <!--                                <td>34</td>-->
                <!--                                <td>236</td>-->
                <!--                            </tr>-->
                <!--                            <tr>-->
                <!--                                <td>Eom Flash Sale</td>-->
                <!--                                <td>34</td>-->
                <!--                                <td>236</td>-->
                <!--                            </tr>-->
                <!--                            <tr>-->
                <!--                                <td>Eom Flash Sale</td>-->
                <!--                                <td>34</td>-->
                <!--                                <td>236</td>-->
                <!--                            </tr>-->
                <!--                            </tbody>-->
                <!--                        </table>-->
                <!--                    </div>-->
                <!--                    <div class="app-card-footer">-->
                <!--                        <a href="#" class="d-flex align-items-center justify-content-between">-->
                <!--                            View details-->
                <!--                            <span><img src="images/arrow-righte.svg" /></span>-->
                <!--                        </a>-->
                <!--                    </div>-->
                <!--                </div>-->
            </div>

<!--            page views-->
            <div class="w-50 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Page Views</h3>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="card__info" v-if="data.Pages.data.length > 0">
                            <strong class="pr-2"><span>{{total_views}}</span></strong><span>Views</span>
                        </div>
                        <tr v-if="data.Pages.data.length == 0">
                            <td colspan="4"> No data found... </td>
                        </tr>
                        <apex-chart style="width: 100%" height="300" type="bar" :options=pageChart :series=dataPages></apex-chart>
<!--                        <bar-chart :chart-data="dataPages" :styles="myStyles"></bar-chart>-->
                    </div>
                    <div class="app-card-footer">
                        <div class="d-flex align-items-center justify-content-between">
<!--                            <a>0 of 0 sessions were from users that fell below your Risk score threshold</a>-->
<!--                            <a href="#" class="d-flex align-items-center">-->
<!--                                View details-->
<!--                                <span class="ml-15"><img src="images/arrow-righte.svg"/></span>-->
<!--                            </a>-->
                        </div>
                    </div>
                </div>
            </div>

<!--            country chart and threats by type-->
            <div class="w-70 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Threats Origin</h3>
                        </div>
                    </div>
                    <div class="app-card-body" id="test">
<!--                        <tr v-if="typeof(data.ThreatOrigin.selected) != 'undefined' && data.ThreatOrigin.selected.length == 0">-->
<!--                            <td colspan="4"> No data found... </td>-->
<!--                        </tr>-->
                        <div id="vmap" style="width: 100%; height: 600px;"></div>
                    </div>
                    <div class="app-card-footer">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-33 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Threat Classification</h3>
                        </div>
                    </div>
                    <div class="app-card-body mb-20 mt-10">
                        <div class="card__table">
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover" cellspacing="0" style="margin-top: 10px;width: 100%;">
                                    <tbody>
                                    <tr v-if="typeof(data.ThreatClassification) != 'undefined' && data.ThreatClassification.length == 0">
                                        <td colspan="4"> No data found... </td>
                                    </tr>
                                    <tr v-else v-for="(item, index, i) in data.ThreatClassification" :key="i">
                                        <td><span v-if="item > 50" class="threat-classi high-threat">{{item}}</span>
                                            <span v-else class="threat-classi low-threat">{{item}}</span></td>
                                        <td>{{index}}</td>
                                        <td>
                                            <a style="cursor: pointer;" @click="changePage(index)"><svg class="mdi-icon " width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"></path></svg></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--            ips-->
            <div class="w-33 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Top Malicious IPs</h3>
                        </div>
                    </div>
                    <div class="app-card-body mb-20 mt-10">
                            <div class="card__table">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover" cellspacing="0" style="margin-top: 10px;width: 100%;">
                                        <tbody>
                                        <tr v-if="typeof(data.TopMaliciousIP) != 'undefined' && data.TopMaliciousIP.length == 0">
                                            <td colspan="4"> No data found... </td>
                                        </tr>
                                        <tr v-else v-for="(item, index) in data.TopMaliciousIP" :key="index">
                                            <td><i class="fa fa-square" :style=ips(item.Percentage)></i></td>
                                            <td>{{item.IPAddress}}</td>
                                            <td>{{item.Percentage}}%</td>
                                            <td>
                                                <a style="cursor: pointer;" @click="openModel(item)"><svg class="mdi-icon " width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"></path></svg></a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="w-33 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Top Attackers</h3>
                        </div>
                    </div>
                    <div class="app-card-body mb-20 mt-10">
                        <div class="card__table">
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover" cellspacing="0" style="margin-top: 10px;width: 100%;">
                                    <tbody>
                                    <tr v-if="typeof(data.TopAttackersIP) != 'undefined' && data.TopAttackersIP.length == 0">
                                        <td colspan="4"> No data found... </td>
                                    </tr>
                                    <tr v-else v-for="(item, index) in data.TopAttackersIP" :key="index">
                                        <td><i class="fa fa-square" :style=ips(item.Percentage)></i></td>
                                        <td>{{item.IPAddress}}</td>
                                        <td>{{item.Percentage}}%</td>
                                        <td>
                                            <a style="cursor: pointer;" @click="openModel(item)"><svg class="mdi-icon " width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"></path></svg></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-33 w-xs-100 mb-30">
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="mb-0">Suspicious IPs</h3>
                        </div>
                    </div>
                    <div class="app-card-body mb-20 mt-10">
                        <div class="card__table">
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover" cellspacing="0" style="margin-top: 10px;width: 100%;">
                                    <tbody>
                                    <tr v-if="typeof(data.TopSuspiciousIP) != 'undefined' && data.TopSuspiciousIP.length == 0">
                                        <td colspan="4"> No data found... </td>
                                    </tr>
                                    <tr v-else v-for="(item, index) in data.TopSuspiciousIP" :key="index">
                                        <td><i class="fa fa-square" :style=ips(item.Percentage)></i></td>
                                        <td>{{item.IPAddress}}</td>
                                        <td>{{item.Percentage}}%</td>
                                        <td>
                                            <a style="cursor: pointer;" @click="openModel(item)"><svg class="mdi-icon " width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"></path></svg></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div id="myThreatModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="position: relative;">
                        <span class="close"  @click="closeModel()">&times;</span>
                        <h4 class="modal-title">Threat Details</h4>
                    </div>
                    <div class="threat-model modal-body app-row">
                        <div class="w-33 w-xs-100 mb-30 first-icon-class">
                            <p>IP Address</p><span class="model-span">{{modelData.IPAddress}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Percentage</p><span class="model-span">{{modelData.Percentage}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Total Attacks</p><span class="model-span">{{modelData.Total}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Severity</p>
                            <span class="model-span">
                                 <span v-if="modelData.Severity >= 90" class="severity severity-high">High</span>
                                    <span v-else-if="modelData.Severity >= 50" class="severity severity-medium">Medium</span>
                                    <span v-else class="severity severity-low">Low</span>
                            </span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Score</p><span class="model-span">{{modelData.Score}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Threat Types</p><span class="model-span">{{modelData.ThreatTypes}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>City</p><span class="model-span">{{modelData.City}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Country</p><span class="model-span">{{modelData.Country}}</span>
                        </div>
                        <div class="w-33 w-xs-100 mb-10 first-icon-class">
                            <p>Last Threat Date</p><span class="model-span">{{modelData.LastThreatDate}}</span>
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

    import moment from 'moment';
    import LineChart from '../../LineChart';
    import ApexChart from 'vue-apexcharts';
    import 'jqvmap/dist/jqvmap.css';
    import Loader from "./Loader";
    import helper from '../../helper';

    export default {
        components: {
            ApexChart,
            LineChart,
            Loader
        },
        data() {
            return {
                filterDays: 7,
                loading: true,
                dataUsers: null,
                dataLiveThreats: null,
                dataHighRiskThreats: null,
                total_visitor: 0,
                total_views: 0,
                total_threat: 0,
                is_visitor_drp: false,
                dataVisitors: [{
                    name : 'visits',
                    data: []
                }],
                dataThreats: [{
                    name : 'threats',
                    data: []
                }],
                dataPages: [{
                    name : 'views',
                    data: []
                }],
                dateFrom: '',
                dateTo: '',
                threatFrom: '',
                threatTo: '',
                visitorFrom: '',
                visitorTo: '',
                data: {
                    live: {
                        live_users: 0,
                        live_threats: 0,
                        high_risk_threats: 0,
                        page_views: 0,
                        perUser: 0,
                        perThreats: 0,
                        perHighThreats: 0,
                        perPageView: 0,
                    },
                    risk_score: '',
                    latest_suspicious_order: [],
                    suspicious_order: '',
                    Visitors: {
                        'labels': [],
                        'data': [],
                    },
                    Threats: {
                        'labels': [],
                        'data': [],
                    },
                    Pages: {
                        'labels': [],
                        'data': [],
                    },
                    ThreatOrigin:{
                        selected: [],
                    }
                },
                visitorChart: {
                    markers: {
                        show: true,
                        size: 4,
                        colors: ['#963dff'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        strokeOpacity: 0.9,
                        strokeDashArray: 0,
                        fillOpacity: 1,
                        discrete: [],
                        shape: "circle",
                        radius: 2,
                        offsetX: 0,
                        offsetY: 0,
                        showNullDataPoints: true,
                        hover: {
                            size: undefined,
                            sizeOffset: 3
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -5,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    stroke: {
                        show: true,
                        curve: 'smooth',
                        lineCap: 'butt',
                        colors: ['#963dff'],
                        width: 2,
                        dashArray: 0,
                    },
                    colors: ['#2E93fA'],
                    yaxis: {
                        show: false,
                    },
                    grid: {
                        show: false,
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            gradientToColors: ['#bbe6fd', '#d3e5fb', '#eaecfc', '#faf9fe'],
                            shadeIntensity: 0.3,
                            opacityFrom: 0.7,
                            opacityTo: 0.1,
                            stops: [],
                            inverseColors: false,
                        },
                    },
                    xaxis: {
                        borderColor: '#963dff',
                        type: 'days',
                    },
                    tooltip:{
                        x: {
                            show: false,
                        },
                    }
                },
                threatChart: {
                    yaxis: {
                        show: false,
                    },
                    grid: {
                        show: false,
                    },
                    colors:['#f07f7b'],
                    tooltip:{
                        enabled: false
                    },
                    // fill: {
                    //     type: 'gradient',
                    //     gradient: {
                    //         shade: 'light',
                    //         type: "vertical",
                    //         gradientToColors: ['#f07f7b', '#f8ae98', '#fccbb0', '#ffe3cb'],
                    //         shadeIntensity: 0.3,
                    //         opacityFrom: 1,
                    //         opacityTo: 0.7,
                    //         stops: [0, 50,70],
                    //         inverseColors: false,
                    //     },
                    // },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            gradientToColors: ['#f07f7b', '#f8ae98', '#fccbb0', '#ffe3cb'],
                            shadeIntensity: 1,
                            opacityFrom: 1,
                            opacityTo: 0.5,
                            stops: [],
                            inverseColors: false,
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                            columnWidth: '40%'
                        }
                    },
                    xaxis: {
                        borderColor: '#963dff',
                        type: 'days',
                    },
                },
                pageChart: {
                    yaxis: {
                        show: false,
                    },
                    grid: {
                        show: false,
                    },
                    tooltip:{
                        enabled: false
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                            columnWidth: '40%'
                        },
                    },
                    colors: ['#60fabd'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            gradientToColors: ['#97fcb4', '#7afbb8', '#8efcb5', '#8cfc8d', '#7bfb8c', '#bcfeb8', '#e1ffc9', '#e1ffc9'],
                            shadeIntensity: 0.3,
                            opacityFrom: 1,
                            opacityTo: 0.9,
                            stops: [0, 50,70],
                            inverseColors: false,
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    xaxis: {
                        borderColor: '#963dff',
                        type: 'days',
                    },
                },
                modelData: [],
            }
        },
        methods: {
            async getData(days) {
                this.loading = true;
                let start = moment().subtract(days - 1, 'd').format('YYYY-MM-DD');
                let end = moment().format('YYYY-MM-DD');
                let dates = [start, end];
                let base = this;
                let url = 'dashboard?days=' + days;
                let method = 'get';
                await axios({
                    url: url,
                    method: method,
                }).then(function (res) {
                    base.loading = false;

                    if( res.data.data.isSuccessfull ){
                        base.data = res.data.data.data;
                        // base.fillSmallVisitorData();
                        base.fillVisitorsData(start, days);
                        base.fillThreatsData(start, days);
                        base.fillPagesData(start, days);
                        base.initmap();
                    }

                }).catch(function (err) {
                    console.log(err);
                });
            },
            fillSmallVisitorData(){
                this.dataUsers = {
                    labels: this.data.Visitors.labels,
                    datasets: [
                        {
                            label: '',
                            backgroundColor: '#eae0f2',
                            borderColor: '#710abd',
                            data: this.data.Visitors.data
                        },
                    ]
                }
                this.dataLiveThreats = {
                    labels: this.data.Threats.labels,
                    datasets: [
                        {
                            label: '',
                            backgroundColor: '#eae0f2',
                            borderColor: '#710abd',
                            data: this.data.Threats.data
                        },
                    ]
                }
                this.dataHighRiskThreats = {
                    labels: this.data.HighRiskThreats.data,
                    datasets: [
                        {
                            label: '',
                            backgroundColor: '#eae0f2',
                            borderColor: '#710abd',
                            data: this.data.HighRiskThreats.data
                        },
                    ]
                }
            },
            fillVisitorsData(start, days) {
                this.data.Visitors.labels = this.getLabel(start, days);

                this.dataVisitors = [{
                    name : 'visits',
                    data: this.data.Visitors.data
                }];
                this.visitorChart = {
                    xaxis: {
                        type: 'date',
                        borderColor: '#963dff',
                        categories: this.data.Visitors.labels
                    },
                };
                this.total_visitor = this.data.Visitors.total;
            },
            getLabel(start, days){
                let dayNames = moment.weekdays();
                var data = [];
                for (let i = 0; i < days; i++) {
                    if( days == 7 ){
                        let d = moment(moment(start).add(i, 'days').format("YYYY-MM-DD")).day();
                        data.push(dayNames[d].substring(0, 3));
                    }else{
                        let d = moment(start).add(i, 'days').format("DD-MM");
                        data.push(d);
                    }
                };
                return data;
            },
            fillThreatsData(start, days) {;
                this.data.Threats.labels = this.getLabel(start, days);;
                this.dataThreats = [{
                    name : 'Threats',
                    data: this.data.Threats.data
                }];
                this.threatChart = {
                    dataLabels: {
                        position: 'top',
                        enabled: true,
                    },
                    xaxis: {
                        type: 'days',
                        borderColor: '#963dff',
                        categories: this.data.Threats.labels
                    },
                };
                this.total_threat = this.data.Threats.total;
            },
            fillPagesData(start, days) {
                this.data.Pages.labels = this.getLabel(start,days);
                this.dataPages = [{
                    name : 'visits',
                    data: this.data.Pages.data
                }];
                this.pageChart = {
                    xaxis: {
                        type: 'days',
                        borderColor: '#963dff',
                        categories: this.data.Pages.labels,
                    },
                };
                this.total_views = this.data.Pages.total;
            },
            initmap(){
                let base = this;
                $('#vmap').vectorMap({
                    map: 'world_en',
                    color: '#f4f3f0',
                    showTooltip: true,
                    hoverColor: '#c9dfaf',
                    hoverColors: {},
                    scaleColors: ['#b6d6ff', '#005ace'],
                    normalizeFunction: 'linear',
                    enableZoom: true,
                    borderColor: '#818181',
                    borderWidth: 1,
                    borderOpacity: 0.5,
                    selectedColor: '#7797b3',
                    selectedRegions: base.data.ThreatOrigin.selected,
                    backgroundColor: '#ffffff',
                    hoverOpacity: 0.7,

                    onRegionClick: function(event, code, region)
                    {
                        event.preventDefault();
                    },
                    onLabelShow: function (event, label, code) {
                        let selectedRegions =  base.data.ThreatOrigin.selected;
                        if(selectedRegions.indexOf(code.toUpperCase()) !== -1){
                            label.append('<br><span style="font-weight: 100;">Detections: <strong>' + base.data.ThreatOrigin.code[code.toUpperCase()] + '</strong></span>');
                        }else{
                            event.preventDefault();
                        }
                    }
                });
            },
            getRandomInt () {
                return Math.floor(Math.random() * (50 - 5 + 1)) + 5
            },
            ddocumentClick(e){
                let base = this;
                let filter_selector = $('.drp-filter');
                if (!filter_selector.is(e.target) && filter_selector.has(e.target).length === 0) {
                    base.is_visitor_drp=false; // problem is this value false for first match dropdown only.
                }

                let fs = $('.setting-dp');
                let elem = document.getElementById("setting-dp");
                if (!fs.is(e.target) && fs.has(e.target).length === 0) {

                    if(elem)
                    {
                        elem.style.display = "none";
                    }
                }else{
                    if(fs.has(e.target).length > 0){
                        elem.style.display = "block";
                    }
                }

            },
            ips(per){
                if( per > 80 ){
                    return {
                        color: '#dc3545',
                    }
                }else if( per > 50 ){
                    return {
                        color: '#ff6000',
                    }
                }else{
                    return {
                        color: '#ffc107',
                    }
                }
            },
            closeModel(){
                $('#myThreatModal').hide();
            },
            openModel(data){
                this.modelData = data;
                $('#myThreatModal').slideDown();
            },
            changePage(type){
                this.$router.push({name: 'threat-intelligence-listing', params: {type: type}})
            }
        },
        created() {
            document.onclick = this.ddocumentClick;
            helper.bannerShow();
        },
        mounted() {
            if( window.ordrId != 0 ){
                this.$router.push({name: 'order-details', params: {order_id: window.ordrId}})
            }else{
                this.getData(7);
            }
        },
        computed: {
            myStyles() {
                return {
                    height: '300px',
                    position: 'relative'
                }
            },


        }
    }
</script>

<style>
    .small {
        max-width: 300px;
        margin: 150px auto;
    }
    .jqvmap-label
    {
        background-color: white;
        color: black;
        border: 1px solid #cacaca;
        font-weight: 700;
        padding: 0.5rem !important;
    }
</style>
