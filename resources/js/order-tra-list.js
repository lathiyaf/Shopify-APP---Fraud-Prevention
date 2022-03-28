// function init() {
//     let url = window.location.href;
//     if (url.includes("/")) {
//         $params = getParams('order-tra-list');
//         let shopifyDomain = $params['shop'];
//     }
// }

jQuery.post('/cart/update.js',
    {
        attributes: {
            'cybertonica tid': localStorage.getItem("cybertonica_tid")
        }
    }
);
// $(document).on('click', ".checkout_crawlapps, input[name='checkout'], [name='checkout_crawlapps'], input[name='checkout_crawlapps'] , input[value='CHECKOUT'], input[value='Checkout'], button[name='checkout'], [href$='checkout'], [href$='/checkout'], [href='/checkout'], [href='checkout'], button[value='Checkout'], input[name='goto_pp'], button[name='goto_pp'], input[name='goto_gc'], button[name='goto_gc'], #dropdown-cart .actions .btn, .checkout-button, .ajax-cart__button.button--add-to-cart", function (e) {
//     e.preventDefault();
//     let forms = document.getElementsByTagName('form');
//     let ac = 'https://' + window.location.hostname;
//     for (var i = 0; i < forms.length; i++) {
//         let str = forms[i].action.replace(ac, '');
//         if (str.includes("cart")) {
//             forms[i].setAttribute("id", "cartform");
//         }
//     }
//
//     let is_hastID = document.getElementById("cybertonica_tid");
//
//     if (!is_hastID) {
//         var div = document.createElement("div");
//         div.className = 'cart-attribute__field';
//
//
//         // Create a tid input
//         var hiddentid = document.createElement("input");
//         hiddentid.setAttribute("type", "hidden");
//         hiddentid.setAttribute("id", "cybertonica_tid");
//         hiddentid.setAttribute("name", "attributes[cybertonica tid]"); // You may want to change this
//         hiddentid.setAttribute("value", localStorage.getItem("cybertonica_tid")); // You may want to change this
//         div.appendChild(hiddentid);
//         //Append the div to the container div
//         document.getElementById("cartform").appendChild(div);
//     }
//     console.log(document.getElementById("cybertonica_tid").value);
//     // $('#cartform').submit();
//     window.location.reload = document.getElementById("cartform").action;
// });

// function addNoteAttribute(shopifyDomain) {
//     let forms = document.getElementsByTagName('form');
//
//     let ac = 'https://' + window.location.hostname + '/cart';
//     for (var i = 0; i < forms.length; i++) {
//         if(forms[i].action === ac){
//             forms[i].setAttribute("id", "cartform");
//         }
//     }
//     var div = document.createElement("div");
//
//     // Create a tid input
//     var hiddentid = document.createElement("input");
//     hiddentid.setAttribute("type", "hidden");
//     hiddentid.setAttribute("id", "cybertonica_tid");
//     hiddentid.setAttribute("name", "attributes[cybertonica tid]"); // You may want to change this
//     hiddentid.setAttribute("value", localStorage.getItem("cybertonica_tid")); // You may want to change this
//     div.appendChild(hiddentid);
//
//     //Append the div to the container div
//     document.getElementById("cartform").appendChild(div);
// }

function getParams(script_name) {
    // Find all script tags
    var scripts = document.getElementsByTagName("script");
    // Look through them trying to find ourselves
    for (var i = 0; i < scripts.length; i++) {
        if (scripts[i].src.indexOf("/" + script_name) > -1) {
            // Get an array of key=value strings of params
            var pa = scripts[i].src.split("?").pop().split("&");
            // Split each key=value into array, the construct js object
            var p = {};
            for (var j = 0; j < pa.length; j++) {
                var kv = pa[j].split("=");
                p[kv[0]] = kv[1];
            }
            return p;
        }
    }

    // No scripts match

    return {};
}

// window.Vue = require('vue');
// const app = new Vue({
//     template: '<div></div>',
//     data: {
//         shopifyDomain: '',
//     },
//     methods: {
//         init() {
//             $params = this.getParams('order-tra-list');
//             this.shopifyDomain = $params['shop'];
//
//             let target = document.getElementsByTagName('head')[0];
//             var script = document.createElement('script');
//             script.type = "text/javascript";
//             script.src = "https://pxl.cybertonica.com/js/v2/beacon.min.js";
//             target.appendChild(script);
//
//             let script_data = `console.log('markk1111111');\n` +
//                 `window.onload = function() {\n` +
//                 `   console.log('markk22222'); \n` +
//                 `   var tid = undefined;\n` +
//                 `   if (typeof AFCYBERTONICA !== "undefined") {\n` +
//                 `   console.log('mark333333'); \n` +
//                 `       tid = AFCYBERTONICA.init('` + this.shopifyDomain + `', undefined, 'https://pxl.cybertonica.com');\n` +
//                 `   }`
//                 `};`
//
//             // let script_data = `console.log('markk1111111');\n` +
//             //     `window.addEventListener('load', function() {\n` +
//             //     `   console.log('markk22222'); \n` +
//             //     `   var tid = undefined;\n` +
//             //     `   if (typeof AFCYBERTONICA !== "undefined") {\n` +
//             //     `   console.log('mark333333'); \n` +
//             //     `       tid = AFCYBERTONICA.init('` + this.shopifyDomain + `', undefined, 'https://pxl.cybertonica.com');\n` +
//             //     `   }\n` +
//             //     `});`
//
//             var newScript = document.createElement("script");
//             var inlineScript = document.createTextNode(script_data);
//             newScript.appendChild(inlineScript);
//             target.appendChild(newScript);
//         },
//         getParams(script_name) {
//             // Find all script tags
//             var scripts = document.getElementsByTagName("script");
//             // Look through them trying to find ourselves
//             for (var i = 0; i < scripts.length; i++) {
//                 if (scripts[i].src.indexOf("/" + script_name) > -1) {
//                     // Get an array of key=value strings of params
//                     var pa = scripts[i].src.split("?").pop().split("&");
//                     // Split each key=value into array, the construct js object
//                     var p = {};
//                     for (var j = 0; j < pa.length; j++) {
//                         var kv = pa[j].split("=");
//                         p[kv[0]] = kv[1];
//                     }
//                     return p;
//                 }
//             }
//
//             // No scripts match
//
//             return {};
//         }
//     },
//     created() {
//         this.init();
//     },
// });

// Window.order_tracking = {
//     init: function () {
//         app.$mount();
//     },
// };
// window.onload = Window.order_tracking.init();

// window.onload = init();
