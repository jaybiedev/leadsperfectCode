var app = angular.module('POSApp', []);

app.controller('POSCtrl', function($scope, $http) {

    var vm = this;

    vm.Summary = {item_count: 0, total_amount_due:0.00, total_amount:0.00, total_tax: 0.00, total_discount: 0.00};
    vm.Items = [];
    vm.Item = {searchkey:null, quantity:0.00, price:0.00, amount:0.00, percent_discount:0.00, discount_amount:0.00, discount_total:0.00};
    vm.Local = {is_processing:false, loader_message: 'Processing...', connection_status: 'ONLINE'};
    vm.User = {is_logged:true, 'name':'Testuser'};

    vm.UserIsLogged = function() {
        return vm.User.is_logged;
    }

    vm.Userget = function(field) {
        return vm.User[field];
    }

    vm.Search = function() {

        vm.Local.is_processing = true;
        var searchkey_split = vm.Item.searchkey.split("*");
        var quantity =  1;
        var searchkey = '';

        if (searchkey_split.length > 1) {
            searchkey = searchkey_split[1];
            if (isNaN(searchkey_split[0]) == false)
                quantity = searchkey_split[0];
        }
        else {
            searchkey = searchkey_split[0];
        }


        $http({
            method : "POST",
            url : "/mylab/pos/controller/search.php",
            params : {
               searchkey: searchkey,
                quantity: quantity
            }
        }).then(function(response) {

            //First function handles success
            if (response.data.success) {

                if (response.data.found || response.data.item == null) {
                    Tools_Dialog.Alert("Item not found.");
                }
                else {
                    // add mapper
                    var item = {
                        code: response.data.item.code,
                        name: response.data.item.name,
                        quantity: response.data.item.quantity,
                        price: response.data.item.price,
                        discout_amount: response.data.item.discount_amount, // amount discount of the item
                        discount_percent: response.data.item.discount_percent, // percent discount of the item
                        discount:  response.data.item.discount, // total discount for the item
                        amount: response.data.item.amount
                    }

                    if (!item.quantity)
                        item.quantity = quantity;

                    if (typeof item.amount == "undefined" || item.amount === null || item.amount === '')
                        item.amount = parseFloat(item.price) * quantity;

                    vm.Items.push(item);
                    vm.Item.searchkey = '';
                    vm.Summarize();
                }
            }
            else {
                // failed
            }

            vm.Local.is_processing = false;

        }, function(response) {
            //Second function handles error
            // "Something went wrong";
            vm.Local.is_processing = false;

        });
    }

    vm.Select = function(item) {
        
    }

    vm.Summarize = function() {
        vm.Summary = {total_amount_due:0.00, total_amount:0.00, total_tax: 0.00, total_discount: 0.00};

        angular.forEach(vm.Items, function(item, key) {
            vm.Summary.total_amount += item.amount || 0.00;
            vm.Summary.total_tax += item.tax || 0.00;
            vm.Summary.total_discount += item.discount || 0.00;
        });

        vm.Summary.item_count = vm.Items.length;
        vm.Summary.total_amount_due += vm.Summary.total_amount + vm.Summary.total_tax - vm.Summary.total_discount;

        return;
    }
});