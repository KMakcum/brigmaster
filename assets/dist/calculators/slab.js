import{C as e,S as t,a as n,b as r,d as i,g as a,h as o,m as s,o as c,p as l,r as u,t as d,u as f,v as p,w as m,x as h,y as g}from"../shared/bootstrap.js";function _(t,n,r){let i=a(t)?.querySelector(`[data-slab-scheme]`);if(!i)return;let o=n?.concrete||{},s=n?.reinforcement||null,c=n?.formwork||null,l=Number(r.length),u=Number(r.width),d=Number(o.areaM2),f=Number.isFinite(l)&&l>0?l:Number.isFinite(d)&&d>0?Math.sqrt(d):1,p=Number.isFinite(u)&&u>0?u:Number.isFinite(d)&&d>0?Math.sqrt(d):1,h=f>=p,g=h?f:p,_=h?p:f,v=Math.max(g,_),y=v>0?g/v:1,b=v>0?_/v:1,x=Math.abs(f-p)<.001,S=0,C=0;x?(S=200,C=200):(S=Math.max(250,Math.min(400,Math.round(404*y))),C=Math.max(110,Math.min(180,Math.round(180*b))));let w=x?152:120,T=64+Math.round((404-S)/2),E=w-Math.round(C/2),D=T+S,O=E+C,k=Math.max(16,E-24),A=Math.max(12,k-4),j=k+4,M=Math.max(8,k-10),N=Math.min(516,D+22),P=Math.min(538,D+44),F=Math.min(548,D+30),I=Math.min(550,D+52),L=h?`vertical`:`horizontal`,R=h?`horizontal`:`vertical`,z=`L: ${m(f)} м`,B=`W: ${m(p)} м`,V=[`<li><strong>Площадь плиты:</strong> ${m(o.areaM2)} м2</li>`,`<li><strong>Длина плиты:</strong> ${m(f)} м</li>`,`<li><strong>Ширина плиты:</strong> ${m(p)} м</li>`,`<li><strong>Высота плиты:</strong> ${m(r.height)} м</li>`];c&&V.push(`<li><strong>Высота опалубки:</strong> ${m(c.heightM)} м</li>`),s&&V.push(`<li><strong>Арматура:</strong><br> Ø${m(s.diameterMm)}, шаг ${m(s.stepMm)} мм, ${m(s.layers)} слоя</li>`);let H=Math.min(S,C),U=Math.max(6,Math.min(14,Math.round(H*.035)));U=Math.min(U,Math.max(0,Math.floor(H/2)-2));let W=T+U,G=D-U,K=E+U,q=O-U,J=G-W,Y=q-K,X=s?`
      <g class="brigmaster-slab-scheme__rebar">
        <line x1="${W}" y1="${K}" x2="${G}" y2="${K}" />
        <line x1="${W}" y1="${K+Y*.33}" x2="${G}" y2="${K+Y*.33}" />
        <line x1="${W}" y1="${K+Y*.66}" x2="${G}" y2="${K+Y*.66}" />
        <line x1="${W}" y1="${q}" x2="${G}" y2="${q}" />
        <line x1="${W}" y1="${K}" x2="${W}" y2="${q}" />
        <line x1="${W+J*.2}" y1="${K}" x2="${W+J*.2}" y2="${q}" />
        <line x1="${W+J*.4}" y1="${K}" x2="${W+J*.4}" y2="${q}" />
        <line x1="${W+J*.6}" y1="${K}" x2="${W+J*.6}" y2="${q}" />
        <line x1="${W+J*.8}" y1="${K}" x2="${W+J*.8}" y2="${q}" />
        <line x1="${G}" y1="${K}" x2="${G}" y2="${q}" />
      </g>`:``,Z=c?`
      <rect x="${T-12}" y="${E-12}" width="${S+24}" height="${C+24}" class="brigmaster-slab-scheme__formwork" />`:``,Q=[`<span class="brigmaster-slab-scheme__legend-item"><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--slab" aria-hidden="true"></span><span class="brigmaster-slab-scheme__legend-text">— плита</span></span>`];s&&Q.push(`<span class="brigmaster-slab-scheme__legend-item"><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--rebar" aria-hidden="true"></span><span class="brigmaster-slab-scheme__legend-text">— арматура</span></span>`),c&&Q.push(`<span class="brigmaster-slab-scheme__legend-item"><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--formwork" aria-hidden="true"></span><span class="brigmaster-slab-scheme__legend-text">— опалубка</span></span>`);let $=`<div class="brigmaster-slab-scheme__legend" aria-label="Легенда схемы">${Q.join(``)}</div>`;i.innerHTML=`
      <h3 class="brigmaster-estimator__scheme-title">Схема плиты</h3>
      <div class="brigmaster-slab-scheme-layout">
        <ul class="brigmaster-slab-scheme__facts" data-scheme-facts>
          ${V.join(``)}
        </ul>
        <svg viewBox="0 0 560 260" class="brigmaster-slab-scheme" aria-label="Схема плитного фундамента">
          <rect x="${T}" y="${E}" width="${S}" height="${C}" class="brigmaster-slab-scheme__slab" />
          ${Z}
          ${X}
          ${R===`horizontal`?`<line x1="${T}" y1="${k}" x2="${D}" y2="${k}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${T}" y1="${A}" x2="${T}" y2="${j}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${D}" y1="${A}" x2="${D}" y2="${j}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${T+S/2}" y="${M}" text-anchor="middle" class="brigmaster-slab-scheme__label">${e(z)}</text>`:`<line x1="${P}" y1="${E}" x2="${P}" y2="${O}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${P-4}" y1="${E}" x2="${P+4}" y2="${E}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${P-4}" y1="${O}" x2="${P+4}" y2="${O}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${I}" y="${(E+O)/2}" class="brigmaster-slab-scheme__label">${e(z)}</text>`}
          ${L===`horizontal`?`<line x1="${T}" y1="${E-24}" x2="${D}" y2="${E-24}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${T}" y1="${E-28}" x2="${T}" y2="${E-20}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${D}" y1="${E-28}" x2="${D}" y2="${E-20}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${T+S/2}" y="${E-34}" text-anchor="middle" class="brigmaster-slab-scheme__label">${e(B)}</text>`:`<line x1="${N}" y1="${E}" x2="${N}" y2="${O}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${N-4}" y1="${E}" x2="${N+4}" y2="${E}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${N-4}" y1="${O}" x2="${N+4}" y2="${O}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${F}" y="${(E+O)/2}" class="brigmaster-slab-scheme__label">${e(B)}</text>`}
        </svg>
      </div>
      ${$}
    `}function v(e,t,r){let i=a(e)?.querySelector(`[data-result]`);if(!i)return;let s=i.querySelector(`[data-result-concrete-volume]`),c=i.querySelector(`[data-result-concrete-area]`),l=i.querySelector(`[data-result-concrete-height]`),d=i.querySelector(`[data-result-card="reinforcement"]`),f=i.querySelector(`[data-result-card="formwork"]`),p=t?.concrete||{},h=t?.reinforcement||null,g=t?.formwork||null;s&&(s.textContent=m(p.volumeM3)),c&&(c.textContent=m(p.areaM2)),l&&(l.textContent=m(p.heightM)),d&&(h?(d.hidden=!1,d.innerHTML=`
          <h3>Арматура</h3>
          <p><strong>Масса:</strong> ${m(h.massKg)} кг</p>
          <p><strong>Общая длина (с запасом):</strong> ${m(h.totalLengthWithReserveM)} м</p>
          <p><strong>Сетка:</strong> Ø${m(h.diameterMm)}, шаг ${m(h.stepMm)} мм, ${m(h.layers)} слоя</p>
        `):(d.hidden=!0,d.innerHTML=``)),f&&(g?(f.hidden=!1,f.innerHTML=`
          <h3>Опалубка</h3>
          <p><strong>Площадь щитов:</strong> ${m(g.areaM2)} м2</p>
          <p><strong>Погонные метры:</strong> ${m(g.linearMeters)} м</p>
          <p><strong>Высота:</strong> ${m(g.heightM)} м</p>
        `):(f.hidden=!0,f.innerHTML=``)),_(e,t,r),u(i.querySelector(`[data-result-card="mixture"]`),t?.mixture,`Смесь и материалы`),n(i),i.hidden=!1,i.classList.add(`is-success`),o(e)}function y(e){if(e.querySelector(`[name="calculator"]`)?.value!==`slab_foundation`)return;s(e);let n=e.querySelector(`[name="mode"]`)?.value||`dimensions`,r=e.querySelector(`[name="includeReinforcement"]`),i=e.querySelector(`[name="includeFormwork"]`);h(e,n===`area`);let a=!!r?.checked,o=!!i?.checked,c=e.querySelector(`[data-field-group="slab-dimensions"]`),l=e.querySelector(`[data-field-group="slab-area"]`),u=e.querySelector(`[data-field-group="slab-height"]`),d=e.querySelector(`[data-area-mode-notice]`),f=e.querySelector(`[data-field-group="slab-reinforcement"]`),p=e.querySelector(`[data-field-group="slab-formwork"]`);t(c,n===`dimensions`),t(l,n===`area`),t(u,!0),t(d,!1),t(f,n===`dimensions`&&a),t(p,n===`dimensions`&&o)}function b(e){if(e.querySelector(`[name="calculator"]`)?.value!==`slab_foundation`)return;let t=e.querySelector(`[name="mode"]`),n=e.querySelector(`[name="includeReinforcement"]`),r=e.querySelector(`[name="includeFormwork"]`);y(e),[t,n,r].forEach(t=>{t&&t.addEventListener(`change`,()=>{l(e),p(e),y(e)})})}function x(e,t){let n=g(t,`calculator`)||`slab_foundation`,a=g(t,`mode`),o={calculator:n,mode:a},s=f(e,o),l=a===`area`,u=l?!1:t.get(`includeReinforcement`)!==null,d=l?!1:t.get(`includeFormwork`)!==null;o.includeReinforcement=u,o.includeFormwork=d,o.height=g(t,`height`),s=i(e,o,`height`,`Высота должна быть больше 0.`)&&s,a===`dimensions`?(o.length=g(t,`length`),o.width=g(t,`width`),s=i(e,o,`length`,`Длина должна быть больше 0.`)&&s,s=i(e,o,`width`,`Ширина должна быть больше 0.`)&&s):a===`area`?(o.area=g(t,`area`),s=i(e,o,`area`,`Площадь должна быть больше 0.`)&&s):(r(e,`mode`,`Выберите режим dimensions или area.`),s=!1),u&&(o.rebarDiameterMm=g(t,`rebarDiameterMm`),o.rebarStepMm=g(t,`rebarStepMm`),o.rebarLayers=g(t,`rebarLayers`),o.rebarReservePercent=g(t,`rebarReservePercent`),s=i(e,o,`rebarDiameterMm`,`Диаметр арматуры должен быть больше 0.`)&&s,s=i(e,o,`rebarStepMm`,`Шаг арматуры должен быть больше 0.`)&&s,s=i(e,o,`rebarLayers`,`Количество слоев должно быть больше 0.`)&&s,s=i(e,o,`rebarReservePercent`,`Запас арматуры должен быть больше 0.`)&&s),d&&(o.formworkHeightM=g(t,`formworkHeightM`),o.formworkReservePercent=g(t,`formworkReservePercent`),s=i(e,o,`formworkHeightM`,`Высота опалубки должна быть больше 0.`)&&s,s=i(e,o,`formworkReservePercent`,`Запас опалубки должен быть больше 0.`)&&s);let p=c(e,t,``,{allowDryReady:!1,includeGravel:!0});return o.mixture=p.mixture,s=p.isValid&&s,{isValid:s,payload:o}}d({calculator:`slab_foundation`,init:b,buildPayload:x,showResult(e,t){v(e,t,e._lastRequestPayload||{})}});