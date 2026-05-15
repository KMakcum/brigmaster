import{S as e,_ as t,a as n,b as r,d as i,g as a,h as o,i as s,l as c,o as l,p as u,r as d,t as f,u as p,v as m,w as h,y as g}from"../shared/bootstrap.js";function _(e,t){let r=a(e)?.querySelector(`[data-result]`);if(!r)return;let i=t?.concrete||{},c=t?.reinforcement||null,l=t?.formwork||null,u=r.querySelector(`[data-result-strip-concrete-length]`),f=r.querySelector(`[data-result-strip-concrete-volume]`),p=r.querySelector(`[data-result-card="strip-reinforcement"]`),m=r.querySelector(`[data-result-card="strip-formwork"]`);if(u&&(u.textContent=h(i.totalLengthM)),f&&(f.textContent=h(i.volumeM3)),s(p,c),m)if(l){m.hidden=!1;let e=m.querySelector(`[data-result-strip-formwork-area]`),t=m.querySelector(`[data-result-strip-formwork-linear]`);e&&(e.textContent=h(l.totalFormworkAreaWithReserveM2)),t&&(t.textContent=h(l.totalFormworkLinearM))}else m.hidden=!0;d(r.querySelector(`[data-result-card="mixture"]`),t?.mixture,`Смесь и материалы`),n(r),r.hidden=!1,r.classList.add(`is-success`),o(e)}function v(e,t,n){let r=t=>String(e.querySelector(`[data-segment-input="${t}"]`)?.value||``).trim(),i={segmentLengthM:r(`segmentLengthM`),segmentWidthM:r(`segmentWidthM`),segmentHeightM:r(`segmentHeightM`)};if(t){let t=e.querySelector(`[data-segment-include-reinforcement]`)?.checked!==!1;if(i.segmentIncludeReinforcement=t,t){let t=e.querySelector(`[data-segment-use-global-rebar]`)?.checked!==!1;i.segmentUseGlobalRebarParams=t,t||(i.segmentLongitudinalBarsCount=r(`segmentLongitudinalBarsCount`),i.segmentLongitudinalDiameterMm=r(`segmentLongitudinalDiameterMm`),i.segmentTransverseDiameterMm=r(`segmentTransverseDiameterMm`),i.segmentTransverseStepMm=r(`segmentTransverseStepMm`))}}if(n){let t=e.querySelector(`[data-segment-include-formwork]`)?.checked!==!1;if(i.segmentIncludeFormwork=t,t){let t=e.querySelector(`[data-segment-use-global-formwork]`)?.checked!==!1;i.segmentUseGlobalFormworkParams=t,t||(i.segmentFormworkHeightM=r(`segmentFormworkHeightM`))}}return i}function y(e){return`
      <article class="brigmaster-estimator__segment-card" data-strip-segment-item data-segment-index="${e}">
        <div class="brigmaster-estimator__segment-head">
          <h3 class="brigmaster-estimator__segment-title">Участок ${e+1}</h3>
          <button type="button" class="brigmaster-estimator__segment-remove" data-strip-remove-segment>Удалить</button>
        </div>
        <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three">
          <div class="brigmaster-estimator__field">
            <label for="segment-${e}-length">Длина участка (м)</label>
            <input id="segment-${e}-length" type="number" min="0.01" step="0.01" value="10" data-segment-input="segmentLengthM">
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentLengthM" data-field-error="segments.${e}.segmentLengthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label for="segment-${e}-width">Ширина участка (м)</label>
            <input id="segment-${e}-width" type="number" min="0.01" step="0.01" value="0.4" data-segment-input="segmentWidthM">
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentWidthM" data-field-error="segments.${e}.segmentWidthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label for="segment-${e}-height">Высота участка (м)</label>
            <input id="segment-${e}-height" type="number" min="0.01" step="0.01" value="1" data-segment-input="segmentHeightM">
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentHeightM" data-field-error="segments.${e}.segmentHeightM" aria-live="polite"></div>
          </div>
        </div>
        <div class="brigmaster-estimator__segment-section" data-segment-rebar-root>
          <div class="brigmaster-estimator__segment-toggles">
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
              <input id="segment-${e}-include-rebar" type="checkbox" checked data-segment-include-reinforcement data-checkbox-key="segment-include-rebar">
              <label for="segment-${e}-include-rebar" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-include-rebar"><span>Учитывать арматуру для этого участка</span></label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentIncludeReinforcement" data-field-error="segments.${e}.segmentIncludeReinforcement" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle brigmaster-estimator__field-group" data-segment-rebar-settings>
              <input id="segment-${e}-use-global-rebar" type="checkbox" checked data-segment-use-global-rebar data-checkbox-key="segment-use-global-rebar">
              <label for="segment-${e}-use-global-rebar" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-use-global-rebar">
                <span>Использовать общие параметры</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: использовать общие параметры арматуры" aria-expanded="false" aria-controls="segment-${e}-use-global-rebar-tooltip">i</button>
                  <div id="segment-${e}-use-global-rebar-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    При включении для этого участка применяются общие настройки арматуры из глобального блока ниже.
                  </div>
                </span>
              </label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentUseGlobalRebarParams" data-field-error="segments.${e}.segmentUseGlobalRebarParams" aria-live="polite"></div>
            </div>
          </div>
          <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-segment-rebar-local>
            <div class="brigmaster-estimator__field">
              <label for="segment-${e}-longitudinal-bars-count" class="brigmaster-estimator__label-row">
                <span>Кол-во продольных стержней</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: количество продольных стержней" aria-expanded="false" aria-controls="segment-${e}-seg-long-bars-tooltip">i</button>
                  <div id="segment-${e}-seg-long-bars-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Число рабочих стержней в сечении этого участка. Обычно 4–6. Больше стержней — выше расход арматуры.
                  </div>
                </span>
              </label>
              <input id="segment-${e}-longitudinal-bars-count" type="number" min="1" step="1" value="4" data-segment-input="segmentLongitudinalBarsCount">
              <p class="brigmaster-estimator__hint">Обычно 4-6 стержней для частного дома.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalBarsCount" data-field-error="segments.${e}.segmentLongitudinalBarsCount" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label for="segment-${e}-longitudinal-diameter" class="brigmaster-estimator__label-row">
                <span>Диаметр продольной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр продольной арматуры" aria-expanded="false" aria-controls="segment-${e}-seg-long-diameter-tooltip">i</button>
                  <div id="segment-${e}-seg-long-diameter-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Диаметр рабочих стержней в мм. Типично 10–14 мм. Чем больше диаметр, тем выше масса и прочность.
                  </div>
                </span>
              </label>
              <input id="segment-${e}-longitudinal-diameter" type="number" min="1" step="1" value="12" data-segment-input="segmentLongitudinalDiameterMm">
              <p class="brigmaster-estimator__hint">Чаще всего 10-14 мм.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalDiameterMm" data-field-error="segments.${e}.segmentLongitudinalDiameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label for="segment-${e}-transverse-diameter" class="brigmaster-estimator__label-row">
                <span>Диаметр поперечной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр поперечной арматуры" aria-expanded="false" aria-controls="segment-${e}-seg-transverse-diameter-tooltip">i</button>
                  <div id="segment-${e}-seg-transverse-diameter-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Диаметр хомутов в мм. Обычно 6–10 мм. Влияет на массу поперечной арматуры.
                  </div>
                </span>
              </label>
              <input id="segment-${e}-transverse-diameter" type="number" min="1" step="1" value="8" data-segment-input="segmentTransverseDiameterMm">
              <p class="brigmaster-estimator__hint">Обычно 6-10 мм для хомутов.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseDiameterMm" data-field-error="segments.${e}.segmentTransverseDiameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label for="segment-${e}-transverse-step" class="brigmaster-estimator__label-row">
                <span>Шаг поперечной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: шаг поперечной арматуры" aria-expanded="false" aria-controls="segment-${e}-seg-transverse-step-tooltip">i</button>
                  <div id="segment-${e}-seg-transverse-step-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Расстояние между хомутами в мм. Типично 200–400 мм. Меньший шаг — больше хомутов и расход стали.
                  </div>
                </span>
              </label>
              <input id="segment-${e}-transverse-step" type="number" min="10" step="10" value="300" data-segment-input="segmentTransverseStepMm">
              <p class="brigmaster-estimator__hint">Меньше шаг = больше хомутов и расход стали.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseStepMm" data-field-error="segments.${e}.segmentTransverseStepMm" aria-live="polite"></div>
            </div>
          </div>
        </div>
        <div class="brigmaster-estimator__segment-section" data-segment-formwork-root>
          <div class="brigmaster-estimator__segment-toggles">
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
              <input id="segment-${e}-include-formwork" type="checkbox" checked data-segment-include-formwork data-checkbox-key="segment-include-formwork">
              <label for="segment-${e}-include-formwork" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-include-formwork"><span>Учитывать опалубку для этого участка</span></label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentIncludeFormwork" data-field-error="segments.${e}.segmentIncludeFormwork" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle brigmaster-estimator__field-group" data-segment-formwork-settings>
              <input id="segment-${e}-use-global-formwork" type="checkbox" checked data-segment-use-global-formwork data-checkbox-key="segment-use-global-formwork">
              <label for="segment-${e}-use-global-formwork" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-use-global-formwork">
                <span>Использовать общие параметры</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: использовать общие параметры опалубки" aria-expanded="false" aria-controls="segment-${e}-use-global-formwork-tooltip">i</button>
                  <div id="segment-${e}-use-global-formwork-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    При включении для этого участка применяются общие настройки опалубки из глобального блока ниже.
                  </div>
                </span>
              </label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentUseGlobalFormworkParams" data-field-error="segments.${e}.segmentUseGlobalFormworkParams" aria-live="polite"></div>
            </div>
          </div>
          <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-segment-formwork-local>
            <div class="brigmaster-estimator__field">
              <label for="segment-${e}-formwork-height">Высота опалубки участка (м)</label>
              <input id="segment-${e}-formwork-height" type="number" min="0.01" step="0.01" value="0.8" data-segment-input="segmentFormworkHeightM">
              <p class="brigmaster-estimator__hint">Считаются только боковые щиты участка.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentFormworkHeightM" data-field-error="segments.${e}.segmentFormworkHeightM" aria-live="polite"></div>
            </div>
          </div>
        </div>
      </article>
    `}function b(t){let n=t.querySelector(`[data-strip-segments-list]`);if(!n)return;let r=n.querySelectorAll(`[data-strip-segment-item]`);r.forEach((t,n)=>{t.dataset.segmentIndex=String(n);let i=t.querySelector(`.brigmaster-estimator__segment-title`);i&&(i.textContent=`Участок ${n+1}`),t.querySelectorAll(`[data-segment-error-field]`).forEach(e=>{let t=e.getAttribute(`data-segment-error-field`);t&&e.setAttribute(`data-field-error`,`segments.${n}.${t}`)});let a=t.querySelector(`[data-strip-remove-segment]`);a&&(a.disabled=r.length===1),[{key:`segment-include-rebar`,id:`segment-${n}-include-rebar`},{key:`segment-use-global-rebar`,id:`segment-${n}-use-global-rebar`},{key:`segment-include-formwork`,id:`segment-${n}-include-formwork`},{key:`segment-use-global-formwork`,id:`segment-${n}-use-global-formwork`}].forEach(({key:e,id:n})=>{let r=t.querySelector(`[data-checkbox-key="${e}"]`),i=t.querySelector(`[data-label-for-checkbox="${e}"]`);r&&(r.id=n),i&&i.setAttribute(`for`,n)}),[{field:`segmentLengthM`,id:`segment-${n}-length`},{field:`segmentWidthM`,id:`segment-${n}-width`},{field:`segmentHeightM`,id:`segment-${n}-height`},{field:`segmentLongitudinalBarsCount`,id:`segment-${n}-longitudinal-bars-count`},{field:`segmentLongitudinalDiameterMm`,id:`segment-${n}-longitudinal-diameter`},{field:`segmentTransverseDiameterMm`,id:`segment-${n}-transverse-diameter`},{field:`segmentTransverseStepMm`,id:`segment-${n}-transverse-step`},{field:`segmentFormworkHeightM`,id:`segment-${n}-formwork-height`}].forEach(({field:e,id:n})=>{let r=t.querySelector(`[data-segment-input="${e}"]`);if(r){r.id=n;let e=r.closest(`.brigmaster-estimator__field`)?.querySelector(`label[for]`);e&&e.setAttribute(`for`,n)}});let o=t.querySelector(`[data-segment-rebar-settings]`),s=t.querySelector(`[data-segment-formwork-settings]`),c=t.querySelector(`[data-segment-rebar-local]`),l=t.querySelector(`[data-segment-formwork-local]`),u=t.querySelector(`[data-segment-use-global-rebar]`),d=t.querySelector(`[data-segment-use-global-formwork]`);n===0?(u&&(u.checked=!0,u.disabled=!0),d&&(d.checked=!0,d.disabled=!0),e(o,!1),e(s,!1),e(c,!1),e(l,!1)):(u&&(u.disabled=!1),d&&(d.disabled=!1))})}function x(t,n,r){let i=t.querySelector(`[data-segment-include-reinforcement]`),a=t.querySelector(`[data-segment-use-global-rebar]`),o=t.querySelector(`[data-segment-rebar-settings]`),s=t.querySelector(`[data-segment-rebar-local]`),c=t.querySelector(`[data-segment-include-formwork]`),l=t.querySelector(`[data-segment-use-global-formwork]`),u=t.querySelector(`[data-segment-formwork-settings]`),d=t.querySelector(`[data-segment-formwork-local]`),f=n&&!!i?.checked;a?.checked;let p=r&&!!c?.checked;l?.checked;let m=t.dataset.segmentIndex===`0`;if(a){let e=m||!n||!f;a.disabled=e,e&&(a.checked=!0)}if(l){let e=m||!r||!p;l.disabled=e,e&&(l.checked=!0)}e(o,!m&&n),e(s,n&&f&&!m&&!a?.checked),e(u,!m&&r),e(d,r&&p&&!m&&!l?.checked)}function S(t){let n=t.querySelector(`[name="calculator"]`)?.value;if(n!==`strip_foundation`&&n!==`pile_foundation`)return;let r=t.querySelector(`[name="mode"]`)?.value||`perimeter`,i=n===`pile_foundation`?!!t.querySelector(`[name="includeGrillage"]`)?.checked:!0,a=i&&!!t.querySelector(`[name="includeReinforcement"]`)?.checked,o=i&&!!t.querySelector(`[name="includeFormwork"]`)?.checked,s=t.querySelector(`[data-field-group="strip-perimeter"]`),c=t.querySelector(`[data-field-group="strip-house"]`),l=t.querySelector(`[data-field-group="strip-segments"]`),u=t.querySelector(`[data-field-group="strip-reinforcement-global"]`),d=t.querySelector(`[data-field-group="strip-formwork-global"]`),f=t.querySelector(`[data-field-group="pile-type"]`),p=t.querySelector(`[data-field-group="pile-primary-row"]`),m=t.querySelector(`[data-pile-primary-grid]`),h=t.querySelector(`[data-pile-primary-cell="shaft-diameter"]`),g=t.querySelector(`[data-pile-primary-cell="shaft-height"]`),_=t.querySelector(`[data-field-group="pile-base-toggle"]`),v=t.querySelector(`[data-field-group="pile-base-fields"]`),y=t.querySelector(`[data-field-group="pile-reinforcement-toggle"]`),b=t.querySelector(`[data-field-group="pile-reinforcement-fields"]`),S=t.querySelector(`[data-toggle-field="strip-reinforcement"]`),C=t.querySelector(`[data-toggle-field="strip-formwork"]`),w=t.querySelector(`[data-field-group="estimator-mode"]`);if(n===`pile_foundation`){let n=!!t.querySelector(`[name="includePiles"]`)?.checked,r=t.querySelector(`[name="pileType"]`)?.value||`bored`,a=!!t.querySelector(`[name="includePileBase"]`)?.checked,o=!!t.querySelector(`[name="includePileReinforcement"]`)?.checked,s=r===`bored`;e(f,n),e(p,n),e(h,n&&s),e(g,n&&s),m&&m.classList.toggle(`is-bored-layout`,n&&s),e(w,i),e(_,n&&s),e(v,n&&s&&a),e(y,n&&s),e(b,n&&s&&o),e(S,i),e(C,i);let c=t.querySelector(`[data-pile-panel="piles"]`),l=t.querySelectorAll(`[data-pile-panel="grillage"]`);c instanceof HTMLDetailsElement&&(c.hidden=!n),l.forEach(e=>{e instanceof HTMLDetailsElement&&(e.hidden=!i)})}e(s,i&&r===`perimeter`),e(c,i&&r===`house`),e(l,i&&r===`segments`),e(u,a),e(d,o),t.querySelectorAll(`[data-strip-segment-item]`).forEach(e=>{x(e,a,o)})}function C(e){let n=e.querySelector(`[name="calculator"]`)?.value;if(n!==`strip_foundation`&&n!==`pile_foundation`)return;let r=e.querySelector(`[name="mode"]`),i=e.querySelector(`[name="includeReinforcement"]`),a=e.querySelector(`[name="includeFormwork"]`),o=e.querySelector(`[name="includePiles"]`),s=e.querySelector(`[name="includeGrillage"]`),c=e.querySelector(`[name="pileType"]`),l=e.querySelector(`[name="includePileBase"]`),d=e.querySelector(`[name="includePileReinforcement"]`),f=e.querySelector(`[data-strip-add-segment]`),p=e.querySelector(`[data-strip-segments-list]`),h=()=>{b(e),S(e)};[r,i,a,o,s,c,l,d].forEach(t=>{t&&t.addEventListener(`change`,()=>{u(e),m(e),h()})}),f&&p&&(f.addEventListener(`click`,()=>{u(e),m(e);let n=p.querySelectorAll(`[data-strip-segment-item]`).length;p.insertAdjacentHTML(`beforeend`,y(n)),h(),t(e)}),p.addEventListener(`click`,t=>{let n=t.target;if(!(n instanceof Element))return;let r=n.closest(`[data-strip-remove-segment]`);if(!r||p.querySelectorAll(`[data-strip-segment-item]`).length<=1)return;let i=r.closest(`[data-strip-segment-item]`);i&&(i.remove(),u(e),m(e),h())}),p.addEventListener(`change`,t=>{let n=t.target;n instanceof Element&&(n.matches(`[data-segment-include-reinforcement]`)||n.matches(`[data-segment-use-global-rebar]`)||n.matches(`[data-segment-include-formwork]`)||n.matches(`[data-segment-use-global-formwork]`))&&(u(e),m(e),h())})),h()}function w(e,t){let n=g(t,`calculator`)||`strip_foundation`,a=g(t,`mode`),o={calculator:n,mode:a},s=p(e,o),u=t.get(`includeReinforcement`)!==null,d=t.get(`includeFormwork`)!==null;if(o.includeReinforcement=u,o.includeFormwork=d,a===`perimeter`)o.totalLengthM=g(t,`totalLengthM`),o.widthM=g(t,`widthM`),o.heightM=g(t,`heightM`),s=i(e,o,`totalLengthM`,`Общая длина ленты должна быть больше 0.`)&&s,s=i(e,o,`widthM`,`Ширина ленты должна быть больше 0.`)&&s,s=i(e,o,`heightM`,`Высота ленты должна быть больше 0.`)&&s;else if(a===`house`)o.houseLengthM=g(t,`houseLengthM`),o.houseWidthM=g(t,`houseWidthM`),o.widthM=String(e.querySelector(`[data-strip-house-width-input]`)?.value||``).trim(),o.heightM=String(e.querySelector(`[data-strip-house-height-input]`)?.value||``).trim(),s=i(e,o,`houseLengthM`,`Длина дома должна быть больше 0.`)&&s,s=i(e,o,`houseWidthM`,`Ширина дома должна быть больше 0.`)&&s,s=i(e,o,`widthM`,`Ширина ленты должна быть больше 0.`)&&s,s=i(e,o,`heightM`,`Высота ленты должна быть больше 0.`)&&s;else if(a===`segments`){let t=e.querySelectorAll(`[data-strip-segment-item]`);o.segments=Array.from(t).map(e=>v(e,u,d)),o.segments.length||(r(e,`segments`,`Добавьте хотя бы один участок.`),s=!1),o.segments.forEach((t,n)=>{let i=`segments.${n}.segmentLengthM`,a=`segments.${n}.segmentWidthM`,o=`segments.${n}.segmentHeightM`;c(t.segmentLengthM)||(r(e,i,`Длина участка должна быть больше 0.`),s=!1),c(t.segmentWidthM)||(r(e,a,`Ширина участка должна быть больше 0.`),s=!1),c(t.segmentHeightM)||(r(e,o,`Высота участка должна быть больше 0.`),s=!1),u&&t.segmentIncludeReinforcement&&t.segmentUseGlobalRebarParams===!1&&(c(t.segmentLongitudinalBarsCount)||(r(e,`segments.${n}.segmentLongitudinalBarsCount`,`Количество продольных стержней должно быть больше 0.`),s=!1),c(t.segmentLongitudinalDiameterMm)||(r(e,`segments.${n}.segmentLongitudinalDiameterMm`,`Диаметр продольной арматуры должен быть больше 0.`),s=!1),c(t.segmentTransverseDiameterMm)||(r(e,`segments.${n}.segmentTransverseDiameterMm`,`Диаметр поперечной арматуры должен быть больше 0.`),s=!1),c(t.segmentTransverseStepMm)||(r(e,`segments.${n}.segmentTransverseStepMm`,`Шаг поперечной арматуры должен быть больше 0.`),s=!1)),d&&t.segmentIncludeFormwork&&t.segmentUseGlobalFormworkParams===!1&&(c(t.segmentFormworkHeightM)||(r(e,`segments.${n}.segmentFormworkHeightM`,`Высота опалубки участка должна быть больше 0.`),s=!1))})}else r(e,`mode`,`Выберите режим perimeter, house или segments.`),s=!1;u&&(o.longitudinalBarsCount=g(t,`longitudinalBarsCount`),o.longitudinalDiameterMm=g(t,`longitudinalDiameterMm`),o.longitudinalReservePercent=g(t,`longitudinalReservePercent`),o.transverseDiameterMm=g(t,`transverseDiameterMm`),o.transverseStepMm=g(t,`transverseStepMm`),o.transverseReservePercent=g(t,`transverseReservePercent`),s=i(e,o,`longitudinalBarsCount`,`Количество продольных стержней должно быть больше 0.`)&&s,s=i(e,o,`longitudinalDiameterMm`,`Диаметр продольной арматуры должен быть больше 0.`)&&s,s=i(e,o,`longitudinalReservePercent`,`Запас продольной арматуры должен быть больше 0.`)&&s,s=i(e,o,`transverseDiameterMm`,`Диаметр поперечной арматуры должен быть больше 0.`)&&s,s=i(e,o,`transverseStepMm`,`Шаг поперечной арматуры должен быть больше 0.`)&&s,s=i(e,o,`transverseReservePercent`,`Запас поперечной арматуры должен быть больше 0.`)&&s),d&&(o.formworkHeightM=g(t,`formworkHeightM`),o.formworkReservePercent=g(t,`formworkReservePercent`),s=i(e,o,`formworkHeightM`,`Высота опалубки должна быть больше 0.`)&&s,s=i(e,o,`formworkReservePercent`,`Запас опалубки должен быть больше 0.`)&&s);let f=l(e,t,``,{allowDryReady:!1,includeGravel:!0});return o.mixture=f.mixture,s=f.isValid&&s,{isValid:s,payload:o}}f({calculator:`strip_foundation`,init:C,buildPayload:w,showResult:_});