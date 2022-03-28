<template>
    <div class="app-body">
        <loader :loading="loading"></loader>
        <div :style="[loading ? {opacity: 0.3} : {opacity: 1}]" class="app-row">
            <div class="app-card-body" style="overflow: hidden;">
                <div class="w-100 w-xs-100 setting-page-mr-class">
                    <div class="app-sub-card app-sub-card-padding mb-30">
                        <div class="app-card-header">
                            <h6 class="mb-0">Settings</h6>
                        </div>
                        <div style="overflow-x: auto;">
                            <div class="w-100 w-xs-100 mb-30 d-flex justify-content-between">
                                <div class="w-50 w-xs-100 mb-30">
                                    <h6 class="mb-0">Email Notifications</h6>
                                    <p>This setting lets you automatically send you email notification when order risk score is high than default risk score.</p>
                                </div>
                                <div class="app-card w-50 w-xs-100 mb-30">
                                    <h6 class="mb-0">Manage Email Notification:</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="card__control-timeframe dropleft btn-group-sm dropdown mr-10 mt-20">
                                            <label class="switch">
                                                <input type="checkbox" @change="change_email_setting" value="" :checked="parseInt(form.manage_email_notification) == 1">
                                                <span class="slider round"></span>
                                            </label><br><br>
                                        </div>
                                        <div>
                                            Send email notification automatically when order risk score is high then default risk score while creating or updating order.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 w-xs-100 mb-30 d-flex justify-content-between">
                                <div class="w-50 w-xs-100 mb-30">
                                    <h6 class="mb-0">Timezone for Reports</h6>
                                    <p>Select the timezone to use when displaying reports.</p>
                                </div>
                                <div class="app-card w-50 w-xs-100 mb-30">
                                    <div class="d-flex align-items-center">

                                        <h6 class="mb-0">Choose a timezone</h6>
                                        <div class="card__control-timeframe dropleft btn-group-sm dropdown ml-15">
                                            <button aria-haspopup="true" class="btn btn-secondary" aria-expanded="true" @click="drp_timezone = !drp_timezone" style="width: 100%;"><span style="vertical-align: text-bottom;">{{form.timezone}}</span>
                                                <svg class="mdi-icon" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <div tabindex="-1" role="menu" v-if="drp_timezone" aria-hidden="false" class="dropdown__menu dropdown-menu drp-menu drp-timezone-cls" style="will-change: transform;transform: translate3d(-112px, 0px, 0px);" x-placement="left-start">
                                                <button type="button" :value=item.timezone tabindex="0" role="menuitem" class="dropdown-item" v-for="(item, index) in timeZones" :key="index" @click="form.timezone = item.timezone, drp_timezone = !drp_timezone">{{item.timezone}}</button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
