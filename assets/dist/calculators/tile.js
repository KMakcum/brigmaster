import{C as e,S as t,T as n,_ as r,a as i,b as a,c as o,d as s,f as c,g as l,h as u,l as d,p as f,t as p,u as m,v as h,w as g,y as _}from"../shared/bootstrap.js";function v(t,r){let a=l(t)?.querySelector(`[data-result]`);if(!a)return;let o=a.querySelector(`[data-result-card="tile-summary"]`),s=a.querySelector(`[data-result-card="tile-layout"]`),c=a.querySelector(`[data-result-card="tile-adhesive"]`),d=a.querySelector(`[data-result-card="tile-grout"]`),f=a.querySelector(`[data-result-card="tile-costs"]`),p=r?.geometry||{},m=r?.tile||{},h=r?.layout||{},_=r?.cutouts||{},v=r?.openings||{},y=r?.adhesive||{},b=r?.grout||{},x=r?.costs||{};if(o){let e=Number(v.count)>0||Number(p.openingsAreaM2)>0?`<p><strong>Проёмы:</strong> ${g(p.openingsAreaM2)} м²</p>`:``,t=Number(_.count)>0||Number(p.cutoutsAreaM2)>0?`<p><strong>Вырезы и отверстия:</strong> ${g(p.cutoutsAreaM2)} м²</p>`:``,n=Number(m.countCutoutWaste)>0?`<p><strong>Потери на вырезы:</strong> ${g(m.countCutoutWaste)} шт</p>`:``;o.innerHTML=`
        <h3>Плитка</h3>
        <p><strong>Общая площадь:</strong> ${g(p.grossAreaM2)} м²</p>
        ${e}
        ${t}
        <p><strong>Чистая площадь:</strong> ${g(p.netAreaM2)} м²</p>
        <p><strong>Плиток без запаса:</strong> ${g(m.countBase)} шт</p>
        ${n}
        <p><strong>Плиток с запасом:</strong> ${g(m.countWithReserve)} шт</p>
        <p><strong>К покупке:</strong> ${g(m.countToBuy)} шт</p>
      `}if(s)if(h.canRender){let t=h.hasNarrowCutWarning?`<p class="brigmaster-estimator__result-note">${e(h.warningText||``)}</p>`:``;s.innerHTML=`
          <h3>Раскладка</h3>
          <p><strong>Плиток по длине:</strong> ${g(h.tilesAlongLength)}</p>
          <p><strong>Рядов:</strong> ${g(h.rowsCount)}</p>
          <p><strong>Остаток по длине:</strong> ${g(h.remainderLengthM)} м</p>
          <p><strong>Остаток по ширине:</strong> ${g(h.remainderWidthM)} м</p>
          <p><strong>Крайняя подрезка по длине:</strong> ${g(h.edgeTrimLengthMm)} мм</p>
          <p><strong>Крайняя подрезка по ширине:</strong> ${g(h.edgeTrimWidthMm)} мм</p>
          ${t}
        `}else s.innerHTML=`
          <h3>Раскладка</h3>
          <p class="brigmaster-estimator__result-note">${e(h.note||`Для ориентировочной раскладки нужны размеры прямоугольной зоны. В режиме по площади показываем только ориентир по материалам.`)}</p>
        `;if(c)if(y.enabled){c.hidden=!1;let e=n(y.costExact)?`<p><strong>Стоимость:</strong> ${g(y.costExact)} / ${g(y.costRounded)} руб</p>`:``;c.innerHTML=`
          <h3>Клей</h3>
          <p><strong>Расход:</strong> ${g(y.requiredKg)} кг</p>
          <p><strong>Нужно мешков:</strong> ${g(y.requiredBags)}</p>
          <p><strong>К покупке:</strong> ${g(y.bagsToBuy)} меш.</p>
          ${e}
        `}else c.hidden=!0;if(d)if(b.enabled){d.hidden=!1;let e=n(b.costExact)?`<p><strong>Стоимость:</strong> ${g(b.costExact)} / ${g(b.costRounded)} руб</p>`:``;d.innerHTML=`
          <h3>Затирка</h3>
          <p><strong>Расход:</strong> ${g(b.requiredKg)} кг</p>
          <p><strong>Нужно упаковок:</strong> ${g(b.requiredPacks)}</p>
          <p><strong>К покупке:</strong> ${g(b.packsToBuy)} уп.</p>
          ${e}
        `}else d.hidden=!0;if(f)if([x.tileCostExact,x.adhesiveCostExact,x.groutCostExact,x.totalExact].some(e=>n(e))){let e=[];n(x.tileCostExact)&&e.push(`<p><strong>Плитка:</strong> ${g(x.tileCostExact)} руб</p>`),n(x.adhesiveCostExact)&&e.push(`<p><strong>Клей:</strong> ${g(x.adhesiveCostExact)} / ${g(x.adhesiveCostRounded)} руб</p>`),n(x.groutCostExact)&&e.push(`<p><strong>Затирка:</strong> ${g(x.groutCostExact)} / ${g(x.groutCostRounded)} руб</p>`),n(x.totalExact)&&e.push(`<p><strong>Итого:</strong> ${g(x.totalExact)} / ${g(x.totalRounded)} руб</p>`),f.hidden=!1,f.innerHTML=`<h3>Стоимость</h3>${e.join(``)}<p class="brigmaster-estimator__result-note">Если есть упаковка, сначала показывается точная оценка, затем ориентир к покупке.</p>`}else f.hidden=!0;i(a),a.hidden=!1,a.classList.add(`is-success`),u(t)}function y(e,t){let n=e.querySelector(`[data-tile-repeat-list="${t}"]`);return n?Array.from(n.querySelectorAll(`[data-tile-repeat-item]`)).map(e=>(e.getAttribute(`data-tile-item-type`)||`opening`)===`cutout`?{shape:String(e.querySelector(`[data-tile-repeat-input="shape"]`)?.value||``).trim()||`circle`,diameterMm:String(e.querySelector(`[data-tile-repeat-input="diameterMm"]`)?.value||``).trim(),widthMm:String(e.querySelector(`[data-tile-repeat-input="widthMm"]`)?.value||``).trim(),heightMm:String(e.querySelector(`[data-tile-repeat-input="heightMm"]`)?.value||``).trim(),count:String(e.querySelector(`[data-tile-repeat-input="count"]`)?.value||``).trim()}:{type:String(e.querySelector(`[data-tile-repeat-input="type"]`)?.value||``).trim(),widthM:String(e.querySelector(`[data-tile-repeat-input="widthM"]`)?.value||``).trim(),heightM:String(e.querySelector(`[data-tile-repeat-input="heightM"]`)?.value||``).trim(),count:String(e.querySelector(`[data-tile-repeat-input="count"]`)?.value||``).trim()}):[]}function b(e,t){let n=!0;return t.forEach((t,r)=>{d(t.widthM)||(a(e,`tileOpenings.${r}.widthM`,`Проём: ширина должна быть больше 0.`),n=!1),d(t.heightM)||(a(e,`tileOpenings.${r}.heightM`,`Проём: высота должна быть больше 0.`),n=!1),o(t.count)||(a(e,`tileOpenings.${r}.count`,`Проём: количество должно быть целым числом больше 0.`),n=!1)}),n}function x(e,t){let n=!0;return t.forEach((t,r)=>{if(o(t.count)||(a(e,`tileCutouts.${r}.count`,`Вырез: количество должно быть целым числом больше 0.`),n=!1),t.shape===`circle`){d(t.diameterMm)||(a(e,`tileCutouts.${r}.diameterMm`,`Вырез: диаметр должен быть больше 0.`),n=!1);return}d(t.widthMm)||(a(e,`tileCutouts.${r}.widthMm`,`Вырез: ширина должна быть больше 0.`),n=!1),d(t.heightMm)||(a(e,`tileCutouts.${r}.heightMm`,`Вырез: высота должна быть больше 0.`),n=!1)}),n}function S(t,n,r){return n===`cutout`?`
        <article class="brigmaster-estimator__segment-card" data-tile-repeat-item data-tile-item-type="cutout" data-tile-group="${e(t)}">
          <div class="brigmaster-estimator__segment-head">
            <h4 class="brigmaster-estimator__segment-title">Вырез или отверстие ${r+1}</h4>
            <button type="button" class="brigmaster-estimator__segment-remove" data-tile-remove-item>Удалить</button>
          </div>
          <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four">
            <div class="brigmaster-estimator__field">
              <label>Что это за элемент</label>
              <select data-tile-repeat-input="shape">
                <option value="circle">Круглое отверстие</option>
                <option value="rect">Прямоугольный вырез</option>
              </select>
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${r}.shape" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field" data-tile-shape-circle>
              <label>Диаметр отверстия (мм)</label>
              <input type="number" min="1" step="1" value="80" data-tile-repeat-input="diameterMm">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${r}.diameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__field-group--hidden" data-tile-shape-rect>
              <label>Ширина выреза (мм)</label>
              <input type="number" min="1" step="1" value="150" data-tile-repeat-input="widthMm">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${r}.widthMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__field-group--hidden" data-tile-shape-rect>
              <label>Высота выреза (мм)</label>
              <input type="number" min="1" step="1" value="150" data-tile-repeat-input="heightMm">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${r}.heightMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label>Количество</label>
              <input type="number" min="1" step="1" value="1" data-tile-repeat-input="count">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${r}.count" aria-live="polite"></div>
            </div>
          </div>
        </article>
      `:`
      <article class="brigmaster-estimator__segment-card" data-tile-repeat-item data-tile-item-type="opening" data-tile-group="${e(t)}">
        <div class="brigmaster-estimator__segment-head">
          <h4 class="brigmaster-estimator__segment-title">Окно или дверь ${r+1}</h4>
          <button type="button" class="brigmaster-estimator__segment-remove" data-tile-remove-item>Удалить</button>
        </div>
        <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four">
          <div class="brigmaster-estimator__field">
            <label>Что вычитаем</label>
            <select data-tile-repeat-input="type">
              <option value="window">Окно</option>
              <option value="door">Дверь</option>
            </select>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Ширина (м)</label>
            <input type="number" min="0.01" step="0.01" value="1.2" data-tile-repeat-input="widthM">
            <div class="brigmaster-estimator__error" data-field-error="tileOpenings.${r}.widthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Высота (м)</label>
            <input type="number" min="0.01" step="0.01" value="1.4" data-tile-repeat-input="heightM">
            <div class="brigmaster-estimator__error" data-field-error="tileOpenings.${r}.heightM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Количество</label>
            <input type="number" min="1" step="1" value="1" data-tile-repeat-input="count">
            <div class="brigmaster-estimator__error" data-field-error="tileOpenings.${r}.count" aria-live="polite"></div>
          </div>
        </div>
      </article>
    `}function C(e){if(!e)return;let t=String(e.querySelector(`[data-tile-repeat-input="shape"]`)?.value||`circle`).trim();e.querySelectorAll(`[data-tile-shape-circle]`).forEach(e=>e.classList.toggle(`brigmaster-estimator__field-group--hidden`,t!==`circle`)),e.querySelectorAll(`[data-tile-shape-rect]`).forEach(e=>e.classList.toggle(`brigmaster-estimator__field-group--hidden`,t!==`rect`))}function w(e){let t=e.getAttribute(`data-tile-repeat-list`)||``,n=e.getAttribute(`data-tile-item-type`)||`opening`,r=e.id||t||`tile-repeat`,i=e.querySelectorAll(`[data-tile-repeat-item]`);i.forEach((e,a)=>{let o=`${r}-${a}`,s=e.querySelector(`.brigmaster-estimator__segment-title`);s&&(s.textContent=`${n===`cutout`?`Вырез или отверстие`:`Окно или дверь`} ${a+1}`);let c=e.querySelector(`[data-tile-remove-item]`);c&&(c.disabled=i.length===1),e.querySelectorAll(`[data-tile-repeat-input]`).forEach(e=>{let n=e.getAttribute(`data-tile-repeat-input`)||`value`,r=`${o}-${n}`;e.id=r,e.name=`${t}[${a}][${n}]`;let i=e.closest(`.brigmaster-estimator__field`)?.querySelector(`label`);i&&i.setAttribute(`for`,r)}),C(n===`cutout`?e:null)})}function T(e){if(e.querySelector(`[name="calculator"]`)?.value!==`tile`)return;let n=e.querySelector(`[name="tileTarget"]`)?.value||`floor`,r=e.querySelector(`[name="mode"]`)?.value||`dimensions`,i=e.querySelector(`[name="tileLayingPattern"]`)?.value||`direct`,a=e.querySelector(`[name="tileIncludeOpenings"]`)?.checked,o=e.querySelector(`[name="tileIncludeCutouts"]`)?.checked,s=e.querySelector(`[name="tileIncludeAdhesive"]`)?.checked,c=e.querySelector(`[name="tileIncludeGrout"]`)?.checked,l=e.querySelector(`[data-tile-thickness-input]`),u=e.querySelector(`[data-tile-reserve-input]`);t(e.querySelector(`[data-field-group="tile-dimensions"]`),r===`dimensions`),t(e.querySelector(`[data-field-group="tile-area"]`),r===`area`),t(e.querySelector(`[data-field-group="tile-wall-height"]`),n===`wall`&&r===`dimensions`),t(e.querySelector(`[data-field-group="tile-openings-toggle"]`),n===`wall`),t(e.querySelector(`[data-tile-openings-root]`),n===`wall`&&a),t(e.querySelector(`[data-tile-cutouts-root]`),!!o),t(e.querySelector(`[data-tile-adhesive-fields]`),!!s),t(e.querySelector(`[data-tile-grout-fields]`),!!c),t(e.querySelector(`[data-field-group="tile-offset"]`),i===`offset`),e.querySelectorAll(`[data-tile-length-label]`).forEach(e=>{e.textContent=n===`wall`?`Длина комнаты (м)`:`Длина помещения (м)`}),e.querySelectorAll(`[data-tile-width-label]`).forEach(e=>{e.textContent=n===`wall`?`Ширина комнаты (м)`:`Ширина помещения (м)`}),l&&!l.dataset.userChanged&&(l.value=n===`wall`?`8`:`9`),u&&!u.dataset.userChanged&&(u.value=i===`diagonal`?`10`:i===`offset`?`7`:`5`)}function E(e){if(e.querySelector(`[name="calculator"]`)?.value!==`tile`)return;let t=e.querySelector(`[name="tileTarget"]`),n=e.querySelector(`[name="mode"]`),i=e.querySelector(`[name="tileLayingPattern"]`),a=e.querySelector(`[name="tileIncludeOpenings"]`),o=e.querySelector(`[name="tileIncludeCutouts"]`),s=e.querySelector(`[name="tileIncludeAdhesive"]`),c=e.querySelector(`[name="tileIncludeGrout"]`);[e.querySelector(`[data-tile-reserve-input]`),e.querySelector(`[data-tile-thickness-input]`)].forEach(e=>{e?.addEventListener(`input`,()=>{e.dataset.userChanged=`1`})});let l=()=>{e.querySelectorAll(`[data-tile-repeat-list]`).forEach(e=>{w(e)}),T(e)};[t,n,i,a,o,s,c].forEach(t=>{t?.addEventListener(`change`,()=>{f(e),h(e),l()})}),e.querySelectorAll(`[data-tile-add-item]`).forEach(t=>{t.addEventListener(`click`,()=>{let n=t.getAttribute(`data-tile-add-item`),i=n?e.querySelector(`[data-tile-repeat-list="${n}"]`):null,a=i?.getAttribute(`data-tile-item-type`)||`opening`;if(!i)return;let o=i.querySelectorAll(`[data-tile-repeat-item]`).length;i.insertAdjacentHTML(`beforeend`,S(n,a,o)),f(e),h(e),l(),r(e)})}),e.querySelectorAll(`[data-tile-repeat-list]`).forEach(t=>{if(!t.querySelector(`[data-tile-repeat-item]`)){let e=t.getAttribute(`data-tile-item-type`)||`opening`,n=t.getAttribute(`data-tile-repeat-list`)||``;t.insertAdjacentHTML(`beforeend`,S(n,e,0))}t.addEventListener(`click`,n=>{let r=n.target;if(!(r instanceof Element))return;let i=r.closest(`[data-tile-remove-item]`);if(!i||t.querySelectorAll(`[data-tile-repeat-item]`).length<=1)return;let a=i.closest(`[data-tile-repeat-item]`);a&&(a.remove(),f(e),h(e),l())}),t.addEventListener(`change`,e=>{let t=e.target;t instanceof Element&&t.matches(`[data-tile-repeat-input="shape"]`)&&C(t.closest(`[data-tile-repeat-item]`))})}),l()}function D(e,t){let n=_(t,`calculator`)||`tile`,r=_(t,`mode`),i={calculator:n,mode:r},a=m(e,i);return i.tileTarget=_(t,`tileTarget`)||`floor`,i.tileLayingPattern=_(t,`tileLayingPattern`)||`direct`,i.length=_(t,`length`),i.width=_(t,`width`),i.height=_(t,`height`),i.area=_(t,`area`),i.tileLengthMm=_(t,`tileLengthMm`),i.tileWidthMm=_(t,`tileWidthMm`),i.tileThicknessMm=_(t,`tileThicknessMm`),i.tileJointMm=_(t,`tileJointMm`),i.tileOffsetPercent=_(t,`tileOffsetPercent`),i.reservePercent=_(t,`reservePercent`),i.tilePricePerM2=_(t,`tilePricePerM2`),i.tileIncludeOpenings=i.tileTarget===`wall`&&t.get(`tileIncludeOpenings`)!==null,i.tileIncludeCutouts=t.get(`tileIncludeCutouts`)!==null,i.tileIncludeAdhesive=t.get(`tileIncludeAdhesive`)!==null,i.tileIncludeGrout=t.get(`tileIncludeGrout`)!==null,a=c(e,`tileTarget`,i.tileTarget,[`floor`,`wall`],`Выберите, что облицовываем.`)&&a,a=c(e,`tileLayingPattern`,i.tileLayingPattern,[`direct`,`offset`,`diagonal`],`Выберите способ укладки.`)&&a,r===`dimensions`?(a=s(e,i,`length`,`Длина должна быть больше 0.`)&&a,a=s(e,i,`width`,`Ширина должна быть больше 0.`)&&a,i.tileTarget===`wall`&&(a=s(e,i,`height`,`Высота стен должна быть больше 0.`)&&a)):a=s(e,i,`area`,`Площадь должна быть больше 0.`)&&a,a=s(e,i,`tileLengthMm`,`Длина плитки должна быть больше 0.`)&&a,a=s(e,i,`tileWidthMm`,`Ширина плитки должна быть больше 0.`)&&a,a=s(e,i,`tileThicknessMm`,`Толщина плитки должна быть больше 0.`)&&a,a=s(e,i,`tileJointMm`,`Ширина шва должна быть больше 0.`)&&a,a=s(e,i,`reservePercent`,`Запас должен быть больше 0.`)&&a,i.tileLayingPattern===`offset`&&(a=s(e,i,`tileOffsetPercent`,`Смещение должно быть больше 0.`)&&a),i.tileTarget===`wall`&&i.tileIncludeOpenings&&(i.tileOpenings=y(e,`tileOpenings`),a=b(e,i.tileOpenings)&&a),i.tileIncludeCutouts&&(i.tileCutouts=y(e,`tileCutouts`),a=x(e,i.tileCutouts)&&a),i.tileIncludeAdhesive&&(i.tileAdhesiveConsumptionKgPerM2=_(t,`tileAdhesiveConsumptionKgPerM2`),i.tileAdhesiveLayerMm=_(t,`tileAdhesiveLayerMm`),i.tileAdhesiveBagWeightKg=_(t,`tileAdhesiveBagWeightKg`),i.tileAdhesiveBagPrice=_(t,`tileAdhesiveBagPrice`),a=s(e,i,`tileAdhesiveConsumptionKgPerM2`,`Расход клея должен быть больше 0.`)&&a,a=s(e,i,`tileAdhesiveLayerMm`,`Толщина слоя клея должна быть больше 0.`)&&a,a=s(e,i,`tileAdhesiveBagWeightKg`,`Вес мешка клея должен быть больше 0.`)&&a),i.tileIncludeGrout&&(i.tileGroutDensityKgPerM3=_(t,`tileGroutDensityKgPerM3`),i.tileGroutPackWeightKg=_(t,`tileGroutPackWeightKg`),i.tileGroutPackPrice=_(t,`tileGroutPackPrice`),a=s(e,i,`tileGroutDensityKgPerM3`,`Плотность затирки должна быть больше 0.`)&&a,a=s(e,i,`tileGroutPackWeightKg`,`Вес упаковки затирки должен быть больше 0.`)&&a),{isValid:a,payload:i}}p({calculator:`tile`,init:E,buildPayload:D,showResult:v});