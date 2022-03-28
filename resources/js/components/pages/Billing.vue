<template>
    <div class="app-body">
        <loader :loading="loading"></loader>
        <div :style="[loading ? {opacity: 0.3} : {opacity: 1}]" class="app-row">
            <div class="app-card-body" style="overflow: hidden;">
                <div class="w-100 w-xs-100 setting-page-mr-class">
                    <div class="app-sub-card app-sub-card-padding mb-30">
                        <div class="app-card-header">
                            <h6 class="mb-0">Billing</h6>
                        </div>
                        <div style="overflow-x: auto;">
                            <div class="w-100 w-xs-100 mb-30">
                                <div class="w-xs-100 mb-30">
                                    <h6 class="mb-0">Account Status</h6>
                                    <p>This is the current status of your Cybertonica account, including your chosen plan type and how many days you have left for either a free trial or an annual contract.</p>
                                </div>
                                <div class="app-card w-xs-100 mb-30">
                                    <h6 class="mb-0" v-if="trial.is_show">Active: Free Trial</h6>
                                    <h6 class="mb-0" v-else-if="plan.plan_id == 1">Active: Starter</h6>
                                    <h6 class="mb-0" v-else-if="plan.plan_id == 2">Active: Premium</h6>
                                    <div class="align-items-center">
                                        <div>
                                            <p><b>Thank you for choosing Cybertonica.</b></p>
<!--                                                <p>Contact Sales at Cybertonica by email or at 888-453-5291 with any questions about pricing and plans.</p>-->
                                        </div>
                                    </div>


<!--                                    <p class="small-text">Total Credit: <b>{{plan.total_credit}}</b></p>-->
<!--                                    <p class="small-text">Available Credit: <b>{{plan.available_credit}}</b></p>-->
                                    <h6 class="mb-0">Available Plans:</h6>
                                    <div class="choose-plan-row" id="plan-price">
                                        <div class="col bill-col">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="title">Standard</h2>

                                                    <p class="price">
                                                        $29/Month
                                                    </p>
                                                    <!--                        <span class="description">Perfect for Online Store</span>-->
                                                    <div class="line"></div>
                                                    <p>Start your 30-day free trial.</p>
                                                </div>
                                                <ul class="features">
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>30-day free trial</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>30,000 scores per month*</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Order fraud protection</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Real-time threat analysis</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Behavioural analytics</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Customer authentication</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Custom risk rules</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Analytics & alerts</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>$1.20 USD per 1,000
                                                        additional scores/month</li>
                                                </ul>
                                                <p>*score = total number of page
                                                    views + orders</p>
                                                <div v-if="plan.plan_id == 1" class="alert alert-success">
                                                    Current Plan
                                                </div>
                                                <a href="/billing/1" v-else target="_parent" class="btn btn-success btn-plan">Select</a>
                                            </div>
                                        </div>
                                        <div class="col bill-col">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="title">Premium</h2>

                                                    <p class="price">
                                                        $79/Month
                                                    </p>
                                                    <!--                        <span class="description">Perfect for Online Store</span>-->
                                                    <div class="line" ></div>
                                                    <p>Start your 30-day free trial.</p>
                                                </div>
                                                <ul class="features">
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>30-day free trial</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>100,000 scores per month*</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Order fraud protection</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Real-time threat analysis</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Behavioural analytics</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Customer authentication</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Custom risk rules</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>Priority customer support</li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i>$1 USD per 1,000 additional
                                                        scores/month</li>
                                                </ul>
                                                <p>*score = total number of page views +
                                                    orders</p>
                                                <div class="alert alert-success" v-if="plan.plan_id == 2">
                                                    Current Plan
                                                </div>
                                                <a href="/billing/2" target="_parent" v-else class="btn btn-success btn-plan">Select</a>
                                            </div>
                                        </div>
<!--                                        <div class="col bill-col">-->
<!--                                            <div class="card">-->
<!--                                                <div class="card-header">-->
<!--                                                    <h2 class="title">Custom</h2>-->

<!--                                                    <p class="price">-->
<!--                                                        Starts from-->
<!--                                                        $129/Month-->
<!--                                                        depending on-->
<!--                                                        volumes for-->
<!--                                                        large-->
<!--                                                        merchants-->
<!--                                                    </p>-->
<!--                                                    &lt;!&ndash;                        <span class="description">Perfect for Online Store</span>&ndash;&gt;-->
<!--                                                    <div class="line" ></div>-->
<!--                                                    <a class="my_custom_link" href="#"><p>Contact for more-->
<!--                                                        info here.</p></a>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 w-xs-100 mb-30">
                                <div class="w-xs-100 mb-30">
                                    <h6 class="mb-0">Usage</h6>
                                    <p>Cybertonica is on constant watch, monitoring every action by every visitor to your website. Actions and behaviors leading up to an order inform Cybertonica about a user's deceptiveness or honesty, and ultimately the validity of each order. Because of this, it is a pay-as-you-go service based on the total number of hits analyzed per month.</p>
                                </div>
                                <div class="app-card w-xs-100 mb-30 plan-card">
                                    <h6 class="mb-0">Current Billing Cycle</h6>
                                    <p class="small-text">Start Date: <b>{{plan.start_dt}}</b></p>
                                    <p class="small-text">End Date: <b>{{plan.end_dt}}</b></p>
                                    <apex-chart style="width: 100%" height="300" type="line" :options=pageChart :series=dataPages></apex-chart>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import helper from '../../helper';
import Loader from "./Loader";
import ApexChart from 'vue-apexcharts';
import moment from "moment";

export default {
    components:{
        Loader,
        ApexChart,
    },
    data(){
        return{
            loading: true,
            plan: [],
            trial: [],
            pageView: [],
            dataPages: [{
                name : 'views',
                data: []
            }],
            pageChart: {
                yaxis: {
                    show: false,
                },
                grid: {
                    show: false,
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        gradientToColors: [ '#FDD835'],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 4,
                    colors: ["#FFA41B"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7,
                    }
                },
                colors: ['#60fabd'],
                stroke: {
                    width: 7,
                    curve: 'smooth'
                },
                dataLabels: {
                    enabled: false,
                },
                xaxis: {
                    type: 'days',
                },
            },
        }
    },
    methods:{
        async getData() {
            this.loading = true;
            let start = moment().subtract(21 - 1, 'd').format('YYYY-MM-DD');
            let base = this;
            let url = 'get-plan?t=b';
            let method = 'get';
            await axios({
                url: url,
                method: method,
            }).then(function (res) {
                base.plan = res.data.data.plan;
                base.trial = res.data.data.trial;
                base.pageView = res.data.data.pageView;
                base.fillPagesData(start, 21);
            }).catch(function (err) {
                console.log(err);
            }).finally(function (res) {
                base.loading = false;
            });
        },
        fillPagesData(start, days) {
            this.pageView.labels = this.getLabel(start,days);
            this.dataPages = [{
                name : 'visitor',
                data: this.pageView.data
            }];
            this.pageChart = {
                xaxis: {
                    type: 'days',
                    borderColor: '#963dff',
                    categories: this.pageView.labels,
                },
            };
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
    },
    created() {
        this.getData();
        helper.bannerHide();
    },
}
</script>
