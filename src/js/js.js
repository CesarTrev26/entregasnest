document.addEventListener('DOMContentLoaded', function() {
    
    eventListeners();
    
    setTimeout(() => {
        adjustHistoryDivHeight();
    }, 500);

    //Put footer at bottom
    var headerHeight = document.getElementsByTagName("header")[0].offsetHeight;
    var mainElem = document.getElementsByTagName("main")[0];
    var footerElem = document.getElementsByTagName("footer")[0]
    
    if(footerElem) {
        var footerHeight = footerElem.offsetHeight;
        var mainOffset = headerHeight + footerHeight + 1;
        mainElem.style.minHeight = "calc(100vh - " + mainOffset + "px)";
        //console.log(headerHeight, footerHeight)
    }
    window.addEventListener('resize', function() {
        headerHeight = document.getElementsByTagName("header")[0].offsetHeight;
        mainElem = document.getElementsByTagName("main")[0];
        footerElem = document.getElementsByTagName("footer")[0];

        if(footerElem) {
            footerHeight = footerElem.offsetHeight;
            mainOffset = headerHeight + footerHeight + 1;
            mainElem.style.minHeight = "calc(100vh - " + mainOffset + "px)";
            //console.log(headerHeight, footerHeight)
        }

        setTimeout(() => {
           adjustHistoryDivHeight(); 
        }, 500);
    });

});

