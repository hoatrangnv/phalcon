var FormDatePicker = function () {
    //function to initiate bootstrap-datepicker
    var runDatePicker = function () {
        $('.date-picker').datepicker({
            autoclose: true
        });
    };
    return {
        //main function to initiate template pages
        init: function () {
            runDatePicker();
        }
    };
}();