<!--                            <div class="w-100 w-xs-100 mb-30 d-flex justify-content-between">-->
<!--                                <div class="w-50 w-xs-100 mb-30">-->
<!--                                    <h6 class="mb-0">Valid vs. Suspicious Sessions Threshold</h6>-->
<!--                                    <p>Select the risk score that determines whether a session is valid or not. Sessions below this score will be marked as suspicious.</p>-->
<!--                                </div>-->
<!--                                <div class="app-card w-50 w-xs-100 mb-30">-->
<!--                                    <h6 class="mb-0">Choose a custom risk score. <span class="small-text"> The default is <span class="bold"></span></span>200. <span style="cursor: pointer;" v-tooltip.top-center="toolTip" @click="resetRisk('score')"><i class="fa fa-refresh" aria-hidden="true"></i></span></h6>-->
<!--                                    <div class="card__control-timeframe dropleft btn-group-sm dropdown setting-slider">-->
<!--                                        <vue-slider v-model="form.risk_score" :dot-options="risk_score.dotOptions" :min="risk_score.min" :max="risk_score.max" @change="updateValue('risk')">-->
<!--                                        </vue-slider>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="w-100 w-xs-100 mb-30 d-flex justify-content-between">
                                <div class="w-50 w-xs-100 mb-30">
                                    <h6 class="mb-0">Assign Cybertonica Risk for Orders.</h6>
                                    <p>Every time a user visits your store, each action they take is scored by the risk engine to determine their risk for fraud.</p>

                                       <p> Negative attributes (such as a user trying to hide their location or identity) will push their score down. Positive attributes (such as natural phone movements) will push their score up.</p>

                                    <p>Cybertonica Risk is determined by the initial scoring of the risk engine prior to any Order Rules being checked.</p>

                                    <p>Select the risk score that determines whether an order is High, Medium, or Low Risk. Orders within this score range will be assigned a Cybertonica Risk value accordingly on the individual order's review screen.</p>
                                </div>
                                <div class="app-card w-50 w-xs-100 mb-30">
                                    <h6 class="mb-0">Choose custom ranges for the risk Score. </h6>
                                    <p class="small-text">The default for High Risk is <b> &gt600</b>, Medium Risk is <b>201-600</b>, and Low Risk is <b><200</b>. <span style="cursor: pointer;" v-tooltip.top-center="toolTip" @click="resetRisk('range')"><i class="fa fa-refresh" aria-hidden="true"></i></span></p>
                                    <div class="card__control-timeframe dropleft btn-group-sm dropdown setting-slider__range">
                                        <vue-slider v-model="form.risk_score_range" :dot-options="risk_score.dotOptionsRange" :min="risk_score.min" :max="risk_score.max" @change="updateValue('range')">
                                        </vue-slider>
                                    </div>
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
    import {ContextualSaveBar} from "@shopify/app-bridge/actions";
    import VueSlider from 'vue-slider-component'
    import 'vue-slider-component/theme/material.css';
    import Loader from "./Loader";

    export default {
        components:{
            VueSlider,
            Loader,
        },
        data(){
            return{
                toolTip: 'Reset default',
                loading: true,
                drp_timezone: false,
                timeZones: [],
                watch: false,
                temp_prisine: '',
                form: {
                    timezone: '',
                    risk_score: [0,200],
                    risk_score_range: [200,600],
                    manage_email_notification: 0,
                },
                risk_score:{
                    min:0,
                    max: 1000,
                    marks1: {
                        '0': '😭',
                        '200': '😊',
                        '1000': '😆'
                    },
                    dotOptions: [
                        {
                            tooltip: 'always'
                        },
                    ],
                    dotOptionsRange: [
                        {
                            tooltip: 'never'
                        },
                        {
                            tooltip: 'never'
                        },
                    ],
                },
            }
        },
        methods:{
            updateValue(type){
                if( type == 'risk' ){
                    if( this.form.risk_score <= 100 ){
                        $('.vue-slider-min').css('display', 'none');
                        $('.vue-slider-max').css('display', 'block');
                    }else if( this.form.risk_score >= 900 ){
                        $('.vue-slider-max').css('display', 'none');
                        $('.vue-slider-min').css('display', 'block');
                    }else{
                        $('.vue-slider-max').css('display', 'block');
                        $('.vue-slider-min').css('display', 'block');
                    }
                }else{
                    console.log('update value');
                    console.log(this.form.risk_score_range);
                    if( this.form.risk_score_range[0] == 0 && this.form.risk_score_range[1] == 0 ) {
                        this.form.risk_score_range[0] = 1;
                        this.form.risk_score_range[1] = 4;
                        $('.risk-dynamic-low').html('0-1');
                        $('.risk-dynamic-medium').html('2-3');
                        $('.risk-dynamic-high').html('4-1000');
                    }else if( this.form.risk_score_range[0] == 1000 && this.form.risk_score_range[1] == 1000 ){
                        this.form.risk_score_range[0] = 966;
                        this.form.risk_score_range[1] = 999;
                        $('.risk-dynamic-low').html('0-966');
                        $('.risk-dynamic-medium').html('977-998');
                        $('.risk-dynamic-high').html('999-1000');
                    }else{
                        this.form.risk_score_range[0]  = ( this.form.risk_score_range[0] == 0 ) ? 1 : this.form.risk_score_range[0];
                        this.form.risk_score_range[1] =  ( this.form.risk_score_range[1] == 1000 ) ? 999 : this.form.risk_score_range[1];

                        $('.risk-dynamic-low').html('0-'+ this.form.risk_score_range[0]);
                        $('.risk-dynamic-medium').html((this.form.risk_score_range[0] + 1) + '-' + ( this.form.risk_score_range[1] - 1 ));
                        $('.risk-dynamic-high').html((this.form.risk_score_range[1]) + '-1000');
                    }
                }
            },
            change_email_setting(){
                this.form.manage_email_notification = ( this.form.manage_email_notification === 0 ) ? 1 : 0;
            },
            createContextualSaveBar() {
                let base = this;
                let options = {
                    saveAction: {
                        disabled: false,
                        loading: false,
                    },
                    discardAction: {
                        disabled: false,
                        loading: false,
                        discardConfirmationModal: true,
                    },
                };
                var contextualSaveBar = helper.contextualSaveBar(options);

                contextualSaveBar.dispatch(ContextualSaveBar.Action.SHOW);

                contextualSaveBar.subscribe(ContextualSaveBar.Action.DISCARD, function () {
                    base.getSettings();
                    contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);
                });
                contextualSaveBar.subscribe(ContextualSaveBar.Action.SAVE, function () {
                    contextualSaveBar.set({saveAction: {loading: true}, discardAction: {disabled: true}});
                    base.sendForm();

                });
            },
            async getSettings() {
                this.loading = true;
                let base = this;
                helper.startLoading();
                base.errors = [];
                await axios.get('settings')
                    .then(res => {
                        var data = res.data.data;
                        base.timeZones = data.timezone;
                        base.form = data.setting;
                        base.plan = data.plan;

                        base.watch = true;
                        base.temp_prisine = JSON.stringify(base.form);
                        this.addLableSlider();
                    })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        helper.stopLoading();
                        base.loading = false;
                    });
            },
            async sendForm() {
                let base = this;
                let url = 'settings';
                let method = 'post';

                await axios({
                    url: url,
                    data: {
                        'data': base.form,
                    },
                    method: method,
                }).then(function (res) {
                    var contextualSaveBar = helper.contextualSaveBar();
                    contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);

                    let msg = res.data.data;
                    helper.successToast(msg);

                    base.getSettings();
                })
                    .catch(function (err) {
                        console.log(err);
                        base.createContextualSaveBar();
                        base.errors = err.response.data;
                    });
            },
            addLableSlider(){
                // add min label in risk
                var target = $('.setting-slider');
                target.prepend('<div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top vue-slider-max vue-slider-dot-tooltip-show"><div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top"><span class="vue-slider-dot-tooltip-text">1000</span></div></div>');
                target.prepend('<div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top vue-slider-min vue-slider-dot-tooltip-show"><div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top"><span class="vue-slider-dot-tooltip-text">0</span></div></div>');

                // add level in risk level
                var target1 = $('.setting-slider__range');
                target1.prepend('<div class="vue-slider-dot-tooltip risk low vue-slider-dot-tooltip-top vue-slider-dot-tooltip-show"><div class="vue-slider-dot-tooltip-content"><div class="vue-slider-dot-tooltip-arrow"></div><div class="vue-slider-dot-tooltip-inner" role="tooltip"><span class="risk-level">Low Risk</span> <span class="risk-dynamic risk-dynamic-low">0-'+ this.form.risk_score_range[0] +'</span></div></div></div>');
                target1.prepend('<div class="vue-slider-dot-tooltip risk medium vue-slider-dot-tooltip-top vue-slider-dot-tooltip-show"><div class="vue-slider-dot-tooltip-content"><div class="vue-slider-dot-tooltip-arrow"></div><div class="vue-slider-dot-tooltip-inner" role="tooltip"><span class="risk-level">Medium Risk</span> <span class="risk-dynamic risk-dynamic-medium">'+ (this.form.risk_score_range[0] + 1) + '-' + this.form.risk_score_range[1] + '</span></div></div></div>');
                target1.prepend('<div class="vue-slider-dot-tooltip risk high vue-slider-dot-tooltip-top vue-slider-dot-tooltip-show"><div class="vue-slider-dot-tooltip-content"><div class="vue-slider-dot-tooltip-arrow"></div><div class="vue-slider-dot-tooltip-inner" role="tooltip"><span class="risk-level">High Risk</span> <span class="risk-dynamic risk-dynamic-high">' + (this.form.risk_score_range[1] + 1 ) + '-1000</span></div></div></div>');

            },
            resetRisk(type){
                if( type == 'score' ){
                    this.form.risk_score = 200;
                }else{
                    this.form.risk_score_range = [200, 600];
                    this.updateValue('range');
                }
            }
        },
        created() {
            this.getSettings();
            helper.bannerShow();
        },
        watch: {
            form: {
                immediate: true,
                deep: true,
                handler: function () {
                    if( this.watch ) {
                        if (_.isEqual(this.temp_prisine, JSON.stringify(this.form))) {
                            let contextualSaveBar = helper.contextualSaveBar();
                            contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);
                        } else {
                            this.createContextualSaveBar();
                        }
                    }
                }
            }
        }
    }
