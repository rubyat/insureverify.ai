import{_ as n}from"./SiteLayout.vue_vue_type_script_setup_true_lang-C4QPYVyk.js";import{d as r,c as l,o,a as s,u as i,h as u,w as c,b as e,e as t,F as p}from"./app-B_Zw6QGP.js";const _=r({__name:"Docs",setup(d){return(m,a)=>(o(),l(p,null,[s(i(u),{title:"API Docs"}),s(n,null,{default:c(()=>[...a[0]||(a[0]=[e("section",{class:"py-16 px-6 md:px-12 max-w-4xl mx-auto space-y-6"},[e("h1",{class:"text-3xl md:text-4xl font-bold"},"API Quick Start"),e("pre",{class:"bg-gray-900 text-gray-100 p-4 rounded overflow-x-auto"},[e("code",null,`POST https://api.insureverify.ai/v1/verify
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json

{
  "license_number": "D123456789",
  "insurance_policy": "XYZ123456789"
}`)]),e("h2",{class:"text-2xl font-semibold"},"Endpoints"),e("ul",{class:"list-disc pl-6"},[e("li",null,[e("code",null,"/v1/verifylicense"),t(" – license status, validity, expiry")]),e("li",null,[e("code",null,"/v1/verifyinsurance"),t(" – insurance provider, status, coverage date")])]),e("p",{class:"text-foreground/70"},"Authentication: Bearer Token. Rate limits: Free 100/mo, Pro 10k/mo, Enterprise custom."),e("p",null,[t("Support: "),e("a",{href:"mailto:support@insureverify.ai",class:"text-blue-600 underline"},"support@insureverify.ai")])],-1)])]),_:1})],64))}});export{_ as default};
