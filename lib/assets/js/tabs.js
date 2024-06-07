(function($) {
    $( document ).ready(function() {
        const navTabs = $(".nav-tab");

        navTabs.click(function(e){
            e.preventDefault();
            const navTab = $(this);
            const navWrapper = navTab.parent();

            // reset
            navWrapper.find(".nav-tab").removeClass("nav-tab-active");            
            navWrapper.find("~ .rds-tab-content .rds-tab").addClass("hide");

            // set the active one
            navWrapper.find("~ .rds-tab-content " + $(this).attr("href") ).removeClass("hide");
            navTab.addClass("nav-tab-active");
        })        
        
        navTabs.on("keydown", function(e){
            const navTab = $(this);
            if( e.code == "Space" || e.code == "Enter" ) {
                e.preventDefault();
                navTab.trigger("click");              
            }
            if(e.code == "ArrowLeft") {
                e.preventDefault();
                navTab.prev(".nav-tab").focus();                
            }
            if(e.code == "ArrowRight") {
                e.preventDefault();
                navTab.next(".nav-tab").focus();                
            }
        });

        $(".nav-tab-wrapper").each(function(){
            $(this).find("> .nav-tab").first().click()
        })
    });
})(jQuery);
