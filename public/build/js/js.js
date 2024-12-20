document.addEventListener("DOMContentLoaded",(function(){eventListeners(),setTimeout((()=>{adjustHistoryDivHeight()}),500);var e=document.getElementsByTagName("header")[0].offsetHeight,t=document.getElementsByTagName("main")[0],o=document.getElementsByTagName("footer")[0];if(o){var i=o.offsetHeight,s=e+i+1;t.style.minHeight="calc(100vh - "+s+"px)",console.log(e,i)}window.addEventListener("resize",(function(){e=document.getElementsByTagName("header")[0].offsetHeight,t=document.getElementsByTagName("main")[0],(o=document.getElementsByTagName("footer")[0])&&(i=o.offsetHeight,s=e+i+1,t.style.minHeight="calc(100vh - "+s+"px)"),setTimeout((()=>{adjustHistoryDivHeight()}),500)}))}));var body=document.getElementsByTagName("body")[0];function eventListeners(){const e=document.querySelector(".login-btn"),t=document.querySelector(".close-btn");e&&(e.addEventListener("click",showLoginMenu),t.addEventListener("click",showLoginMenu))}function adjustHistoryDivHeight(){var e=document.querySelectorAll(".project-div");if(e.length>0){var t=e[0].offsetHeight,o=document.getElementById("historyDiv");o&&(o.style.height=t+"px",o.style.visibility="visible")}}function showLoginMenu(){document.querySelector(".login-popup-container").classList.toggle("show")}function showProjectButtons(e){var t=e;t.classList.toggle("show");var o=t.querySelector(".project-buttons-div"),i=t.querySelectorAll(".project-buttons-div > .project-button"),s=t.querySelectorAll(".gray-line"),n=(t.querySelector(".project-video"),t.offsetHeight),l=o.offsetHeight;t.classList.contains("show")?(window.matchMedia("(max-width: 992px) and (max-height: 550px").matches?i.forEach((e=>{e.style.padding="10px 8px"})):i.forEach((e=>{e.style.padding="15px 10px"})),window.matchMedia("(max-width: 992px) and (max-height: 550px").matches?s.forEach((e=>{e.style.height="15px",e.style.transform="scaleY(1)"})):s.forEach((e=>{e.style.height="30px",e.style.transform="scaleY(1)"})),null!==o&&(window.matchMedia("(max-width: 992px) and (max-height: 550px").matches?o.style.gap="1rem":o.style.gap="2rem",n=t.offsetHeight,l=o.offsetHeight,l+=125,t.style.marginBottom=l+"px",o.style.top=n+"px")):(null!==o&&setTimeout((function(){t.style.marginBottom="",o.style.top="",o.style.gap="0rem"}),300),setTimeout((function(){i.forEach((e=>{e.style.padding=""})),s.forEach((e=>{e.style.height="0px",e.style.transform="scaleY(0)"}))}),300))}function showProjectVideo(e,t){if(e&&e.stopPropagation(),t){var o=t.closest(".project-div").nextElementSibling;if(console.log(o),o){var i=o.querySelector(".background-video"),s=o.querySelector(".project-video"),n=o.querySelector(".close-modal-button");i.classList.toggle("show-video"),s&&(i.addEventListener("click",(e=>l(e,i))),n.addEventListener("click",(e=>l(e,n))),i.classList.contains("show-video")?setTimeout((()=>{o.style.display="",o.style.visibility="visible",i.style.display="block",s.style.opacity="1",s.style.visibility="visible",n.style.transform="scale(1)",body.style.overflow="hidden"}),300):(o.style.display="none",o.style.visibility="hidden",i.style.display="none",s.style.opacity="0",s.style.visibility="hidden",n.style.transform="scale(0)",body.style.overflow="auto",s.pause()))}}function l(e,t){e.stopPropagation();var o=t.closest("#video-div"),i=o.querySelector(".background-video"),s=o.querySelector(".project-video"),n=o.querySelector(".close-modal-button");i.classList.remove("show-video"),i.classList.contains("show-video")?setTimeout((()=>{o.style.visibility="visible",i.style.display="block",s.style.opacity="1",s.style.visibility="visible",n.style.transform="scale(1)",body.style.overflow="hidden"}),300):(setTimeout((()=>{o.style.display="none",o.style.visibility="hidden",i.style.display="none",s.style.visibility="hidden"}),500),s.style.opacity="0",n.style.transform="scale(0)",body.style.overflow="auto",s.pause())}}function showFileButtons(e){var t=e.closest(".button-container"),o=e.nextElementSibling,i=o.querySelectorAll(".file-button"),s=e;setTimeout((()=>{elementButtonHeight=s.offsetHeight}),300),t.classList.toggle("show-button"),t.classList.contains("show-button")?(o.style.marginTop="1.5rem",o.style.gap="1rem",o.style.transition="all 1s linear, gap 0.4s linear",i.forEach((e=>{e.style.width="100%"})),o.style.top="0",setTimeout((()=>{i.forEach((e=>{e.style.height="100px"}))}),300)):(o.style.marginTop="0",o.style.top=`-${elementButtonHeight-2}px`,o.style.transition="all .8s linear, gap .5s linear",console.log(elementButtonHeight),setTimeout((()=>{i.forEach((e=>{e.style.height="0"}))}),300),setTimeout((()=>{o.style.gap="0"}),1e3))}document.addEventListener("DOMContentLoaded",(function(){document.getElementById("updateUserForm")&&document.getElementById("updateUserForm").addEventListener("submit",(function(e){confirm("¿Está seguro de que desea actualizar este usuario?")||e.preventDefault()}))})),document.addEventListener("DOMContentLoaded",(function(){const e=document.getElementById("project_id"),t=document.getElementById("department_id");e.addEventListener("change",(function(){const o=e.value;o?fetch(`/admin/customers/getDepartments?project_id=${o}`).then((e=>e.json())).then((e=>{console.log(e),t.innerHTML='<option value="">Seleccionar Departamento</option>',e.departments.forEach((e=>{const o=document.createElement("option");o.value=e.id,o.textContent=e.department_name,t.appendChild(o)}))})).catch((e=>{console.error("Error fetching departments:",e)})):t.innerHTML='<option value="">Seleccionar Departamento</option>'}))}));