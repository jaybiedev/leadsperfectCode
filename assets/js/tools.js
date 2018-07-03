// static objects

$(document).ready(function() {
    Global_Window.Init();
})


var Global_Window = {

    Init : function() {
        var hWindow = $(window).height();
        var hHeader = 50;
        var hSearchbar = 35;
        var hFooterSummary = parseInt($('div#items-summary.footer .sub').css('height'), 10);
        var hPad = 50; // footer summary fixed 25px bottom + 20px padding

        var hItemsTable = hWindow - (hHeader + hSearchbar + hFooterSummary  + hPad);

        $('div#items-table').css('height', hItemsTable + 'px');


    }
}

var Tools_Dialog = {

    Alert : function(message, callback) {
        bootbox.alert(message, callback);
    }
}