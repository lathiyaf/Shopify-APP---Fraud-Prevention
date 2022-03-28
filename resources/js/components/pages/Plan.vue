<template>
    <div class="">
        <loader :loading="loading"></loader>
        <div :style="[loading ? {opacity: 0.3} : {opacity: 1}]" class="choose-plan-row" id="plan-price"  style="display: flex;">
            <div class="col bill-col">
                <div class="card">
                    <div class="card-header">
                        <h2 class="title">Standard</h2>

                        <p class="price">
                            29$/Month
                        </p>
                        <!--                        <span class="description">Perfect for Online Store</span>-->
                        <div class="line"></div>
                        <p>Start your 30-day free trial.</p>
                    </div>
                    <ul class="features">
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
                    </ul>
                    <p>*score = total number of page
                        views + orders</p>
                    <div v-if="plan_id == 1" class="alert alert-success">
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
                            79$/Month
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
                    <div class="alert alert-success" v-if="plan_id == 2">
                        Current Plan
                    </div>
                    <a href="/billing/2" target="_parent" v-else class="btn btn-success btn-plan">Select</a>
                </div>
            </div>
<!--            <div class="col bill-col">-->
<!--                <div class="card">-->
<!--                    <div class="card-header">-->
<!--                        <h2 class="title">Custom</h2>-->

<!--                        <p class="price">-->
<!--                            Starts from-->
<!--                            $129/Month-->
<!--                            depending on-->
<!--                            volumes for-->
<!--                            large-->
<!--                            merchants-->
<!--                        </p>-->
<!--                        &lt;!&ndash;                        <span class="description">Perfect for Online Store</span>&ndash;&gt;-->
<!--                        <div class="line" ></div>-->
<!--                        <a class="my_custom_link" href="#"><p>Contact for more-->
<!--                            info here.</p></a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</template>

<script>
import Loader from "./Loader";
import helper from "../../helper";
    export default {
        components:{
            Loader
        },
        data(){
          return{
              loading:true,
              plan_id: '',
          }
        },
        methods:{
            async getData() {
                this.loading = true;
                let base = this;
                let url = 'get-plan?t=p';
                let method = 'get';
                await axios({
                    url: url,
                    method: method,
                }).then(function (res) {
                    base.plan_id = res.data.data.plan.plan_id;
                }).catch(function (err) {
                    console.log(err);
                }).finally(function (res) {
                    base.loading = false;
                });
            },
        },
        created() {
            this.getData();
            helper.bannerHide();
        }
    }
</script>
