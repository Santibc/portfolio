/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const f={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round"};/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const g=([e,t,o])=>{const n=document.createElementNS("http://www.w3.org/2000/svg",e);return Object.keys(t).forEach(c=>{n.setAttribute(c,String(t[c]))}),o!=null&&o.length&&o.forEach(c=>{const d=g(c);n.appendChild(d)}),n},y=(e,t={})=>{const o="svg",n={...f,...t};return g([o,n,e])};/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const A=e=>{for(const t in e)if(t.startsWith("aria-")||t==="role"||t==="title")return!0;return!1};/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const b=(...e)=>e.filter((t,o,n)=>!!t&&t.trim()!==""&&n.indexOf(t)===o).join(" ").trim();/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const v=e=>e.replace(/^([A-Z])|[\s-_]+(\w)/g,(t,o,n)=>n?n.toUpperCase():o.toLowerCase());/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const E=e=>{const t=v(e);return t.charAt(0).toUpperCase()+t.slice(1)};/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const M=e=>Array.from(e.attributes).reduce((t,o)=>(t[o.name]=o.value,t),{}),m=e=>typeof e=="string"?e:!e||!e.class?"":e.class&&typeof e.class=="string"?e.class.split(" "):e.class&&Array.isArray(e.class)?e.class:"",p=(e,{nameAttr:t,icons:o,attrs:n})=>{var u;const c=e.getAttribute(t);if(c==null)return;const d=E(c),i=o[d];if(!i)return console.warn(`${e.outerHTML} icon name was not found in the provided icons object.`);const l=M(e),a=A(l)?{}:{"aria-hidden":"true"},r={...f,"data-lucide":c,...a,...n,...l},s=m(l),w=m(n),h=b("lucide",`lucide-${c}`,...s,...w);h&&Object.assign(r,{class:h});const k=y(i,r);return(u=e.parentNode)==null?void 0:u.replaceChild(k,e)};/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const S=[["path",{d:"M12 5v14"}],["path",{d:"m19 12-7 7-7-7"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const T=[["path",{d:"M7 7h10v10"}],["path",{d:"M7 17 17 7"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const L=[["path",{d:"M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z"}],["circle",{cx:"12",cy:"13",r:"3"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const I=[["path",{d:"M20 6 9 17l-5-5"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const j=[["circle",{cx:"12",cy:"12",r:"10"}],["path",{d:"M12 6v6l4 2"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const x=[["path",{d:"M4 5h16"}],["path",{d:"M4 12h16"}],["path",{d:"M4 19h16"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const N=[["path",{d:"M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const D=[["path",{d:"M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"}],["path",{d:"M20 2v4"}],["path",{d:"M22 4h-4"}],["circle",{cx:"4",cy:"20",r:"2"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const O=[["path",{d:"M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const P=[["circle",{cx:"12",cy:"12",r:"4"}],["path",{d:"M12 2v2"}],["path",{d:"M12 20v2"}],["path",{d:"m4.93 4.93 1.41 1.41"}],["path",{d:"m17.66 17.66 1.41 1.41"}],["path",{d:"M2 12h2"}],["path",{d:"M20 12h2"}],["path",{d:"m6.34 17.66-1.41 1.41"}],["path",{d:"m19.07 4.93-1.41 1.41"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const B=[["path",{d:"M18 6 6 18"}],["path",{d:"m6 6 12 12"}]];/**
 * @license lucide v1.14.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const C=({icons:e={},nameAttr:t="data-lucide",attrs:o={},root:n=document,inTemplates:c}={})=>{if(!Object.values(e).length)throw new Error(`Please provide an icons object.
If you want to use all the icons you can import it like:
 \`import { createIcons, icons } from 'lucide';
lucide.createIcons({icons});\``);if(typeof n>"u")throw new Error("`createIcons()` only works in a browser environment.");if(Array.from(n.querySelectorAll(`[${t}]`)).forEach(i=>p(i,{nameAttr:t,icons:e,attrs:o})),c&&Array.from(n.querySelectorAll("template")).forEach(l=>C({icons:e,nameAttr:t,attrs:o,root:l.content,inTemplates:c})),t==="data-lucide"){const i=n.querySelectorAll("[icon-name]");i.length>0&&(console.warn("[Lucide] Some icons were found with the now deprecated icon-name attribute. These will still be replaced for backwards compatibility, but will no longer be supported in v1.0 and you should switch to data-lucide"),Array.from(i).forEach(l=>p(l,{nameAttr:"icon-name",icons:e,attrs:o})))}};(function(){const e="dorilokos-theme",t=window.matchMedia("(prefers-color-scheme: dark)");function o(a){const r=document.documentElement;a==="dark"?(r.setAttribute("data-theme","dark"),r.setAttribute("data-bs-theme","dark"),r.classList.add("dark")):(r.setAttribute("data-theme","light"),r.setAttribute("data-bs-theme","light"),r.classList.remove("dark"));const s=document.getElementById("flatpickrDarkTheme");s&&(s.disabled=a!=="dark"),window.dispatchEvent(new CustomEvent("dorilokos:theme-changed",{detail:{theme:a}}))}function n(a){return a==="light"||a==="dark"?a:t.matches?"dark":"light"}function c(){return localStorage.getItem(e)||"auto"}function d(a){localStorage.setItem(e,a),o(n(a)),i(a)}function i(a){var s;const r=(s=document.querySelector('meta[name="csrf-token"]'))==null?void 0:s.content;r&&fetch("/profile/theme",{method:"PATCH",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":r,Accept:"application/json"},body:JSON.stringify({theme:a}),credentials:"same-origin"}).catch(function(){})}function l(){const a=c(),s=n(a)==="dark"?"light":"dark";d(s)}t.addEventListener("change",function(){c()==="auto"&&o(n("auto"))}),document.addEventListener("DOMContentLoaded",function(){const a=document.getElementById("themeToggleBtn");a&&a.addEventListener("click",l);const r=document.documentElement.getAttribute("data-theme")==="dark",s=document.getElementById("flatpickrDarkTheme");s&&(s.disabled=!r)}),window.DorilokosTheme={toggle:l,set:d,get:c,resolved:function(){return n(c())}}})();export{S as A,L as C,x as M,D as S,B as X,T as a,I as b,C as c,j as d,N as e,O as f,P as g};
