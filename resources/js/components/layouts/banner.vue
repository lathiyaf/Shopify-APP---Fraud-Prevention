<template>
    <div v-if="is_show" class="app-body" id="component-Banner">
        <div class="component-Banner component-Banner--statusWarning component-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner18Heading" aria-describedby="Banner18Content">
        <div class="component-Banner__Ribbon">
            <span class="component-Icon component-Icon--colorYellowDark component-Icon--isColored component-Icon--hasBackdrop"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
          <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
          <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m0-13a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V6a1 1 0 0 0-1-1m0 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2"></path>
        </svg></span></div>
        <div class="component-Banner__ContentWrapper">
            <div class="component-Banner__Heading" id="Banner18Heading">
                <p class="component-Heading">{{trialDays}} days remain.</p>
            </div>
            <div class="component-Banner__Content" id="Banner18Content">
                <ul class="component-List">
                    <li class="">Your 30 day free trial for Cybertonica ends in {{trialDays}} days. <router-link :to="{name :'plan'}" tag="span" class="component-link">Upgrade Now</router-link> .You'll still get those 30 days for free.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </div>
</template>
<script>
import helper from "../../helper";

export default {
    name: 'Banner',
    data() {
        return {
            is_show: false,
            trialDays: false,
        }
    },
    created() {
            this.getTrialEnds();
    },
    methods: {
        async getTrialEnds() {
            let base = this;
            helper.startLoading();
            base.errors = [];
            await axios.get('/get-trial')
                .then(res => {
                    var data = res.data.data;
                    base.is_show = data.is_show;
                    base.trialDays = data.trial_days;

                    base.watch = true;
                    base.temp_prisine = JSON.stringify(base.form);
                })
                .catch(err => {
                    console.log(err);
                })
                .finally(res => {
                    helper.stopLoading();
                });
        },
    }
};
</script>
