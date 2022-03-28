<template>
    <div class="header">
        <div class="d-flex align-items-center main-menu">
        <div class="menu-icon">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div>
        <ul class="list-unstyled d-flex align-items-center">
            <li><router-link :class="activeClass('threat-intelligence-listing')" :to="{name :'threat-intelligence-listing'}" tag="a" class="">
                <svg class="mr-5" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">

                <path fill="#111111" d="M12.6,0.4C12.4,0.2,12.2,0,11.8,0H8.5v4h6.2L12.6,0.4z"/>
                    <path fill="#111111" d="M7.5,0H4.2C3.8,0,3.6,0.2,3.4,0.5L1.4,4h6.2L7.5,0L7.5,0z"/>
                    <path fill="#111111" d="M1,4.9v10C1,15.5,1.5,16,2.1,16h11.7c0.6,0,1.1-0.5,1.1-1.1v-10H1z M10.1,9.1l-2.4,2.4
                  c-0.1,0.1-0.2,0.1-0.3,0.1s-0.2,0-0.3-0.1l-1.1-1.1c-0.2-0.2-0.2-0.5,0-0.7c0.2-0.2,0.5-0.2,0.7,0l0.8,0.8l2.1-2.1
                  c0.2-0.2,0.5-0.2,0.7,0C10.3,8.6,10.3,8.9,10.1,9.1z"/>
              </svg>
                Threat Intelligence</router-link></li>
            <li class="dropdown" style="cursor: pointer;">
                <a class="btn dropdown-toggle" @click="is_show_setting = !is_show_setting"  type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg class="mr-5" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
         <g>
             <defs>
                 <rect id="SVGID_1_" width="16" height="16"/>
             </defs>
             <clipPath id="SVGID_2_">
                 <use xlink:href="#SVGID_1_" style="overflow:visible;"/>
             </clipPath>
             <g class="settin-st0">
                 <path fill="#111111" d="M15.6,6.2l-1.2-0.3c-0.1-0.3-0.2-0.6-0.4-1l0.7-1.1c0.1-0.2,0.1-0.4-0.1-0.6l-1.9-1.9
                     c-0.2-0.2-0.4-0.2-0.6-0.1L11,2c-0.3-0.2-0.6-0.3-1-0.4L9.8,0.4C9.8,0.2,9.6,0,9.4,0H6.6C6.4,0,6.2,0.2,6.2,0.4L5.9,1.6
                     C5.6,1.7,5.3,1.8,5,2L3.9,1.3C3.7,1.2,3.5,1.2,3.3,1.4L1.4,3.3C1.2,3.5,1.2,3.7,1.3,3.9L2,5c-0.2,0.3-0.3,0.6-0.4,1L0.4,6.2
                     C0.2,6.2,0,6.4,0,6.6v2.7c0,0.2,0.2,0.4,0.4,0.5l1.2,0.3c0.1,0.3,0.2,0.6,0.4,1l-0.7,1.1c-0.1,0.2-0.1,0.4,0.1,0.6l1.9,1.9
                     c0.2,0.2,0.4,0.2,0.6,0.1L5,14c0.3,0.2,0.6,0.3,1,0.4l0.3,1.2c0,0.2,0.2,0.4,0.5,0.4h2.7c0.2,0,0.4-0.2,0.5-0.4l0.3-1.2
                     c0.3-0.1,0.6-0.2,1-0.4l1.1,0.7c0.2,0.1,0.4,0.1,0.6-0.1l1.9-1.9c0.2-0.2,0.2-0.4,0.1-0.6L14,11c0.2-0.3,0.3-0.6,0.4-1l1.2-0.3
                     c0.2,0,0.4-0.2,0.4-0.5V6.6C16,6.4,15.8,6.2,15.6,6.2z M10.9,8c0,1.6-1.3,2.9-2.9,2.9S5.1,9.6,5.1,8S6.4,5.1,8,5.1
                     S10.9,6.4,10.9,8z"/>
             </g>
         </g>
         </svg>Settings/Support<i class="fa fa-caret-down ml-15"></i>
                </a>
                <div class="dropdown-menu setting-dp" v-if="is_show_setting" id="setting-dp" aria-labelledby="dropdownMenuButton" @click="is_show_setting=false">
                    <router-link :class="activeClass('settings')" :to="{name :'settings'}" tag="a" class="dropdown-item">
                        Settings</router-link>
                    <router-link :class="activeClass('billing')" :to="{name :'billing'}" tag="a" class="dropdown-item">
                        Billing</router-link>
                </div>
            </li>
            <li><router-link :class="activeClass('order-listing')" :to="{name :'order-listing'}" tag="a" class="">
                <svg class="mr-5" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">

                <path fill="#111111" d="M12.6,0.4C12.4,0.2,12.2,0,11.8,0H8.5v4h6.2L12.6,0.4z"/>
                    <path fill="#111111" d="M7.5,0H4.2C3.8,0,3.6,0.2,3.4,0.5L1.4,4h6.2L7.5,0L7.5,0z"/>
                    <path fill="#111111" d="M1,4.9v10C1,15.5,1.5,16,2.1,16h11.7c0.6,0,1.1-0.5,1.1-1.1v-10H1z M10.1,9.1l-2.4,2.4
                  c-0.1,0.1-0.2,0.1-0.3,0.1s-0.2,0-0.3-0.1l-1.1-1.1c-0.2-0.2-0.2-0.5,0-0.7c0.2-0.2,0.5-0.2,0.7,0l0.8,0.8l2.1-2.1
                  c0.2-0.2,0.5-0.2,0.7,0C10.3,8.6,10.3,8.9,10.1,9.1z"/>
              </svg>
                Order Protection</router-link></li>
            <li>
                <router-link :class="activeClass('/dashboard')" :to="{name :'dashboard'}" tag="a" class="">
                    <svg class="mr-5" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
              <path d="M10.6,2.8C5,1,0,5.1,0,10.4c0,0.7,0.1,2.1,0.3,2.9c0,0.2,0.2,0.3,0.3,0.3h14.6c0.2,0,0.3-0.2,0.4-0.3
                  c0.2-0.8,0.3-2.2,0.3-2.9C16,7,13.8,3.8,10.6,2.8z M8,4c0.5,0,0.8,0.3,0.8,0.8S8.5,5.6,8,5.6S7.2,5.3,7.2,4.8S7.5,4,8,4z M2.4,11.2
                  c-0.5,0-0.8-0.3-0.8-0.8c0-0.5,0.3-0.8,0.8-0.8s0.8,0.3,0.8,0.8C3.2,10.9,2.9,11.2,2.4,11.2z M4.6,7C4.3,7.4,3.8,7.4,3.5,7
                  C3.2,6.7,3.2,6.2,3.4,5.8c0.3-0.3,0.8-0.3,1.1,0S4.9,6.7,4.6,7z M8,12c-0.9,0-1.7-0.8-1.4-1.6C6.8,9.6,8,7.2,8,7.2s1.2,2.4,1.4,3.2
                  C9.7,11.2,8.9,12,8,12z M12.6,7c-0.3,0.3-0.8,0.3-1.1,0c-0.3-0.3-0.3-0.8,0-1.1c0.3-0.3,0.8-0.3,1.1,0C12.8,6.2,12.8,6.7,12.6,7z
                  M13.6,11.2c-0.5,0-0.8-0.3-0.8-0.8c0-0.5,0.3-0.8,0.8-0.8c0.5,0,0.8,0.3,0.8,0.8C14.4,10.9,14.1,11.2,13.6,11.2z"/>
              </svg>
                    Dashboard</router-link>
            </li>
            <a id="" href="#" class="btn btn-success lv-sprt my_custom_link">
                <svg width="16" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
          <path fill="#fff"
                d="M4.2,6.6C3.5,6.6,2.8,7.2,2.8,8v3.8c0,0.8,0.6,1.4,1.4,1.4s1.4-0.6,1.4-1.4V8C5.7,7.2,5,6.6,4.2,6.6z"/>
                    <path fill="#fff"
                          d="M1.9,8c0-0.1,0-0.3,0-0.4C1.4,7.8,1,8.3,1,8.9v1.9c0,0.6,0.4,1.1,1,1.3c0-0.1,0-0.3,0-0.4V8z"/>
                    <path fill="#fff" d="M11.8,6.6c-0.8,0-1.4,0.6-1.4,1.4v3.8c0,0.8,0.6,1.4,1.4,1.4c0.1,0,0.3,0,0.4-0.1c-0.2,0.6-0.7,1-1.3,1H9.3
              c-0.2-0.5-0.7-0.9-1.3-0.9c-0.8,0-1.4,0.6-1.4,1.4C6.6,15.4,7.2,16,8,16c0.6,0,1.1-0.4,1.3-0.9h1.5c1.3,0,2.3-1.1,2.3-2.3v-0.9V8
              C13.2,7.2,12.5,6.6,11.8,6.6z"/>
                    <path fill="#fff"
                          d="M14.1,7.6c0,0.1,0,0.3,0,0.4v3.8c0,0.1,0,0.3,0,0.4c0.6-0.2,1-0.7,1-1.3V8.9C15,8.3,14.6,7.8,14.1,7.6z"/>
                    <path fill="#fff" d="M8,0C4.1,0,1,3.2,1,7v0c0.3-0.2,0.6-0.4,1-0.4C2.1,3.4,4.8,0.9,8,0.9c3.2,0,5.9,2.5,6.1,5.7
              c0.4,0.1,0.7,0.2,1,0.4v0C15,3.2,11.9,0,8,0z"/>
          </svg>
                Live Support
            </a>

        </ul>
    </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                is_show_setting: false
            }
        },
        created() {
            document.onclick = this.documentClick;
        },
        methods: {
            activeClass: function (name) {
                if ( name === this.$route.name ) {
                    return 'active';
                }
            },
            documentClick(e){
                let base = this;
                let filter_selector = $('.setting-dp');
                if (!filter_selector.is(e.target) && filter_selector.has(e.target).length === 0) {
                    base.is_show_setting=false;
                }
            }
        }
    };
</script>
