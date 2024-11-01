/*!
 * 
 * wpMailPro
 * 
 * @author 
 * @version 0.1.0
 * @link UNLICENSED
 * @license UNLICENSED
 * 
 * Copyright (c) 2021 
 * 
 * This software is released under the UNLICENSED License
 * https://opensource.org/licenses/UNLICENSED
 * 
 * Compiled with the help of https://wpack.io
 * A zero setup Webpack Bundler Script for WordPress
 */
(window.wpackiowpMailProadmin_jsJsonp=window.wpackiowpMailProadmin_jsJsonp||[]).push([[0],{203:function(e,t,a){a(204),e.exports=a(370)},370:function(e,t,a){"use strict";a.r(t);var n=a(10),r=a.n(n),c=a(20),l=a(0),s=a.n(l),i=a(42),o=a(23),u=a(5),m=a(25),d=a(35);function p(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function g(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?p(Object(a),!0).forEach((function(t){Object(d.a)(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):p(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}var f={fields:g(g({},window.WPMailPro.data.fields),{},{metricsFilter:"thisMonth",logsPage:1,logsFilters:{status:"all",recipient:""}}),isLoading:!1,notice:{message:null,success:!1,error:!1,warning:!1},updateField:function(){},setLoading:function(){},displayNotice:function(){}},E=Object(l.createContext)(f),v=function(e){var t=e.children,a=Object(l.useState)(f.fields),n=Object(m.a)(a,2),r=n[0],c=n[1],i=Object(l.useState)(f.isLoading),o=Object(m.a)(i,2),u=o[0],p=o[1],v=Object(l.useState)(f.notice),_=Object(m.a)(v,2),b=_[0],h=_[1],w=function(){h((function(e){return f.notice}))};return Object(l.useEffect)((function(){var e=setTimeout(w,7e3);return function(){clearTimeout(e)}}),[b.message]),s.a.createElement(E.Provider,{value:{fields:r,isLoading:u,notice:b,updateField:function(e,t){c((function(a){return g(g({},a),{},Object(d.a)({},e,t))}))},setLoading:p,displayNotice:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"success";h((function(a){return g(g({},f.notice),{},Object(d.a)({message:e},t,!0))}))},hideNotice:w}},t)},_=function(){return Object(l.useContext)(E)},b=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;return t&&(e="".concat(t,".").concat(e)),window.WPMailPro.strings[e]?window.WPMailPro.strings[e]:""},h=function(){var e=_(),t=e.notice,a=e.hideNotice;if(!t.message)return null;var n="success";return t.error&&(n="error"),t.warning&&(n="warning"),s.a.createElement(u.j,{className:"feedback-notice",status:n,onDismiss:a},t.message)},w=Object(o.hot)((function(){return s.a.createElement("header",{className:"settings-page-header"},s.a.createElement("h1",{className:"wp-heading-inline"},b("Global.PageTitle")),s.a.createElement(h,null))})),N=Object(o.hot)((function(){return _().isLoading?s.a.createElement("div",{className:"loader"},s.a.createElement(u.k,{size:"large",label:"Loading..."})):null})),y=Object(o.hot)((function(e){var t=e.title,a=e.children,n=e.className,r=void 0===n?"":n;return s.a.createElement(u.h,{className:"tab-section ".concat(r)},t&&s.a.createElement(u.h.Header,null,t),s.a.createElement(u.h.Section,null,a))})),O=a(180),j=a.n(O);function x(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function k(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?x(Object(a),!0).forEach((function(t){Object(d.a)(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):x(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}var F=function(){var e=Object(c.a)(r.a.mark((function e(t,a){return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",j.a.post("".concat(window.WPMailPro.data.api.rest_url,"wpmailpro/v1/admin/").concat(t),a,{headers:{"Content-Type":"application/json","X-WP-Nonce":window.WPMailPro.data.api.nonce},timeout:15e3}).then((function(e){return Promise.resolve(e.data)}),(function(e){return Promise.reject(e)})));case 1:case"end":return e.stop()}}),e)})));return function(t,a){return e.apply(this,arguments)}}(),S=function(){var e=Object(c.a)(r.a.mark((function e(){var t,a,n,c=arguments;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t=c.length>0&&void 0!==c[0]&&c[0],a=c.length>1?c[1]:void 0,e.prev=2,e.next=5,F("metrics-get",{force:t,range:a});case 5:return n=e.sent,e.abrupt("return",k({success:!0},n));case 9:return e.prev=9,e.t0=e.catch(2),e.abrupt("return",{error:e.t0.message});case 12:case"end":return e.stop()}}),e,null,[[2,9]])})));return function(){return e.apply(this,arguments)}}(),P=function(){var e=Object(c.a)(r.a.mark((function e(){var t,a,n,c=arguments;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t=c.length>0&&void 0!==c[0]?c[0]:{},a=c.length>1&&void 0!==c[1]?c[1]:1,e.prev=2,e.next=5,F("logs-get",{filters:t,page:a});case 5:return n=e.sent,e.abrupt("return",k({success:!0},n));case 9:return e.prev=9,e.t0=e.catch(2),e.abrupt("return",{error:e.t0.message});case 12:case"end":return e.stop()}}),e,null,[[2,9]])})));return function(){return e.apply(this,arguments)}}(),C=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("fields-save",{fields:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),D=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("send-test-email",{email:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),L=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("domain-create",{domain:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),T=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("domain-verify",{domain:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),M=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("license-activate",{license:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),R=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("license-deactivate",{license:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),z=function(){var e=Object(c.a)(r.a.mark((function e(t){var a;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,F("email-content-get",{id:t});case 3:return a=e.sent,e.abrupt("return",k({success:!0},a));case 7:return e.prev=7,e.t0=e.catch(0),e.abrupt("return",{error:e.t0.message});case 10:case"end":return e.stop()}}),e,null,[[0,7]])})));return function(t){return e.apply(this,arguments)}}(),H=a(11),B=a(379),I=a(377),A=a(195),W=a(191),V=["#1273e6","#CACACA"],q=function(e,t){return 0==e?0:parseFloat(100*e/t).toFixed(2)},J=function(e){var t=e.number,a=e.total,n=e.title,r=function(e,t){return[{value:parseInt(e)},{value:parseInt(t)-parseInt(e)}]}(t,a);return s.a.createElement("div",{className:"pie-and-title"},s.a.createElement(B.a,{width:"99%",height:200},s.a.createElement(I.a,null,s.a.createElement(A.a,{data:r,innerRadius:70,outerRadius:90,startAngle:90,endAngle:450,paddingAngle:0,dataKey:"value"},r.map((function(e,t){return s.a.createElement(W.a,{key:"cell-".concat(t),fill:V[t%V.length]})}))))),s.a.createElement("header",{className:"title"},s.a.createElement("h6",null,n," ",s.a.createElement("strong",null,"".concat(q(t,a),"%")))))},X=Object(o.hot)((function(e){var t=e.data;if(!t)return null;var a=t.targeted;return s.a.createElement("div",{className:"metrics-charts-container"},s.a.createElement(u.e,null,s.a.createElement(u.d,{className:"first-col"},s.a.createElement(y,null,s.a.createElement("div",{className:"metrics-box summary targeted"},s.a.createElement(u.e,null,s.a.createElement(u.d,{className:"icon-col",width:"40"},s.a.createElement(H.D,{size:36,className:"icon"})),s.a.createElement(u.d,{className:"content-col"},s.a.createElement("h3",null,b("Metrics.Targeted")),s.a.createElement("p",{className:"stat"},t.targeted))))),s.a.createElement(y,{className:"engagement-box-container"},s.a.createElement("h2",null,b("Metrics.EngagementRates")),s.a.createElement("div",{className:"engagement-box"},s.a.createElement("p",{className:"stat"},"".concat(q(t.opened,t.accepted),"%")),s.a.createElement("p",{className:"caption"},b("Metrics.OpenRate")," ",s.a.createElement("small",null,"(",t.opened,")"))),s.a.createElement("hr",null),s.a.createElement("div",{className:"engagement-box"},s.a.createElement("p",{className:"stat"},"".concat(q(t.clicked,t.accepted),"%")),s.a.createElement("p",{className:"caption"},b("Metrics.ClickRate")," ",s.a.createElement("small",null,"(",t.clicked,")"))))),s.a.createElement(u.d,null,s.a.createElement(y,null,s.a.createElement("div",{className:"metrics-box summary accepted"},s.a.createElement(u.e,null,s.a.createElement(u.d,{className:"icon-col",width:"40"},s.a.createElement(H.w,{size:36,className:"icon"})),s.a.createElement(u.d,{className:"content-col"},s.a.createElement("h3",null,b("Metrics.Delivered")),s.a.createElement("p",{className:"stat"},t.accepted)))),s.a.createElement("hr",null),s.a.createElement(J,{number:t.accepted,total:a,title:b("Metrics.DeliveredRate")}))),s.a.createElement(u.d,null,s.a.createElement(y,null,s.a.createElement("div",{className:"metrics-box summary bounced"},s.a.createElement(u.e,null,s.a.createElement(u.d,{className:"icon-col",width:"40"},s.a.createElement(H.q,{size:36,className:"icon"})),s.a.createElement(u.d,{className:"content-col"},s.a.createElement("h3",null,b("Metrics.Bounced")),s.a.createElement("p",{className:"stat"},t.bounce)))),s.a.createElement("hr",null),s.a.createElement(J,{number:t.bounce,total:a,title:b("Metrics.BounceRate")}))),s.a.createElement(u.d,null,s.a.createElement(y,null,s.a.createElement("div",{className:"metrics-box summary rejected"},s.a.createElement(u.e,null,s.a.createElement(u.d,{className:"icon-col",width:"40"},s.a.createElement(H.k,{size:36,className:"icon"})),s.a.createElement(u.d,{className:"content-col"},s.a.createElement("h3",null,b("Metrics.Rejected")),s.a.createElement("p",{className:"stat"},t.rejected+t.admin_bounce)))),s.a.createElement("hr",null),s.a.createElement(J,{number:t.rejected+t.admin_bounce,total:a,title:b("Metrics.RejectedRate")})))))})),G=[{value:"last24Hours",label:b("Metrics.RangeLast24Hours")},{value:"last7Days",label:b("Metrics.RangeLast7Days")},{value:"thisMonth",label:b("Metrics.RangeThisMonth")},{value:"lastMonth",label:b("Metrics.RangeLastMonth")}],K=Object(o.hot)((function(e){var t=e.onChange,a=e.current;return s.a.createElement("ul",{className:"filters-button"},G.map((function(e,n){return e.value===a?s.a.createElement("li",{className:"active",key:"metricFilter".concat(n)},e.label):s.a.createElement("li",{key:"metricFilter".concat(n),onClick:function(){t(e.value)}},e.label)})))})),Q=Object(o.hot)((function(e){var t=e.progress,a=100*t.current/t.max;a>100&&(a=100),a=Math.round(a);var n=["progress-bar"];return t.current>t.max?n.push("overflow"):t.current===t.max?n.push("full"):a<100&&a>85&&n.push("almost"),s.a.createElement(y,{className:"section-sending-limit"},s.a.createElement("div",{className:"section-inner"},s.a.createElement(H.C,{size:32,color:"#1273e6"}),s.a.createElement("h3",null,b("Text.SendingLimit")),s.a.createElement("p",{className:"numbers"},t.current," / ",t.max),s.a.createElement("div",{className:n.join(" ")},s.a.createElement("div",{className:"progresser",style:{width:"".concat(a,"%")}})),s.a.createElement("a",{href:"https://wpmailpro.com/pricing?upgrade=true",target:"_blank",className:"upgrade-button"},b("Text.Upgrade"))))})),U=Object(o.hot)((function(){var e=_(),t=e.updateField,a=e.fields,n=e.isLoading,i=e.setLoading,o=e.displayNotice;Object(l.useEffect)((function(){a.license_activated&&!n&&m(!0)}),[a.metricsFilter]);var m=function(){var e=Object(c.a)(r.a.mark((function e(){var c,l,s=arguments;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(c=s.length>0&&void 0!==s[0]&&s[0],!n){e.next=3;break}return e.abrupt("return");case 3:return i(!0),e.next=6,S(c,a.metricsFilter);case 6:!0===(l=e.sent).success?(t("metrics",l.fields.metrics),l.fields.sending_limit&&t("sending_limit",l.fields.sending_limit)):(t("metrics",{}),t("sending_limit",null),o(l.message||l.error,"error")),i(!1);case 9:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}}(),d=function(){var e=Object(c.a)(r.a.mark((function e(t){return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:m(!0);case 1:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),p=a.metrics&&a.metrics.data?a.metrics.data:null,g=!!a.license_activated&&!!a.sending_domain;return s.a.createElement(s.a.Fragment,null,s.a.createElement(y,{className:"section-metrics"},g?s.a.createElement("header",null,g&&s.a.createElement(s.a.Fragment,null,s.a.createElement(K,{onChange:function(e){t("metricsFilter",e)},current:a.metricsFilter}),s.a.createElement(u.b,{size:"small",variant:"text",color:"blue",onClick:d,mutedOutline:!0},s.a.createElement(H.A,{size:"36",fill:"gray"})))):s.a.createElement(u.a,{size:"small",status:"warning",onDismiss:function(){}},b("Text.NoMetricsReason"))),g&&a.sending_limit&&a.sending_limit.max>0&&s.a.createElement(Q,{progress:a.sending_limit}),p&&g&&s.a.createElement(X,{data:p}))}));function $(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function Y(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?$(Object(a),!0).forEach((function(t){Object(d.a)(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):$(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}var Z,ee,te=function(e){var t=e.logs,a=e.resetExpand,n=Object(l.useState)(null),r=Object(m.a)(n,2),c=r[0],i=r[1],o=function(e){i(e)};return Object(l.useEffect)((function(){a&&i(null)}),[a]),s.a.createElement(u.h,{mt:"300"},s.a.createElement(u.l,{className:"logs-table"},s.a.createElement("thead",null,s.a.createElement(u.l.Row,{header:!0},s.a.createElement(u.l.HeaderCell,{className:"col-date"},b("Text.Date")),s.a.createElement(u.l.HeaderCell,{className:"col-subject"},b("Text.Subject")),s.a.createElement(u.l.HeaderCell,{className:"col-sender"},b("Text.Sender")),s.a.createElement(u.l.HeaderCell,{className:"col-recipient"},b("Text.Recipient")),s.a.createElement(u.l.HeaderCell,{className:"col-status"},b("Text.Status")),s.a.createElement(u.l.HeaderCell,{className:"col-actions"}," "))),s.a.createElement("tbody",null,0===t.length&&s.a.createElement(u.l.Row,null,s.a.createElement(u.l.Cell,{colSpan:"6"},s.a.createElement("p",{className:"empty-table-message"},b("Text.NoLogFound")))),t.map((function(e,t){var a=t===c;return s.a.createElement(s.a.Fragment,{key:"logRow".concat(t)},s.a.createElement(u.l.Row,null,s.a.createElement(u.l.Cell,{className:"col-date"},e.date),s.a.createElement(u.l.Cell,{className:"col-subject"},s.a.createElement("span",null,e.subject||"")),s.a.createElement(u.l.Cell,{className:"col-sender"},e.sender),s.a.createElement(u.l.Cell,{className:"col-recipient"},e.recipient),s.a.createElement(u.l.Cell,{className:"col-status"},s.a.createElement("span",{className:"log-status status-".concat(e.status)},s.a.createElement(u.p,{content:"".concat(e.details),id:"logRowDetails".concat(t)},e.status))),s.a.createElement(u.l.Cell,{className:"col-actions"},a?s.a.createElement(H.o,{size:"28",fill:"gray",onClick:function(){return o(null)}}):s.a.createElement(H.p,{size:"28",fill:"gray",onClick:function(){return o(t)}}))),a&&s.a.createElement(ae,{log:e,even:t%2==0}))})))))},ae=function(e){var t=e.even,a=e.log;return s.a.createElement(s.a.Fragment,null,s.a.createElement(u.l.Row,{className:"col-actions-row ".concat(t?"gray":"white")},s.a.createElement(u.l.Cell,{colSpan:6},s.a.createElement("div",{className:"log-row-data"},s.a.createElement("div",{className:"subject"},s.a.createElement("h6",null,b("Text.Subject")),s.a.createElement("p",null,a.subject)),!a.has_error&&a.engagement&&s.a.createElement(s.a.Fragment,null,s.a.createElement("div",{className:"engagement type-open"},s.a.createElement("h6",null,b("Text.EngagementOpened")),a.engagement.opened?s.a.createElement("p",null,s.a.createElement("span",{className:"icon positive dashicons dashicons-yes-alt"})):s.a.createElement("p",null,s.a.createElement("span",{className:"icon negative dashicons dashicons-no"}))),s.a.createElement("div",{className:"engagement type-clicked"},s.a.createElement("h6",null,b("Text.EngagementClicked")),a.engagement.clicked?s.a.createElement("p",null,s.a.createElement("span",{className:"icon positive dashicons dashicons-yes-alt"})):s.a.createElement("p",null,s.a.createElement("span",{className:"icon negative dashicons dashicons-no"})))),a.has_error&&s.a.createElement("div",{className:"error-info"},s.a.createElement("h6",null,b("Text.ErrorMessage")),s.a.createElement("p",null,a.details)),a.has_content&&s.a.createElement("nav",null,s.a.createElement("a",{href:a.preview_url,target:"_blank"},b("Text.ViewEmail")))))),s.a.createElement(u.l.Row,{className:"col-actions-fake-row"},s.a.createElement(u.l.Cell,{colSpan:6})))},ne=function(e){var t=e.currentPage,a=e.pagination,n=e.onPaginationChange;return 0===a.total?null:s.a.createElement("nav",{className:"logs-table-navigator"},a.total&&a.total>0&&s.a.createElement("p",{className:"total"},a.total," ",b("Text.XEmailsFound")),a.max_pages&&a.max_pages>0&&s.a.createElement(u.g,{currentPage:t,pageRange:5,pages:a.max_pages,onChange:n}))},re=Object(o.hot)((function(){var e=Object(l.useState)("last_30days"),t=Object(m.a)(e,2),a=t[0],n=t[1],i=Object(l.useState)(!1),o=Object(m.a)(i,2),p=o[0],g=o[1],f=_(),E=f.updateField,v=f.fields,h=f.isLoading,w=f.setLoading,N=f.displayNotice,O=function(){var e=Object(c.a)(r.a.mark((function e(){var t,n,c,l=arguments;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t=l.length>0&&void 0!==l[0]?l[0]:1,n=l.length>1&&void 0!==l[1]&&l[1],!h||n){e.next=4;break}return e.abrupt("return");case 4:return w(!0),g(!0),e.next=8,P({status:v.logsFilters.status||"all",recipient:v.logsFilters.recipient||"",date_range:a},t);case 8:!0===(c=e.sent).success?(E("logs",c.fields.logs.data),E("logsPagination",c.fields.logs.pagination)):(E("logs",[]),N(c.message||c.error,"error")),w(!1),g(!1);case 12:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}}(),j=function(e,t){E("logsFilters",Y(Y({},v.logsFilters),{},Object(d.a)({},e,t)))},x=function(e){n(e)};Object(l.useEffect)((function(){O(1,!0)}),[]),Object(l.useEffect)((function(){O(1,!0)}),[a]);var k=function(){var e=Object(c.a)(r.a.mark((function e(t){return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:t.preventDefault(),E("logsPage",1),O(1);case 3:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),F=function(e){var t=parseInt(e+1);t!==v.logsPage&&(E("logsPage",t),O(t,!0))},S=void 0!==v.logs;return s.a.createElement("div",{className:"section-logs"},s.a.createElement(y,null,s.a.createElement("form",{onSubmit:k},s.a.createElement(u.e,null,s.a.createElement(u.d,{width:.4},s.a.createElement(u.i,{label:b("Fields.Logs.FilterByStatus"),onChange:function(e){return j("status",e.target.value)},options:[{value:"all",label:b("Fields.Logs.StatusAll")},{value:"pending",label:b("Fields.Logs.StatusPending")},{value:"delivered",label:b("Fields.Logs.StatusDelivered")},{value:"failed",label:b("Fields.Logs.StatusFailed")},{value:"error",label:b("Fields.Logs.StatusError")}]})),s.a.createElement(u.d,{width:.4},s.a.createElement(u.n,{id:"filter_recipient",label:b("Fields.Logs.FilterByRecipient"),placeholder:"someone@email.com",value:v.logsFilters.recipient||"",onChange:function(e){return j("recipient",e.target.value)}})),s.a.createElement(u.d,{width:.2,className:"align-bottom"},s.a.createElement(u.b,{size:"default",width:"100%",variant:"outline",color:"blue",onClick:k,mutedOutline:!0},b("Button.Filter")))))),s.a.createElement(y,{className:"date-range-section"},s.a.createElement("nav",{className:"date-range-selectors"},s.a.createElement("ul",{className:"range-selector"},s.a.createElement("li",{className:"".concat("last_hour"===a?"active":""),onClick:function(){return x("last_hour")}},b("Fields.Logs.LastHour")),s.a.createElement("li",{className:"".concat("last_24hours"===a?"active":""),onClick:function(){return x("last_24hours")}},b("Fields.Logs.Last24Hours")),s.a.createElement("li",{className:"".concat("last_7days"===a?"active":""),onClick:function(){return x("last_7days")}},b("Fields.Logs.Last7Days")),s.a.createElement("li",{className:"".concat("last_30days"===a?"active":""),onClick:function(){return x("last_30days")}},b("Fields.Logs.Last30Days"))),s.a.createElement(u.b,{size:"small",variant:"text",color:"blue",onClick:k,mutedOutline:!0,className:"refresh-button"},s.a.createElement(H.A,{size:"36",fill:"gray"})))),S&&s.a.createElement(s.a.Fragment,null,s.a.createElement(ne,{currentPage:v.logsPage,pagination:v.logsPagination||{},onPaginationChange:F}),s.a.createElement(te,{logs:v.logs,resetExpand:p}),s.a.createElement(ne,{currentPage:v.logsPage,pagination:v.logsPagination||{},onPaginationChange:F})))})),ce=Object(o.hot)((function(){var e=_(),t=e.fields,a=e.updateField,n=e.setLoading,l=e.displayNotice,i=Object(u.q)().copy;if(!t.license_activated)return s.a.createElement(y,null,s.a.createElement(u.a,{size:"small",status:"warning",onDismiss:function(){}},b("Text.NoDNSReason")));var o=function(){var e=Object(c.a)(r.a.mark((function e(c){var s,i;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n(!0),e.next=3,L(t.sending_domain);case 3:(s=e.sent).message&&(i=s.error?"error":s.success?"success":"warning",l(s.message,i)),s.fields&&(s.fields.sending_domain&&a("sending_domain",s.fields.sending_domain),s.fields.current_sending_domain&&a("current_sending_domain",s.fields.current_sending_domain),s.fields.sending_domain_record_hostname&&s.fields.sending_domain_record_value&&(a("sending_domain_record_hostname",s.fields.sending_domain_record_hostname),a("sending_domain_record_value",s.fields.sending_domain_record_value),a("domain_dkim_record_is_valid",null),a("domain_dkim_record_verified",null))),n(!1);case 7:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),m=function(){var e=Object(c.a)(r.a.mark((function e(c){var s,i;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n(!0),e.next=3,T(t.sending_domain);case 3:(s=e.sent).message&&(i=s.error?"error":s.fields.domain_dkim_record_is_valid?"success":"warning",l(s.message,i)),s.fields&&(void 0!==s.fields.domain_dkim_record_is_valid&&a("domain_dkim_record_is_valid",s.fields.domain_dkim_record_is_valid),void 0!==s.fields.domain_dkim_record_verified&&a("domain_dkim_record_verified",s.fields.domain_dkim_record_verified),void 0!==s.fields.sender_mail&&(a("sender_mail",s.fields.sender_mail),a("sender_mail_prefix",s.fields.sender_mail_prefix))),n(!1);case 7:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),d=function(e){i(e),l(b("Text.TextCopied"),"success")},p=!!t.sending_domain_record_hostname&&!!t.sending_domain_record_value,g=parseInt(t.domain_dkim_record_verified)>0,f=t.sending_domain||"";return f===t.default_sending_domain&&(f=""),s.a.createElement(s.a.Fragment,null,s.a.createElement(y,{title:b("Panels.DNS.SendingDomainField"),className:"section-create-domain"},s.a.createElement("div",{className:"restricted-width"},s.a.createElement(u.n,{id:"sending_domain",label:b("Fields.DNS.SendingDomain"),placeholder:"your-domain.com",name:"sending_domain",value:f,onChange:function(e){a(e.target.name,e.target.value)},connectRight:s.a.createElement(u.b,{size:"default",variant:"outline",color:"blue",onClick:o,disabled:t.sending_domain===t.current_sending_domain},s.a.createElement(u.b.Icon,{as:H.a,size:20,mr:"200"}),b("Button.CreateDomain"))}))),p&&s.a.createElement(s.a.Fragment,null,s.a.createElement(y,{title:b("Panels.DNS.SendingDomainRecords"),className:"section-domain-records"},s.a.createElement(u.e,null,s.a.createElement(u.d,{className:"dkim-record-hostname",width:1/3},s.a.createElement(u.f,{orientation:"vertical"},s.a.createElement(u.f.Label,null,b("Text.Hostname")," ",s.a.createElement("span",{className:"copy-link",onClick:function(){d(t.sending_domain_record_hostname)}},b("Text.Copy"))),s.a.createElement(u.f.Value,null,s.a.createElement("code",{className:"dkim-record-hostname"},t.sending_domain_record_hostname)))),s.a.createElement(u.d,{className:"dkim-record-value"},s.a.createElement(u.f,{orientation:"vertical"},s.a.createElement(u.f.Label,null,b("Text.Value")," ",s.a.createElement("span",{className:"copy-link",onClick:function(){d(t.sending_domain_record_value)}},b("Text.Copy"))),s.a.createElement(u.f.Value,null,s.a.createElement("code",{className:"dkim-record-value"},t.sending_domain_record_value)))))),s.a.createElement(y,{title:b("Panels.DNS.SendingDomainVerify"),className:"section-verify-domain"},g&&!t.domain_dkim_record_is_valid&&s.a.createElement(u.a,{mb:"300",size:"small",status:"warning",onDismiss:function(){}},b("Fields.Verify.PropagationWarning")),s.a.createElement(u.e,null,s.a.createElement(u.d,{width:"content"},s.a.createElement(u.b,{variant:"outline",color:"blue",disabled:!p,onClick:m},b("Button.Verify")," ",t.sending_domain)),s.a.createElement(u.d,{className:"vertical-align"},g&&s.a.createElement(s.a.Fragment,null,t.domain_dkim_record_is_valid?s.a.createElement("p",{className:"last-verification-status valid"},s.a.createElement("span",{className:"icon dashicons dashicons-yes-alt"}),s.a.createElement("span",{className:"text"},b("Text.DkimValid")," ",s.a.createElement("small",null,"(",t.domain_dkim_record_verified,")"))):s.a.createElement("p",{className:"last-verification-status invalid"},s.a.createElement("span",{className:"icon dashicons dashicons-no"}),s.a.createElement("span",{className:"text"},b("Text.DkimInvalid")," ",s.a.createElement("small",null,"(",t.domain_dkim_record_verified,")"))))))),s.a.createElement("p",{className:"help-link"},s.a.createElement("a",{href:"https://docs.wpmailpro.com/docs/configure-sending-domain/",target:"_blank"},s.a.createElement(H.r,{size:18})," ",b("Text.NeedHelp")))))})),le=function(e){var t=e.fields,a=e.updateField;return t.domain_dkim_record_is_valid?s.a.createElement(u.i,{className:"sender-mail-suffix",name:"sender_mail_suffix",value:t.sender_mail_suffix,onChange:function(e){return a("sender_mail_suffix",e.target.value)},options:[{value:t.sending_domain,label:"@".concat(t.sending_domain)},{value:t.default_sending_domain,label:"@".concat(t.default_sending_domain)}]}):s.a.createElement(u.n,{id:"sender_mail_suffix",value:"@".concat(t.default_sending_domain),disabled:!0})},se=Object(o.hot)((function(){var e=_(),t=e.fields,a=e.updateField,n=e.setLoading,l=e.displayNotice,i=function(){var e=Object(c.a)(r.a.mark((function e(c){var s;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n(!0),e.next=3,M(t.license);case 3:!0===(s=e.sent).success?(a("license_activated",!0),l(s.message,"success")):(a("license_activated",!1),l(s.message||s.error,"error")),n(!1);case 6:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),o=function(){var e=Object(c.a)(r.a.mark((function e(c){var s;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n(!0),e.next=3,R(t.license);case 3:!0===(s=e.sent).success?(a("license_activated",!1),l(s.message,"success")):l(s.message||s.error,"error"),n(!1);case 6:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),m=function(){var e=Object(c.a)(r.a.mark((function e(a){var c;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return a.preventDefault(),n(!0),e.next=4,C({sender_mail_prefix:t.sender_mail_prefix,sender_mail_suffix:t.sender_mail_suffix,sender_name:t.sender_name});case 4:!0===(c=e.sent).success?l(c.message,"success"):l(c.message||c.error,"error"),n(!1);case 7:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),d=function(){var e=Object(c.a)(r.a.mark((function e(a){var c;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return a.preventDefault(),n(!0),e.next=4,C({reply_to_mail:t.reply_to_mail,reply_to_name:t.reply_to_name});case 4:!0===(c=e.sent).success?l(c.message,"success"):l(c.message||c.error,"error"),n(!1);case 7:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),p=function(){var e=Object(c.a)(r.a.mark((function e(t){var c;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.preventDefault(),n(!0),a("force_sender_email",t.target.checked),e.next=5,C({force_sender_email:t.target.checked});case 5:!0===(c=e.sent).success?l(c.message,"success"):l(c.message||c.error,"error"),n(!1);case 8:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),g=function(){var e=Object(c.a)(r.a.mark((function e(t){var c;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.preventDefault(),n(!0),a("reply_to_enabled",t.target.checked),e.next=5,C({reply_to_enabled:t.target.checked});case 5:!0===(c=e.sent).success?l(c.message,"success"):l(c.message||c.error,"error"),n(!1);case 8:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),f=function(){var e=Object(c.a)(r.a.mark((function e(a){var c;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return a.preventDefault(),n(!0),e.next=4,D(t.test_email_recipient);case 4:!0===(c=e.sent).success?l(c.message,"success"):l(c.message||c.error,"error"),n(!1);case 7:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),E=!!t.license_activated;return s.a.createElement(s.a.Fragment,null,E&&s.a.createElement(s.a.Fragment,null,s.a.createElement(y,{title:b("Panels.Settings.EmailSender"),className:"email-sender-settings"},t.sending_domain===t.default_sending_domain&&s.a.createElement(u.a,{mb:"500",size:"small",status:"warning",onDismiss:function(){}},b("Fields.Settings.WarningDefaultDomain1")," ",s.a.createElement("a",{href:"https://docs.wpmailpro.com/docs/configure-sending-domain/",target:"_blank"},b("Fields.Settings.WarningDefaultDomain2"))),s.a.createElement(u.e,null,s.a.createElement(u.d,{width:.4},s.a.createElement(u.n,{id:"sender_mail",label:b("Fields.Settings.EmailSenderMail"),placeholder:"john.doe",name:"sender_mail_prefix",value:t.sender_mail_prefix,disabled:!t.domain_dkim_record_is_valid,onChange:function(e){a(e.target.name,e.target.value)},connectRight:s.a.createElement(le,{fields:t,updateField:a})})),s.a.createElement(u.d,{width:.4},s.a.createElement(u.n,{id:"sender_name",label:b("Fields.Settings.EmailSenderName"),placeholder:"John Doe",name:"sender_name",value:t.sender_name,onChange:function(e){a(e.target.name,e.target.value)}})),s.a.createElement(u.d,{className:"align-bottom"},s.a.createElement(u.b,{size:"default",width:"100%",variant:"outline",color:"blue",onClick:m,mutedOutline:!0},b("Button.Save")))),s.a.createElement("div",{className:"force-sender-setting"},s.a.createElement(u.c,{id:"force_sender_email",name:"force_sender_email",checked:t.force_sender_email,label:b("Fields.Settings.ForceSenderEmail"),helpText:b("Fields.Settings.ForceSenderEmailDesc"),onChange:p}))),s.a.createElement(y,{title:b("Fields.Settings.ReplyToEnabled"),className:"reply-to-section"},s.a.createElement(u.c,{id:"reply_to_enabled",name:"reply_to_enabled",checked:t.reply_to_enabled,label:b("Fields.Settings.ReplyToEnabledLabel"),onChange:g}),t.reply_to_enabled&&s.a.createElement(u.e,{mt:300},s.a.createElement(u.d,{width:.4},s.a.createElement(u.n,{id:"reply_to_mail",label:b("Fields.Settings.EmailSenderMail"),placeholder:"john.doe@mail.com",name:"reply_to_mail",type:"email",value:t.reply_to_mail,onChange:function(e){a(e.target.name,e.target.value)}})),s.a.createElement(u.d,{width:.4},s.a.createElement(u.n,{id:"reply_to_name",label:b("Fields.Settings.EmailSenderName"),placeholder:"John Doe",name:"reply_to_name",value:t.reply_to_name,onChange:function(e){a(e.target.name,e.target.value)}})),s.a.createElement(u.d,{className:"align-bottom"},s.a.createElement(u.b,{size:"default",width:"100%",variant:"outline",color:"blue",onClick:d,mutedOutline:!0},b("Button.Save"))))),s.a.createElement(y,{title:b("Panels.DNS.TestEmail")},s.a.createElement("form",{className:"restricted-width",onSubmit:f},s.a.createElement(u.n,{id:"test_email_recipient",label:b("Fields.DNS.TestEmailRecipient"),placeholder:"some@email.com",name:"test_email_recipient",value:t.test_email_recipient||"",onChange:function(e){a(e.target.name,e.target.value)},connectRight:s.a.createElement(u.b,{size:"default",variant:"outline",color:"blue",disabled:!t.test_email_recipient,onClick:f},s.a.createElement(u.b.Icon,{as:H.l,size:20,mr:"200"}),b("Button.Send"))})))),s.a.createElement(y,{title:b("Panels.Settings.License")},s.a.createElement("div",{className:"restricted-width xl"},s.a.createElement(u.n,{id:"license",label:b("Fields.Settings.PluginLicense"),placeholder:b("Fields.Settings.PluginLicense"),name:"license",value:t.license,onChange:function(e){a(e.target.name,e.target.value)},connectRight:s.a.createElement(s.a.Fragment,null,s.a.createElement(u.b,{size:"default",onClick:i,mutedOutline:!0,color:"blue"},b("Button.LicenseActivate")),s.a.createElement(u.b,{size:"default",onClick:o,color:"red"},b("Button.LicenseDeactivate")))})),!t.license_activated&&s.a.createElement("p",{className:"free-license-callout"},s.a.createElement("a",{href:"https://wpmailpro.com/pricing?upgrade=true",target:"_blank"},s.a.createElement(H.E,{size:22})," ",b("Text.NeedFreeLicense")))),s.a.createElement("p",{className:"help-link"},s.a.createElement("a",{href:"https://docs.wpmailpro.com/docs/settings/",target:"_blank"},s.a.createElement(H.r,{size:18})," ",b("Text.NeedHelp"))))})),ie=[{title:b("Tabs.Metrics"),component:U,slug:"metrics"},{title:b("Tabs.Logs"),component:re,slug:"logs"},{title:b("Tabs.DNS"),component:ce,slug:"dns"},{title:b("Tabs.Settings"),component:se,slug:"settings"}],oe=ie.findIndex((function(e){return e.slug===window.WPMailPro.data.current_tab}))||0,ue=function(e){var t=document.querySelector("ul#adminmenu li#toplevel_page_wpmailpro ul.wp-submenu li.current"),a=document.querySelector("ul#adminmenu li#toplevel_page_wpmailpro ul.wp-submenu li a[href$=".concat(ie[e].slug,"]")).parentNode;t&&t.classList.remove("current"),a&&a.classList.add("current")},me=Object(o.hot)((function(){var e=_().hideNotice,t=Object(u.r)({tabs:ie.map((function(e){return{content:e.title}}))}),a=t.tabIndex,n=t.setTabIndex,r=t.tabs;Object(l.useEffect)((function(){n(oe),ue(oe)}),[]);return s.a.createElement(s.a.Fragment,null,s.a.createElement("nav",{className:"plugin-tabs"},s.a.createElement(u.m,{selected:a,tabs:r,onSelect:function(t){ue(t),n(t),e()},fitted:!0})),ie.map((function(e,t){if(t!==a)return null;var n=e.component;return s.a.createElement("div",{className:"tab-content",key:"tabContent".concat(t)},s.a.createElement(n,null),s.a.createElement(N,null))})))})),de=Object(o.hot)((function(){return s.a.createElement(u.o,null,s.a.createElement(v,null,s.a.createElement("div",{className:"wrap"},s.a.createElement(w,null),s.a.createElement("div",{className:"admin-page-core-content"},s.a.createElement(me,null)))))}));if(document.querySelector("#wpmp-admin-page-container")){document.addEventListener("DOMContentLoaded",(function(){Object(i.render)(s.a.createElement(de,null),document.querySelector("#wpmp-admin-page-container"))}))}Z=jQuery,(ee=document.querySelector("#email-preview-iframe"))&&function(){var e=Object(c.a)(r.a.mark((function e(){var t;return r.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,z(Z(ee).data("email-id"));case 2:(t=e.sent).success&&t.content&&Z(ee).contents().find("body").html(t.content);case 4:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}}()()}},[[203,1,2]]]);
//# sourceMappingURL=admin-89177f27.js.map