</script>
<style lang="scss">
.tooltip {
    display: block !important;
    z-index: 10000;

.tooltip-inner {
    background: black;
    color: white;
    border-radius: 16px;
    padding: 5px 10px 4px;
}

.tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
    border-color: black;
    z-index: 1;
}

&[x-placement^="top"] {
     margin-bottom: 5px;

.tooltip-arrow {
    border-width: 5px 5px 0 5px;
    border-left-color: transparent !important;
    border-right-color: transparent !important;
    border-bottom-color: transparent !important;
    bottom: -5px;
    left: calc(50% - 5px);
    margin-top: 0;
    margin-bottom: 0;
}
}

&[x-placement^="bottom"] {
     margin-top: 5px;

.tooltip-arrow {
    border-width: 0 5px 5px 5px;
    border-left-color: transparent !important;
    border-right-color: transparent !important;
    border-top-color: transparent !important;
    top: -5px;
    left: calc(50% - 5px);
    margin-top: 0;
    margin-bottom: 0;
}
}

&[x-placement^="right"] {
     margin-left: 5px;

.tooltip-arrow {
    border-width: 5px 5px 5px 0;
    border-left-color: transparent !important;
    border-top-color: transparent !important;
    border-bottom-color: transparent !important;
    left: -5px;
    top: calc(50% - 5px);
    margin-left: 0;
    margin-right: 0;
}
}

&[x-placement^="left"] {
     margin-right: 5px;

.tooltip-arrow {
    border-width: 5px 0 5px 5px;
    border-top-color: transparent !important;
    border-right-color: transparent !important;
    border-bottom-color: transparent !important;
    right: -5px;
    top: calc(50% - 5px);
    margin-left: 0;
    margin-right: 0;
}
}

&[aria-hidden='true'] {
     visibility: hidden;
     opacity: 0;
     transition: opacity .15s, visibility .15s;
 }

&[aria-hidden='false'] {
     visibility: visible;
     opacity: 1;
     transition: opacity .15s;
 }
}
</style>