var body = document.getElementsByTagName("body")[0];

    function eventListeners() {
        const loginBtn = document.querySelector('.login-btn');
        const closeBtn = document.querySelector('.close-btn');

        if(loginBtn) {
            loginBtn.addEventListener('click', showLoginMenu);
            closeBtn.addEventListener('click', showLoginMenu);
        }
    }

    function adjustHistoryDivHeight() {
        var projectDiv = document.querySelectorAll('.project-div');
    
        // Ensure there are projectDiv elements
        if (projectDiv.length > 0) {
            
            var projectDivHeight = projectDiv[0].offsetHeight;
            var historyDiv = document.getElementById("historyDiv");
            
            // Check if historyDiv exists
            if (historyDiv) {
                historyDiv.style.height = projectDivHeight + "px";
                historyDiv.style.visibility = "visible"
            } /*else { console.error("Element with id 'historyDiv' not found."); }*/

        //console.log(projectDivHeight);
        } /*else { console.error("No elements found with class 'project-div'."); }*/
    }
        
    function showLoginMenu() {
        const loginMenu = document.querySelector('.login-popup-container')

        loginMenu.classList.toggle('show');
    }

    function showProjectButtons(element) {
        var projectDiv = element;
        projectDiv.classList.toggle('show'); // Toggle the 'show' class    
        var projectButtonsDiv = projectDiv.querySelector('.project-buttons-div'); // Select relative to the clicked element
        var projectButtons = projectDiv.querySelectorAll('.project-buttons-div > .project-button');
        var buttonsDivisor = projectDiv.querySelectorAll('.gray-line');
        var projectVideo = projectDiv.querySelector('.project-video');
        var projectDivHeight = projectDiv.offsetHeight;
        var buttonsheight = projectButtonsDiv.offsetHeight;
    
        // Apply or remove styles based on the presence of the 'show' class
        if (projectDiv.classList.contains('show')) {
            
            if(window.matchMedia("(max-width: 992px) and (max-height: 550px").matches) {
                projectButtons.forEach(el => {
                    el.style.padding = "10px 8px";
                });
            }
            else {
                    projectButtons.forEach(el => {
                        el.style.padding = "15px 10px";
                    });
                }

            if(window.matchMedia("(max-width: 992px) and (max-height: 550px").matches) { 
                buttonsDivisor.forEach(el => {
                    el.style.height = "15px";
                    el.style.transform = "scaleY(1)";
                })
            }
            else {
                buttonsDivisor.forEach(el => {
                    el.style.height = "30px";
                    el.style.transform = "scaleY(1)";
                });
            }

            if (projectButtonsDiv !== null) {

                if(window.matchMedia("(max-width: 992px) and (max-height: 550px").matches) { 
                    projectButtonsDiv.style.gap = "1rem";
                }
                else {
                    projectButtonsDiv.style.gap = "2rem";
                }

                projectDivHeight = projectDiv.offsetHeight;
                buttonsheight = projectButtonsDiv.offsetHeight;
                buttonsheight += 125;   
                projectDiv.style.marginBottom = buttonsheight + "px";
                projectButtonsDiv.style.top = projectDivHeight + "px";
            }
        } 
        else {
            if (projectButtonsDiv !== null) {
                setTimeout(function() {
                    projectDiv.style.marginBottom = '';
                    projectButtonsDiv.style.top = '';
                    projectButtonsDiv.style.gap = "0rem";
                }, 300);
                
            }
            // Reset padding after removing 'show' class
            setTimeout(function() {
                projectButtons.forEach(el => {
                    el.style.padding = '';
                });
                buttonsDivisor.forEach(el => {
                    el.style.height = "0px";
                    el.style.transform = "scaleY(0)";
                });
            }, 300);
        }
    }
    
    function showProjectVideo(event, element) {
        if (event) {
            event.stopPropagation();
        }
        if (element) {
            // Find the closest project-div first
            var projectDiv = element.closest('.project-div');
            // Then find the sibling project-video-div
            var videoDiv = projectDiv.nextElementSibling;
            
            console.log(videoDiv); // Ensure videoDiv is being found
            if (videoDiv) {
                var videoelem = videoDiv.querySelector('.project-video');
                
                var closeBtn = videoDiv.querySelector('.close-modal-button');
                /*
                var screenHeight = window.innerHeight;
                var videoHeight = videoelem.offsetHeight;
                var translateY = (screenHeight - videoHeight) / 2;
                //console.log(videoelem); Ensure videoelem is being found
                console.log(videoHeight);
                */
                videoDiv.classList.toggle('show-video');
                
                
    
                if (videoelem) {
                    videoDiv.addEventListener("click", (event) => hideVideo(event, videoDiv));
                    closeBtn.addEventListener("click", (event) => hideVideo(event, closeBtn));
                    if(videoDiv.classList.contains('show-video')) {
                        setTimeout(() => {
                            videoDiv.style.visibility = "visible";
                            videoelem.style.transform = "scale(1)";
                            closeBtn.style.transform = "scale(1)";
                            body.style.overflow = "hidden";
                        }, 300);
                    } else {
                        videoDiv.style.visibility = "hidden";
                        videoelem.style.transform = "scale(0)";
                        closeBtn.style.transform = "scale(0)";
                        body.style.overflow = "auto";
                        videoelem.pause();
                    }
                }
            }
        }

        function hideVideo(event, element) {

            event.stopPropagation();
            //console.log(element)
            
            var videoDiv = element.closest('#video-div');
            var videoelem = videoDiv.querySelector(".project-video");
            var closeBtn = videoDiv.querySelector('.close-modal-button');
            videoDiv.classList.remove('show-video');
            
            if (videoDiv.classList.contains('show-video')) {
                setTimeout(() => {
                    videoDiv.style.visibility = "visible";
                    videoelem.style.transform = "scale(1)";
                    closeBtn.style.transform = "scale(1)";
                    body.style.overflow = "hidden";
                }, 300);
            } else {
                setTimeout(() => {
                    videoDiv.style.visibility = "hidden";
                }, 500);
                videoelem.style.transform = "scale(0)";
                closeBtn.style.transform = "scale(0)";
                body.style.overflow = "auto";
                videoelem.pause();
            }
        }
    }

    function showServicesButtons(element) {
        
        var servicesContainer = element.closest('.services-container');
        var servicesDiv = element.nextElementSibling;
        var servicesButtons = servicesDiv.querySelectorAll(".services-button");
        var elementButton = element;
        setTimeout(() => {
            elementButtonHeight = elementButton.offsetHeight;
        }, 300)
        /*
        console.log("Enseñando botones de instalaciones...");
        console.log(servicesContainer)
        console.log(servicesDiv);
        console.log(servicesButtons);
        */
        servicesContainer.classList.toggle("show-services");

        if(servicesContainer.classList.contains("show-services")) {
            servicesDiv.style.marginTop = "1.5rem";
            servicesDiv.style.gap = "1rem";
            servicesDiv.style.transition = "all 1s linear, gap 0.4s linear";
            
            servicesButtons.forEach(el => {
                el.style.width = "100%";
            });
            servicesDiv.style.top = "0";
            setTimeout(() => {
               
                servicesButtons.forEach(el => {
                    el.style.height = "100px";
                });
            }, 300);
            
            
        } else {
            servicesDiv.style.marginTop = "0";
            
            servicesDiv.style.top = `-${elementButtonHeight - 2}px`;
            servicesDiv.style.transition = "all .8s linear, gap .5s linear";
            console.log(elementButtonHeight);
            setTimeout(() => {
                servicesButtons.forEach(el => {
                    el.style.height = "0";
                    
                });
            },300);
            setTimeout(() => {
                servicesDiv.style.gap = "0";
            }, 1000);
        }
    }
    



    




    