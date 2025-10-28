$(document).ready(function () {
    let currentLocation = window.location.href;
    let allLinks = $('.navigation').children();
    // loop for main links
    for (let i = 0; i < allLinks?.length; i++) {

        if ($(allLinks[i]).hasClass('has-sub')) {
            let subItems = $(allLinks[i]).children()[1].children;
            // loop for sub links
            for (let j = 0; j < subItems?.length; j++) {
                if (subItems[j].children?.length === 2) {
                    let deepSubItems = subItems[j].children[1]
                    let finalSubItems = $(deepSubItems).children();
                    // loop for deep sub links
                    for (let k = 0; k < finalSubItems?.length; k++) {
                        if (finalSubItems[k].children[0]?.href === currentLocation) {
                        
                            $(finalSubItems[k]).addClass('active')
                        }
                    }
                }
                if (subItems[j].children[0]?.href === currentLocation) {
                    $(subItems[j]).addClass('active')
                }
            }
        }
        if ($(allLinks[i]).children()[0]?.href === currentLocation) {
            $(allLinks[i]).addClass('active')
        }
    }
})
